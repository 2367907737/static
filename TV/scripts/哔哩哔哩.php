<?php
// B站视频爬虫 - 简洁可用版（移除search相关代码）
header('Content-Type: application/json; charset=utf-8');

class BiliBiliSpider {
    private $extendDict = [];
    private $cookie = [];
    private $header = [
        "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.54 Safari/537.36",
        "Referer" => "https://www.bilibili.com"
    ];
    
    public function __construct() {
        $this->extendDict = $this->getExtendDict();
        $this->cookie = $this->getCookie();
    }
    
    private function getExtendDict() {
        return [
            'cookie' => $this->getConfigCookie(),
            'thread' => '0'
        ];
    }
    
    private function getConfigCookie() {
        // 配置您的B站Cookie
        return '';
    }
    
    private function getCookie() {
        $cookie = $this->extendDict['cookie'] ?? '';
        if (empty($cookie)) return [];
        
        $cookies = [];
        $pairs = explode(';', $cookie);
        foreach ($pairs as $pair) {
            $pair = trim($pair);
            if (strpos($pair, '=') !== false) {
                list($name, $value) = explode('=', $pair, 2);
                $cookies[trim($name)] = trim($value);
            }
        }
        return $cookies;
    }
    
    private function httpRequest($url, $params = []) {
        $ch = curl_init();
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        $headers = [];
        foreach ($this->header as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_COOKIE => $this->buildCookieString(),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true) ?: [];
    }
    
    private function buildCookieString() {
        $pairs = [];
        foreach ($this->cookie as $name => $value) {
            $pairs[] = $name . '=' . $value;
        }
        return implode('; ', $pairs);
    }
    
    // homeContent - 首页分类
    public function homeContent() {
        $classes = [
            ["type_id" => "动态漫画", "type_name" => "动态漫画"],
            ["type_id" => "日番", "type_name" => "日番"],
            ["type_id" => "英文歌曲", "type_name" => "英文歌曲"],
            ["type_id" => "日语歌曲", "type_name" => "日语歌曲"],
            ["type_id" => "韩语歌曲", "type_name" => "韩语歌曲"],
            ["type_id" => "hiphop舞蹈", "type_name" => "hiphop舞蹈"],
            ["type_id" => "国外旅行", "type_name" => "国外旅行"],
            ["type_id" => "日韩综艺", "type_name" => "日韩综艺"]
        ];
        
        return ['class' => $classes];
    }
    
    // homeVideoContent - 首页推荐视频
    public function homeVideoContent() {
        $url = 'https://api.bilibili.com/x/web-interface/popular';
        $data = $this->httpRequest($url, ['ps' => 20, 'pn' => 1]);
        
        $videos = [];
        if (isset($data['data']['list'])) {
            foreach ($data['data']['list'] as $item) {
                $videos[] = [
                    'vod_id' => $item['aid'],
                    'vod_name' => strip_tags($item['title']),
                    'vod_pic' => $item['pic'],
                    'vod_remarks' => $this->formatDuration($item['duration'])
                ];
            }
        }
        
        return ['list' => $videos];
    }
    
    // categoryContent - 分类内容（使用搜索API）
    public function categoryContent($tid, $page, $filters = []) {
        $page = max(1, intval($page));
        
        $url = 'https://api.bilibili.com/x/web-interface/search/type';
        $params = [
            'search_type' => 'video',
            'keyword' => $tid,
            'page' => $page
        ];
        
        $data = $this->httpRequest($url, $params);
        
        $videos = [];
        if (isset($data['data']['result'])) {
            foreach ($data['data']['result'] as $item) {
                if ($item['type'] !== 'video') continue;
                
                $videos[] = [
                    'vod_id' => $item['aid'],
                    'vod_name' => strip_tags($item['title']),
                    'vod_pic' => 'https:' . $item['pic'],
                    'vod_remarks' => $this->formatSearchDuration($item['duration'])
                ];
            }
        }
        
        $pageCount = $data['data']['numPages'] ?? 1;
        $total = $data['data']['numResults'] ?? count($videos);
        
        return [
            'list' => $videos,
            'page' => $page,
            'pagecount' => $pageCount,
            'limit' => 20,
            'total' => $total
        ];
    }
    
    // detailContent - 视频详情
    public function detailContent($vid) {
        $url = 'https://api.bilibili.com/x/web-interface/view';
        $data = $this->httpRequest($url, ['aid' => $vid]);
        
        if (!isset($data['data'])) {
            return ['list' => []];
        }
        
        $video = $data['data'];
        
        // 构建播放列表
        $playUrl = '';
        foreach ($video['pages'] as $index => $page) {
            $part = $page['part'] ?: '第' . ($index + 1) . '集';
            $duration = $this->formatDuration($page['duration']);
            $playUrl .= "{$part}\${$vid}_{$page['cid']}#";
        }
        
        $vod = [
            "vod_id" => $vid,
            "vod_name" => strip_tags($video['title']),
            "vod_pic" => $video['pic'],
            "vod_content" => $video['desc'],
            "vod_play_from" => "B站视频",
            "vod_play_url" => rtrim($playUrl, '#')
        ];
        
        return ['list' => [$vod]];
    }
    
