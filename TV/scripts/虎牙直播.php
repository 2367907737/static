<?php
require_once __DIR__ . '/lib/spider.php';

class HuyaSpider extends BaseSpider
{
    private const TITLE = "虎牙直播";
    private const HOST = "https://www.huya.com";
    private const HOME_URL = "/cache.php?m=LiveList&do=getLiveListByPage&gameId=2168&tagAll=0&page=1";
    private const CATEGORY_URL_TEMPLATE = "/cache.php?m=LiveList&do=getLiveListByPage&gameId=fyfilter&tagAll=0&page=fypage";
    private const ROOM_INFO_URL = "https://mp.huya.com/cache.php?m=Live&do=profileRoom&roomid=";
    private const SEARCH_URL = "https://search.cdn.huya.com/?m=Search&do=getSearchContent&q=**&uid=0&v=4&typ=-5&livestate=0&rows=40&start=0";

    public function init($extend = "")
    {
    }

    public function homeContent($filter)
    {
        $classes = [
            ['type_id' => '8', 'type_name' => '娱乐'],
            ['type_id' => '1', 'type_name' => '网游'],
            ['type_id' => '2', 'type_name' => '单机'],
            ['type_id' => '3', 'type_name' => '手游']
        ];
        
        $filters = $this->getFilters();
        $recommend = $this->getRecommendVideos();
        
        return [
            'class' => $classes,
            'filters' => $filters,
            'list' => $recommend
        ];
    }

    public function categoryContent($tid, $pg = 1, $filter = [], $extend = [])
    {
        $page = max(1, $pg);
        $gameId = $this->getGameIdFromParams($tid, $filter, $extend);
        
        $url = self::HOST . str_replace(
            ['fyfilter', 'fypage'],
            [$gameId, $page],
            self::CATEGORY_URL_TEMPLATE
        );
        
        $html = $this->fetch($url);
        $data = json_decode($html, true);
        
        if (empty($data) || empty($data['data']['datas'])) {
            return $this->pageResult([], $page);
        }
        
        $videos = [];
        foreach ($data['data']['datas'] as $item) {
            $profileRoom = $item['profileRoom'] ?? '';
            $roomId = $this->extractRoomId($profileRoom);
            
            $videos[] = [
                'vod_id' => $roomId,
                'vod_name' => $item['introduction'] ?? '未知标题',
                'vod_pic' => $item['screenshot'] ?? '',
                'vod_remarks' => '👁' . ($item['totalCount'] ?? 0) . '  🆙' . ($item['nick'] ?? '')
            ];
        }
        
        $total = $data['data']['total'] ?? 0;
        $pageSize = 8;
        
        return $this->pageResult($videos, $page, $total, $pageSize);
    }

    private function getGameIdFromParams($tid, $filter, $extend)
    {
        $defaultGameIds = [
            '8' => '2135',
            '1' => '1',
            '2' => '1732',
            '3' => '2336'
        ];
        
        if (!empty($extend) && isset($extend['cateId']) && !empty($extend['cateId'])) {
            return $extend['cateId'];
        }
        
        if (is_string($filter) && !empty($filter)) {
            if (strpos($filter, '{') === 0 || strpos($filter, '[') === 0) {
                $decoded = json_decode($filter, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['cateId'])) {
                    return $decoded['cateId'];
                }
            } else {
                return $filter;
            }
        }
        
        if (is_array($filter) && !empty($filter)) {
            if (isset($filter['cateId']) && !empty($filter['cateId'])) {
                return $filter['cateId'];
            }
        }
        