    // playContent - 播放地址（高清优化）
    public function playContent($vid) {
        if (strpos($vid, '_') !== false) {
            list($avid, $cid) = explode('_', $vid);
        } else {
            return $this->errorResponse('无效的视频ID格式');
        }
        
        // 使用高质量参数
        $url = 'https://api.bilibili.com/x/player/playurl';
        $params = [
            'avid' => $avid,
            'cid' => $cid,
            'qn' => 80, // 原画质量
            'fnval' => 0,
        ];
        
        $data = $this->httpRequest($url, $params);
        
        if (!isset($data['data']) || $data['code'] !== 0) {
            return $this->errorResponse('获取播放地址失败');
        }
        
        // 直接返回第一个播放地址
        if (isset($data['data']['durl'][0]['url'])) {
            $playUrl = $data['data']['durl'][0]['url'];
            
            $headers = $this->header;
            $headers['Referer'] = 'https://www.bilibili.com/video/av' . $avid;
            $headers['Origin'] = 'https://www.bilibili.com';
            
            return [
                'parse' => 0,
                'url' => $playUrl,
                'header' => $headers,
                'danmaku' => "https://api.bilibili.com/x/v1/dm/list.so?oid={$cid}"
            ];
        }
        
        return $this->errorResponse('无法获取播放地址');
    }
    
    // 工具函数
    private function formatDuration($seconds) {
        if ($seconds <= 0) return '00:00';
        $minutes = floor($seconds / 60);
        $secs = $seconds % 60;
        return sprintf('%02d:%02d', $minutes, $secs);
    }
    
    private function formatSearchDuration($duration) {
        $parts = explode(':', $duration);
        if (count($parts) === 2) {
            return $duration;
        }
        return '00:00';
    }
    
    private function errorResponse($message) {
        return [
            'parse' => 0,
            'url' => '',
            'error' => $message
        ];
    }
}

// 获取请求参数
$filter = $_GET['filter'] ?? null;
$ac = $_GET['ac'] ?? null;
$t = $_GET['t'] ?? null;
$pg = $_GET['pg'] ?? '1';
$ids = $_GET['ids'] ?? null;
$wd = $_GET['wd'] ?? null;
$flag = $_GET['flag'] ?? null;
$play = $_GET['play'] ?? null;
$ext = $_GET['ext'] ?? null;

// 解码 ext 参数（Base64 编码的 JSON）
$extData = [];
if ($ext) {
    $extJson = base64_decode($ext);
    if ($extJson) {
        $extData = json_decode($extJson, true) ?: [];
    }
}

$spider = new BiliBiliSpider();

try {
    // ============================================================================
    // 首页/分类接口
    // ============================================================================
    if ($filter !== null) {
        echo json_encode([
            'class' => [
                ['type_id' => '动态漫画', 'type_name' => '动态漫画'],
                ['type_id' => '日番', 'type_name' => '日番'],
                ['type_id' => '日语歌曲', 'type_name' => '日语歌曲'],
                ['type_id' => '韩语歌曲', 'type_name' => '韩语歌曲'],
                ['type_id' => '英文歌曲', 'type_name' => '英文歌曲'],
                ['type_id' => 'hiphop舞蹈', 'type_name' => 'hiphop舞蹈'],
                ['type_id' => '国外旅行', 'type_name' => '国外旅行'],
                ['type_id' => '日韩综艺', 'type_name' => '日韩综艺']
            ],
            'filters' => [
                '1001' => [
                    [
                        'key' => 'sort',
                        'name' => '排序',
                        'value' => [
                            ['n' => '最新', 'v' => 'new'],
                            ['n' => '最热', 'v' => 'hot'],
                            ['n' => '推荐', 'v' => 'recommend']
                        ]
                    ]
                ]
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ============================================================================
    // 首页视频内容
    // ============================================================================
    if ($ac === 'detail' && empty($t) && empty($ids)) {
        $result = $spider->homeContent();
        $videoResult = $spider->homeVideoContent();
        $result['list'] = $videoResult['list'];
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ============================================================================
    // 分类列表接口
    // ============================================================================
    if ($ac === 'detail' && $t !== null) {
        $filters = !empty($extData) ? $extData : [];
        echo json_encode($spider->categoryContent($t, $pg, $filters), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ============================================================================
    // 详情接口
    // ============================================================================
    if ($ac === 'detail' && $ids !== null) {
        echo json_encode($spider->detailContent($ids), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ============================================================================
    // 播放解析接口
    // ============================================================================
    if ($flag !== null && $play !== null) {
        // 解析B站视频地址
        if (strpos($play, 'bilibili.com') !== false) {
            // 对于B站地址，直接返回（TVBox会自行解析）
            echo json_encode([
                'parse' => 0,
                'url' => $play,
                'header' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.54 Safari/537.36',
                    'Referer' => 'https://www.bilibili.com/'
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // 使用spider的playContent方法解析内部格式
            echo json_encode($spider->playContent($play), JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
    
    // ============================================================================
    // 搜索接口（简单实现）
    // ============================================================================
    if ($wd !== null) {
        echo json_encode($spider->categoryContent($wd, $pg), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ============================================================================
    // 默认响应
    // ============================================================================
    echo json_encode([
        'error' => '未知请求',
        'params' => $_GET,
        'info' => [
            'name' => 'B站视频源',
            'version' => '1.0.0',
            'description' => '简洁可用版B站爬虫接口',
            'platform' => 'TVBox/T4',
            'php_version' => PHP_VERSION
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
?>