        return $defaultGameIds[$tid] ?? $tid;
    }

    public function detailContent($ids)
    {
        if (empty($ids) || !is_array($ids)) {
            return ['list' => []];
        }
        
        $roomId = $ids[0];
        if (empty($roomId)) {
            return ['list' => []];
        }
        
        $roomInfo = $this->getRoomInfo($roomId);
        
        $vodName = '虎牙直播间';
        $vodPic = '';
        $vodContent = '房间ID: ' . $roomId;
        
        if ($roomInfo && is_array($roomInfo)) {
            $vodName = isset($roomInfo['roomName']) ? (string)$roomInfo['roomName'] : $vodName;
            $vodPic = isset($roomInfo['screenshot']) ? (string)$roomInfo['screenshot'] : $vodPic;
            
            $introduction = isset($roomInfo['introduction']) ? (string)$roomInfo['introduction'] : '';
            $nick = isset($roomInfo['nick']) ? (string)$roomInfo['nick'] : '';
            $vodContent = trim($introduction . "\n主播: " . $nick);
        }
        
        return [
            'list' => [
                [
                    'vod_id' => (string)$roomId,
                    'vod_name' => $vodName,
                    'vod_pic' => $vodPic,
                    'vod_content' => $vodContent,
                    'vod_play_from' => '直播',
                    'vod_play_url' => (string)$roomId
                ]
            ]
        ];
    }

    public function searchContent($key, $quick = false, $pg = 1)
    {
        $page = max(1, $pg);
        $start = ($page - 1) * 40;
        
        $url = str_replace(
            ['**', 'start=0'],
            [urlencode($key), "start={$start}"],
            self::SEARCH_URL
        );
        
        $html = $this->fetch($url);
        $data = json_decode($html, true);
        
        if (empty($data) || empty($data[3]['docs'])) {
            return $this->pageResult([]);
        }
        
        $videos = [];
        foreach ($data[3]['docs'] as $item) {
            $roomId = isset($item['room_id']) ? (string)$item['room_id'] : '';
            
            $videos[] = [
                'vod_id' => $roomId,
                'vod_name' => isset($item['game_roomName']) ? (string)$item['game_roomName'] : '未知标题',
                'vod_pic' => isset($item['game_screenshot']) ? (string)$item['game_screenshot'] : '',
                'vod_remarks' => '主播: ' . (isset($item['game_nick']) ? (string)$item['game_nick'] : '')
            ];
        }
        
        return $this->pageResult($videos, $page);
    }

    public function playerContent($flag, $id, $vipFlags = [])
    {
        $rid = $this->extractRoomId($id);
        
        if (empty($rid)) {
            return ['parse' => 0, 'url' => ''];
        }
        
        $apiUrl = self::ROOM_INFO_URL . $rid;
        $response = $this->fetch($apiUrl);
        $data = json_decode($response, true);
        
        if (empty($data['data']['stream']['flv']['multiLine'][0]['url'])) {
            return ['parse' => 0, 'url' => ''];
        }
        
        $purl = $data['data']['stream']['flv']['multiLine'][0]['url'];
        $realUrl = $this->getRealUrl($purl);
        
        return [
            'parse' => 0,
            'jx' => 0,
            'url' => $realUrl,
            'header' => (object)[
                'user-agent' => 'Mozilla/5.0'
            ]
        ];
    }

    private function getRecommendVideos()
    {
        $url = self::HOST . self::HOME_URL;
        $html = $this->fetch($url);
        $data = json_decode($html, true);
        
        $videos = [];
        if (!empty($data['data']['datas'])) {
            foreach ($data['data']['datas'] as $item) {
                $profileRoom = $item['profileRoom'] ?? '';
                $roomId = $this->extractRoomId($profileRoom);
                
                $videos[] = [
                    'vod_id' => $roomId,
                    'vod_name' => $item['introduction'] ?? '未知标题',
                    'vod_pic' => $item['screenshot'] ?? '',
                    'vod_remarks' => '👁' . ($item['totalCount'] ?? 0) . '  🆙' . ($item['nick'] ?? '')
                ];
            }
        }
        
        return $videos;
    }

    private function getRoomInfo($roomId)
    {
        if (empty($roomId)) {
            return false;
        }
        
        $apiUrl = self::ROOM_INFO_URL . $roomId;
        $response = $this->fetch($apiUrl);
        $data = json_decode($response, true);
        
        if (!empty($data['data'])) {
            return $data['data'];
        }
        
        return false;
    }

    private function extractRoomId($url)
    {
        if (empty($url)) {
            return '';
        }
        
        if (is_numeric($url)) {
            return (string)$url;
        }
        
        preg_match('/(\d+)/', $url, $matches);
        
        if (!empty($matches[1])) {
            return (string)$matches[1];
        }
        
        return (string)$url;
    }

    private function getFilters()
    {
        // 简化分类数据，只保留主要分类
        return [
            '8' => [
                [
                    'key' => 'cateId',
                    'name' => '分类',
                    'value' => [
                        ['n' => '一起看', 'v' => '2135'],
                        ['n' => '星秀', 'v' => '1663'],
                        ['n' => '户外', 'v' => '2165'],
                        ['n' => '二次元', 'v' => '2633'],
                        ['n' => '颜值', 'v' => '2168']
                    ]
                ]
            ],
            '1' => [
                [
                    'key' => 'cateId',
                    'name' => '分类',
                    'value' => [
                        ['n' => '英雄联盟', 'v' => '1'],
                        ['n' => 'CS2', 'v' => '862'],
                        ['n' => '穿越火线', 'v' => '4'],
                        ['n' => '无畏契约', 'v' => '5937'],
                        ['n' => 'DOTA2', 'v' => '7']
                    ]
                ]
            ],
            '2' => [
                [
                    'key' => 'cateId',
                    'name' => '分类',
                    'value' => [
                        ['n' => '天天吃鸡', 'v' => '2793'],
                        ['n' => '永劫无间', 'v' => '6219'],
                        ['n' => '我的世界', 'v' => '1732'],
                        ['n' => '主机游戏', 'v' => '100032'],
                        ['n' => 'Apex英雄', 'v' => '5011']
                    ]
                ]
            ],
            '3' => [
                [
                    'key' => 'cateId',
                    'name' => '分类',
                    'value' => [
                        ['n' => '王者荣耀', 'v' => '2336'],
                        ['n' => '和平精英', 'v' => '3203'],
                        ['n' => '英雄联盟手游', 'v' => '6203'],
                        ['n' => '原神', 'v' => '5489'],
                        ['n' => '金铲铲之战', 'v' => '7185']
                    ]
                ]
            ]
        ];
    }

    private function getRealUrl($live_url)
    {
        if (empty($live_url)) {
            return '';
        }
        
        $parts = explode('?', $live_url, 2);
        if (count($parts) < 2) {
            return $live_url;
        }
        
        list($i, $b) = $parts;
        $r = basename($i);
        $s = preg_replace('/\.(flv|m3u8)$/', '', $r);
        
        $params = explode('&', $b);
        $params = array_filter($params);
        
        $n = [];
        $c_tmp2 = [];
        
        foreach ($params as $index => $param) {
            if ($index < 3) {
                $pair = explode('=', $param, 2);
                if (count($pair) == 2) {
                    $n[$pair[0]] = $pair[1];
                }
            } else {
                $c_tmp2[] = $param;
            }
        }
        
        $tmp2 = implode('&', $c_tmp2);
        if (!empty($tmp2)) {
            $pair = explode('=', $tmp2, 2);
            if (count($pair) == 2) {
                $n[$pair[0]] = $pair[1];
            }
        }
        
        if (!isset($n['fm'])) {
            return $live_url;
        }
        
        $fm = urldecode($n['fm']);
        $fmParts = explode('&', $fm);
        $fm = $fmParts[0] ?? '';
        
        $u = base64_decode($fm);
        if ($u === false) {
            return $live_url;
        }
        
        $uParts = explode('_', $u);
        $p = $uParts[0] ?? '';
        
        $f = time() . '0000';
        $ll = $n['wsTime'] ?? '';
        $t = '0';
        
        $h = "{$p}_{$t}_{$s}_{$f}_{$ll}";
        $m = md5($h);
        
        $result = $i . '?wsSecret=' . $m . '&wsTime=' . $ll . '&u=' . $t . '&seqid=' . $f;
        
        if (!empty($c_tmp2)) {
            $result .= '&' . end($c_tmp2);
        }
        
        return str_replace(['hls', 'm3u8'], ['flv', 'flv'], $result);
    }
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    (new HuyaSpider())->run();
}