<?php
// B站爬虫模板 - 单一分类无限细分 + 修复播放URL
//
header('Content-Type: application/json; charset=utf-8');

// 使用Apple CMS标准参数
$ac = $_GET['ac'] ?? 'detail';
$t = $_GET['t'] ?? '';
$pg = $_GET['pg'] ?? '1';
$f = $_GET['f'] ?? '';
$ids = $_GET['ids'] ?? '';
$wd = $_GET['wd'] ?? '';
$flag = $_GET['flag'] ?? '';
$id = $_GET['id'] ?? '';

// B站爬虫类
class BiliBiliSpider {
    private $categories = [];
    private $baseUrl = 'https://api.bilibili.com';
    
    public function __construct() {
        $this->initializeCategories();
    }
    
    // 初始化分类 - 只有B站一个一级分类
    private function initializeCategories() {
        // 只有一个一级分类：B站
        $this->categories = [
            '1' => ['name' => 'B站', 'tid' => 0, 'level' => 1, 'has_children' => true],
        ];

        // 初始化B站所有主要分区作为二级分类
        $this->initializeBilibiliCategories();
    }
    
    // 初始化B站所有分类
    private function initializeBilibiliCategories() {
        // B站主要分区
        $mainCategories = [
            ['id' => '1_1', 'name' => '动画', 'tid' => 1],
            ['id' => '1_2', 'name' => '番剧', 'tid' => 13],
            ['id' => '1_3', 'name' => '国创', 'tid' => 167],
            ['id' => '1_4', 'name' => '音乐', 'tid' => 3],
            ['id' => '1_5', 'name' => '舞蹈', 'tid' => 129],
            ['id' => '1_6', 'name' => '游戏', 'tid' => 4],
            ['id' => '1_7', 'name' => '知识', 'tid' => 36],
            ['id' => '1_8', 'name' => '科技', 'tid' => 188],
            ['id' => '1_9', 'name' => '运动', 'tid' => 234],
            ['id' => '1_10', 'name' => '汽车', 'tid' => 223],
            ['id' => '1_11', 'name' => '生活', 'tid' => 160],
            ['id' => '1_12', 'name' => '美食', 'tid' => 211],
            ['id' => '1_13', 'name' => '动物', 'tid' => 217],
            ['id' => '1_14', 'name' => '鬼畜', 'tid' => 119],
            ['id' => '1_15', 'name' => '时尚', 'tid' => 155],
            ['id' => '1_16', 'name' => '娱乐', 'tid' => 5],
            ['id' => '1_17', 'name' => '影视', 'tid' => 181],
            ['id' => '1_18', 'name' => '纪录片', 'tid' => 177],
            ['id' => '1_19', 'name' => '电影', 'tid' => 23],
            ['id' => '1_20', 'name' => '电视剧', 'tid' => 11],
            ['id' => '1_21', 'name' => '热门推荐', 'tid' => 0],
        ];

        foreach ($mainCategories as $cat) {
            $this->categories[$cat['id']] = [
                'name' => $cat['name'],
                'tid' => $cat['tid'],
                'level' => 2,
                'parent' => '1',
                'has_children' => $this->hasSubCategories($cat['tid'])
            ];
        }

        // 初始化所有子分类
        $this->initializeAllSubCategories();
    }
    
    // 检查是否有子分类
    private function hasSubCategories($tid) {
        $hasSubs = [1, 13, 167, 3, 129, 4, 36, 188, 234, 160, 5, 181];
        return in_array($tid, $hasSubs);
    }
    
    // 初始化所有子分类
private function initializeAllSubCategories() {
    // ... 前面已有的分类代码保持不变 ...

    // 动画分区细分 - 修复tid
    $this->addSubCategory('1_1', '1_1_1', 'MAD·AMV', 24);
    $this->addSubCategory('1_1', '1_1_2', 'MMD·3D', 25);
    $this->addSubCategory('1_1', '1_1_3', '短片·手书', 47);
    $this->addSubCategory('1_1', '1_1_4', '综合', 27);
    
    // 番剧分区细分
    $this->addSubCategory('1_2', '1_2_1', '连载动画', 33);
    $this->addSubCategory('1_2', '1_2_2', '完结动画', 32);
    $this->addSubCategory('1_2', '1_2_3', '资讯', 51);
    $this->addSubCategory('1_2', '1_2_4', '官方延伸', 152);
    
    // 国创分区细分
    $this->addSubCategory('1_3', '1_3_1', '国产动画', 153);
    $this->addSubCategory('1_3', '1_3_2', '国产原创', 168);
    $this->addSubCategory('1_3', '1_3_3', '布袋戏', 169);
    $this->addSubCategory('1_3', '1_3_4', '动态漫·广播剧', 195);
    
    // 音乐分区细分
    $this->addSubCategory('1_4', '1_4_1', '原创音乐', 28);
    $this->addSubCategory('1_4', '1_4_2', '翻唱', 31);
    $this->addSubCategory('1_4', '1_4_3', 'VOCALOID·UTAU', 30);
    $this->addSubCategory('1_4', '1_4_4', '演奏', 59);
    $this->addSubCategory('1_4', '1_4_5', 'MV', 193);
    $this->addSubCategory('1_4', '1_4_6', '音乐现场', 29);
    $this->addSubCategory('1_4', '1_4_7', '音乐综合', 130);
    
    // 舞蹈分区细分
    $this->addSubCategory('1_5', '1_5_1', '宅舞', 20);
    $this->addSubCategory('1_5', '1_5_2', '街舞', 198);
    $this->addSubCategory('1_5', '1_5_3', '明星舞蹈', 199);
    $this->addSubCategory('1_5', '1_5_4', '中国舞', 200);
    $this->addSubCategory('1_5', '1_5_5', '舞蹈综合', 154);
    
    // 游戏分区细分
    $this->addSubCategory('1_6', '1_6_1', '单机游戏', 17);
    $this->addSubCategory('1_6', '1_6_2', '电子竞技', 171);
    $this->addSubCategory('1_6', '1_6_3', '手机游戏', 65);
    $this->addSubCategory('1_6', '1_6_4', '网络游戏', 172);
    $this->addSubCategory('1_6', '1_6_5', '桌游棋牌', 173);
    $this->addSubCategory('1_6', '1_6_6', 'GMV', 121);
    $this->addSubCategory('1_6', '1_6_7', '音游', 136);
    $this->addSubCategory('1_6', '1_6_8', 'Mugen', 19);
    
    // 知识分区细分
    $this->addSubCategory('1_7', '1_7_1', '科学科普', 201);
    $this->addSubCategory('1_7', '1_7_2', '社科人文', 124);
    $this->addSubCategory('1_7', '1_7_3', '财经', 207);
    $this->addSubCategory('1_7', '1_7_4', '校园学习', 208);
    $this->addSubCategory('1_7', '1_7_5', '职业职场', 209);
    $this->addSubCategory('1_7', '1_7_6', '野生技术协会', 122);
    
    // 科技分区细分
    $this->addSubCategory('1_8', '1_8_1', '数码', 95);
    $this->addSubCategory('1_8', '1_8_2', '软件应用', 230);
    $this->addSubCategory('1_8', '1_8_3', '计算机技术', 231);
    $this->addSubCategory('1_8', '1_8_4', '工业工程', 232);
    $this->addSubCategory('1_8', '1_8_5', '机械', 233);
    
    // 运动分区细分
    $this->addSubCategory('1_9', '1_9_1', '篮球', 235);
    $this->addSubCategory('1_9', '1_9_2', '足球', 249);
    $this->addSubCategory('1_9', '1_9_3', '健身', 164);
    $this->addSubCategory('1_9', '1_9_4', '竞技体育', 236);
    $this->addSubCategory('1_9', '1_9_5', '运动文化', 237);
    
    // 汽车分区细分
    $this->addSubCategory('1_10', '1_10_1', '汽车生活', 176);
    $this->addSubCategory('1_10', '1_10_2', '汽车文化', 224);
    $this->addSubCategory('1_10', '1_10_3', '汽车极客', 225);
    $this->addSubCategory('1_10', '1_10_4', '智能出行', 226);
    $this->addSubCategory('1_10', '1_10_5', '购车攻略', 227);
    
    // 生活分区细分
    $this->addSubCategory('1_11', '1_11_1', '搞笑', 138);
    $this->addSubCategory('1_11', '1_11_2', '日常', 21);
    $this->addSubCategory('1_11', '1_11_3', '手工', 161);
    $this->addSubCategory('1_11', '1_11_4', '绘画', 162);
    $this->addSubCategory('1_11', '1_11_5', '运动', 163);
    $this->addSubCategory('1_11', '1_11_6', '其他', 174);
    
    // 美食分区细分
    $this->addSubCategory('1_12', '1_12_1', '美食制作', 76);
    $this->addSubCategory('1_12', '1_12_2', '美食侦探', 212);
    $this->addSubCategory('1_12', '1_12_3', '美食测评', 213);
    $this->addSubCategory('1_12', '1_12_4', '田园美食', 214);
    $this->addSubCategory('1_12', '1_12_5', '美食记录', 215);
    
    // 动物分区细分
    $this->addSubCategory('1_13', '1_13_1', '喵星人', 218);
    $this->addSubCategory('1_13', '1_13_2', '汪星人', 219);
    $this->addSubCategory('1_13', '1_13_3', '大熊猫', 220);
    $this->addSubCategory('1_13', '1_13_4', '野生动物', 221);
    $this->addSubCategory('1_13', '1_13_5', '爬宠', 222);
    $this->addSubCategory('1_13', '1_13_6', '动物综合', 75);
    
    // 鬼畜分区细分
    $this->addSubCategory('1_14', '1_14_1', '鬼畜调教', 22);
    $this->addSubCategory('1_14', '1_14_2', '音MAD', 26);
    $this->addSubCategory('1_14', '1_14_3', '鬼畜剧场', 126);
    $this->addSubCategory('1_14', '1_14_4', '教程演示', 127);
    
    // 时尚分区细分
    $this->addSubCategory('1_15', '1_15_1', '美妆', 157);
    $this->addSubCategory('1_15', '1_15_2', '服饰', 158);
    $this->addSubCategory('1_15', '1_15_3', '时尚潮流', 159);
    $this->addSubCategory('1_15', '1_15_4', '仿妆', 192);
    $this->addSubCategory('1_15', '1_15_5', '穿搭', 189);
    
    // 娱乐分区细分
    $this->addSubCategory('1_16', '1_16_1', '综艺', 71);
    $this->addSubCategory('1_16', '1_16_2', '明星', 137);
    $this->addSubCategory('1_16', '1_16_3', 'Korea相关', 131);
    
    // 影视分区细分
    $this->addSubCategory('1_17', '1_17_1', '影视杂谈', 182);
    $this->addSubCategory('1_17', '1_17_2', '影视剪辑', 183);
    $this->addSubCategory('1_17', '1_17_3', '短片', 85);
    $this->addSubCategory('1_17', '1_17_4', '预告·资讯', 184);
    
    // 纪录片分区细分
    $this->addSubCategory('1_18', '1_18_1', '人文历史', 37);
    $this->addSubCategory('1_18', '1_18_2', '科学探索', 178);
    $this->addSubCategory('1_18', '1_18_3', '社会纪实', 179);
    $this->addSubCategory('1_18', '1_18_4', '自然生态', 180);
    $this->addSubCategory('1_18', '1_18_5', '旅行纪录片', 196);
    
    // 电影分区细分
    $this->addSubCategory('1_19', '1_19_1', '华语电影', 147);
    $this->addSubCategory('1_19', '1_19_2', '欧美电影', 145);
    $this->addSubCategory('1_19', '1_19_3', '日本电影', 146);
    $this->addSubCategory('1_19', '1_19_4', '韩国电影', 83);
    $this->addSubCategory('1_19', '1_19_5', '其他地区', 82);
    
    // 电视剧分区细分
    $this->addSubCategory('1_20', '1_20_1', '国产剧', 185);
    $this->addSubCategory('1_20', '1_20_2', '海外剧', 187);
        // 可以继续添加更多细分分类...
    }
    
    // 添加子分类
    private function addSubCategory($parentId, $id, $name, $tid) {
        $this->categories[$id] = [
            'name' => $name,
            'tid' => $tid,
            'level' => $this->categories[$parentId]['level'] + 1,
            'parent' => $parentId,
            'has_children' => false // 默认没有更深层的子分类
        ];
    }
    
    // 获取分类信息
    public function getCategory($categoryId) {
        return $this->categories[$categoryId] ?? null;
    }
    
    // 获取子分类
    public function getChildren($parentId) {
        $children = [];
        foreach ($this->categories as $id => $category) {
            if (isset($category['parent']) && $category['parent'] === $parentId) {
                $children[] = [
                    'id' => $id,
                    'name' => $category['name'],
                    'level' => $category['level'],
                    'has_children' => $category['has_children'],
                    'tid' => $category['tid'] ?? 0
                ];
            }
        }
        return $children;
    }
    
    // 获取一级分类
    public function getLevel1Categories() {
        $result = [];
        foreach ($this->categories as $id => $category) {
            if ($category['level'] === 1) {
                $result[] = [
                    'type_id' => $id,
                    'type_name' => $category['name']
                ];
            }
        }
        return $result;
    }
    
    // 从B站API获取视频列表
    // 从B站API获取视频列表
public function getVideoList($tid, $page = 1, $keyword = '') {
    $videos = [];
    
    if (!empty($keyword)) {
        return $this->searchVideos($keyword, $page);
    }
    
    // 热门推荐特殊处理
    if ($tid == 0) {
        return $this->getPopularVideos($page);
    }
    
    // 处理自定义分类
    if (is_string($tid) && !is_numeric($tid)) {
        return $this->getCustomCategoryVideos($tid, $page);
    }
    
    try {
        $url = "{$this->baseUrl}/x/web-interface/newlist?" . http_build_query([
            'rid' => $tid,
            'type' => 0,
            'pn' => $page,
            'ps' => 20
        ]);
        
        $response = $this->curlRequest($url);
        $data = json_decode($response, true);
        
        if (isset($data['data']['archives'])) {
            foreach ($data['data']['archives'] as $item) {
                $videos[] = $this->formatVideoData($item);
            }
        }
    } catch (Exception $e) {
        $videos = $this->getSampleVideos($tid);
    }
    
    return $videos;
}

// 新增方法：处理自定义分类
private function getCustomCategoryVideos($categoryId, $page = 1) {
    $videos = [];
    
    switch ($categoryId) {
        // 热门推荐细分
        case 'hot_today':
            return $this->getPopularVideos($page);
        case 'hot_week':
            return $this->getWeeklyPopularVideos($page);
        case 'hot_month':
            return $this->getMonthlyPopularVideos($page);
        case 'hot_year':
            return $this->getYearlyPopularVideos($page);
        case 'hot_soaring':
            return $this->getSoaringVideos($page);
        case 'hot_newcomer':
            return $this->getNewcomerVideos($page);
            
        // 地区分类 - 使用搜索API
        case 'region_china':
            return $this->searchVideos('中国', $page);
        case 'region_japan':
            return $this->searchVideos('日本', $page);
        case 'region_korea':
            return $this->searchVideos('韩国', $page);
        case 'region_usa':
            return $this->searchVideos('美国', $page);
        case 'region_europe':
            return $this->searchVideos('欧洲', $page);
            
        // 时间分类 - 结合年份搜索
        case 'time_2025':
            return $this->searchVideos('2025', $page);
        case 'time_2024':
            return $this->searchVideos('2024', $page);
        case 'time_2023':
            return $this->searchVideos('2023', $page);
        case 'time_2022':
            return $this->searchVideos('2022', $page);
        case 'time_2021':
            return $this->searchVideos('2021', $page);
        case 'time_classic':
            return $this->searchVideos('经典', $page);
            
        // 类型分类
        case 'genre_funny':
            return $this->searchVideos('搞笑', $page);
        case 'genre_healing':
            return $this->searchVideos('治愈', $page);
        case 'genre_hotblood':
            return $this->searchVideos('热血', $page);
        case 'genre_romance':
            return $this->searchVideos('恋爱', $page);
        case 'genre_mystery':
            return $this->searchVideos('悬疑', $page);
        case 'genre_horror':
            return $this->searchVideos('恐怖', $page);
            
        // UP主分类
        case 'uper_laofanqie':
            return $this->searchVideos('老番茄', $page);
        case 'uper_lexburner':
            return $this->searchVideos('LexBurner', $page);
        case 'uper_aochang':
            return $this->searchVideos('敖厂长', $page);
        case 'uper_wanggang':
            return $this->searchVideos('王刚', $page);
        case 'uper_liziqi':
            return $this->searchVideos('李子柒', $page);
        case 'uper_luoxiang':
            return $this->searchVideos('罗翔说刑法', $page);
            
        default:
            return $this->getSampleVideos($categoryId);
    }
}

// 新增方法：获取周榜、月榜等（这里可以用不同的API或搜索条件）
private function getWeeklyPopularVideos($page = 1) {
    // 可以使用不同的排序方式或时间范围
    return $this->getPopularVideos($page);
}

private function getMonthlyPopularVideos($page = 1) {
    return $this->getPopularVideos($page);
}

private function getYearlyPopularVideos($page = 1) {
    return $this->getPopularVideos($page);
}

private function getSoaringVideos($page = 1) {
    // 飙升榜可以使用搜索API按播放量增长率排序
    return $this->searchVideos('', $page);
}

private function getNewcomerVideos($page = 1) {
    // 新人榜可以搜索新UP主的视频
    return $this->searchVideos('新人', $page);
}
    
    // 获取热门视频
    private function getPopularVideos($page = 1) {
        try {
            $url = "{$this->baseUrl}/x/web-interface/popular?" . http_build_query([
                'pn' => $page,
                'ps' => 20
            ]);
            
            $response = $this->curlRequest($url);
            $data = json_decode($response, true);
            
            $videos = [];
            if (isset($data['data']['list'])) {
                foreach ($data['data']['list'] as $item) {
                    $videos[] = $this->formatVideoData($item);
                }
            }
            return $videos;
        } catch (Exception $e) {
            return $this->getSampleVideos(0);
        }
    }
    
    // 格式化视频数据 - 修复播放URL格式
    private function formatVideoData($item) {
        $aid = $item['aid'] ?? $item['id'] ?? '0';
        $bvid = $item['bvid'] ?? '';
        
        // 修复播放URL - 使用av号_cid格式
        $cid = $item['cid'] ?? $item['stat']['aid'] ?? $aid;
        $playUrl = $aid . '_' . $cid;
        
        return [
            'vod_id' => $aid,
            'vod_name' => $item['title'] ?? '未知标题',
            'vod_pic' => $item['pic'] ?? '',
            'vod_remarks' => $this->formatPlayCount($item['stat']['view'] ?? $item['play'] ?? 0),
            'vod_content' => $item['desc'] ?? $item['description'] ?? '',
            'vod_play_from' => 'B站',
            'vod_play_url' => "第1集$$playUrl"
        ];
    }
    
    // 搜索视频
    private function searchVideos($keyword, $page = 1) {
        try {
            $url = "{$this->baseUrl}/x/web-interface/search/type?" . http_build_query([
                'search_type' => 'video',
                'keyword' => $keyword,
                'page' => $page
            ]);
            
            $response = $this->curlRequest($url);
            $data = json_decode($response, true);
            
            $videos = [];
            if (isset($data['data']['result'])) {
                foreach ($data['data']['result'] as $item) {
                    $videos[] = $this->formatVideoData($item);
                }
            }
            return $videos;
        } catch (Exception $e) {
            $playUrl = "123456_789012"; // 示例ID
            return [[
                'vod_id' => 'search_1', 
                'vod_name' => '搜索结果: ' . $keyword, 
                'vod_pic' => '', 
                'vod_remarks' => '搜索',
                'vod_play_from' => 'B站',
                'vod_play_url' => "第1集$$playUrl"
            ]];
        }
    }
    
    // 获取视频详情
    public function getVideoDetail($aid) {
        try {
            $url = "{$this->baseUrl}/x/web-interface/view?aid=" . $aid;
            $response = $this->curlRequest($url);
            $data = json_decode($response, true);
            
            if (isset($data['data'])) {
                $video = $data['data'];
                $cid = $video['cid'] ?? $aid;
                $playUrl = $aid . '_' . $cid;
                
                return [
                    'vod_id' => $aid,
                    'vod_name' => $video['title'] ?? '未知标题',
                    'vod_pic' => $video['pic'] ?? '',
                    'vod_content' => $video['desc'] ?? '',
                    'vod_play_from' => 'B站',
                    'vod_play_url' => "第1集$$playUrl"
                ];
            }
        } catch (Exception $e) {
            $playUrl = $aid . '_' . $aid;
            return [
                'vod_id' => $aid,
                'vod_name' => 'B站视频详情 - ' . $aid,
                'vod_pic' => '',
                'vod_content' => '这是一个B站视频的详细描述',
                'vod_play_from' => 'B站',
                'vod_play_url' => "第1集$$playUrl"
            ];
        }
    }
    
    // 获取播放地址 - 采用三级获取策略
    public function getPlayUrl($id) {
        if (strpos($id, '_') === false) {
            return [
                'parse' => 0,
                'url' => '',
                'header' => $this->getHeaders()
            ];
        }
        
        list($avid, $cid) = explode('_', $id);
        $playUrl = '';
        
        // 第一级：官方API获取播放地址
        $playUrl = $this->getOfficialPlayUrl($avid, $cid);
        
        // 第二级：备用地址
        if (empty($playUrl)) {
            $playUrl = $this->getBackupPlayUrl($avid, $cid);
        }
        
        // 第三级：第三方解析
        if (empty($playUrl)) {
            $playUrl = $this->getThirdPartyPlayUrl($avid, $cid);
        }
        
        $headers = $this->getHeaders();
        $headers['Referer'] = 'https://www.bilibili.com/video/av' . $avid;
        
        return [
            'parse' => 0,  // 0=直接播放
            'url' => $playUrl,
            'header' => $headers
        ];
    }
    
    // 官方API获取播放地址
    private function getOfficialPlayUrl($avid, $cid) {
        try {
            $url = "{$this->baseUrl}/x/player/playurl";
            $params = [
                'avid' => $avid,
                'cid' => $cid,
                'qn' => 116, // 高清
                'fnval' => 16,
                'fourk' => 1
            ];
            
            $response = $this->curlRequest($url, $params);
            $data = json_decode($response, true);
            
            if (isset($data['data']['durl'][0]['url'])) {
                return $data['data']['durl'][0]['url'];
            }
        } catch (Exception $e) {
            // 忽略错误，继续尝试其他方法
        }
        
        return '';
    }
    
    // 获取备用播放地址
    private function getBackupPlayUrl($avid, $cid) {
        // 尝试多种备用地址格式
        $backupUrls = [
            "https://cn-bj-cc-01-12.bilivideo.com/upgcxcode/21/73/{$cid}/{$cid}-1-80.flv",
            "https://upos-sz-mirrorcos.bilivideo.com/upgcxcode/21/73/{$cid}/{$cid}-1-80.flv",
        ];
        
        foreach ($backupUrls as $url) {
            if ($this->checkUrlAccessible($url)) {
                return $url;
            }
        }
        
        return '';
    }
    
    // 获取第三方解析地址
    private function getThirdPartyPlayUrl($avid, $cid) {
        // 使用第三方B站解析服务
        $thirdPartyUrls = [
            "https://api.injahow.cn/bparse/?av={$avid}&cid={$cid}",
        ];
        
        foreach ($thirdPartyUrls as $url) {
            try {
                $result = $this->curlRequest($url);
                $data = json_decode($result, true);
                if (isset($data['url']) && !empty($data['url'])) {
                    return $data['url'];
                }
            } catch (Exception $e) {
                // 继续尝试下一个
            }
        }
        
        return '';
    }
    
    // 检查URL是否可访问
    private function checkUrlAccessible($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return $httpCode === 200;
    }
    
    // 获取请求头
    private function getHeaders() {
        return [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Referer' => 'https://www.bilibili.com'
        ];
    }
    
    // 示例视频数据
    private function getSampleVideos($tid) {
        $samples = [];
        $categoryName = $this->getCategoryNameByTid($tid);
        
        for ($i = 1; $i <= 10; $i++) {
            $playUrl = "123456_789012";
            
            $samples[] = [
                'vod_id' => 'sample_' . $tid . '_' . $i,
                'vod_name' => $categoryName . '示例视频' . $i,
                'vod_pic' => '',
                'vod_remarks' => '示例',
                'vod_content' => '这是一个示例视频描述',
                'vod_play_from' => 'B站',
                'vod_play_url' => "第1集$$playUrl"
            ];
        }
        
        return $samples;
    }
    
    // 根据tid获取分类名称
    private function getCategoryNameByTid($tid) {
        foreach ($this->categories as $category) {
            if (isset($category['tid']) && $category['tid'] == $tid) {
                return $category['name'];
            }
        }
        return '未知分类';
    }
    
    // 格式化播放量
    private function formatPlayCount($count) {
        if ($count >= 10000) {
            return round($count / 10000, 1) . '万';
        }
        return $count;
    }
    
    // CURL请求
    private function curlRequest($url, $params = []) {
        $ch = curl_init();
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_REFERER => 'https://www.bilibili.com',
            CURLOPT_ENCODING => 'gzip'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('HTTP请求失败: ' . $httpCode);
        }
        
        return $response;
    }
}

// 初始化B站爬虫
$biliSpider = new BiliBiliSpider();

// 获取子分类
function getChildCategories($parentId) {
    global $biliSpider;
    return $biliSpider->getChildren($parentId);
}

// 获取分类信息
function getCategoryInfo($categoryId) {
    global $biliSpider;
    return $biliSpider->getCategory($categoryId);
}

// 主逻辑
switch ($ac) {
    case 'detail':
        if (!empty($ids)) {
            $videoDetail = $biliSpider->getVideoDetail($ids);
            $data = ['list' => [$videoDetail]];
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        } elseif (!empty($t)) {
            $filters = !empty($f) ? json_decode($f, true) : [];
            
            $isSubRequest = isset($filters['is_sub']) && $filters['is_sub'] === 'true';
            $categoryId = $filters['category_id'] ?? $t;
            
            $categoryInfo = getCategoryInfo($categoryId);
            $hasChildren = $categoryInfo['has_children'] ?? false;
            
            if ($isSubRequest && !$hasChildren) {
                $tid = $categoryInfo['tid'] ?? 0;
                $videos = $biliSpider->getVideoList($tid, $pg);
                
                $data = [
                    'list' => $videos,
                    'page' => intval($pg),
                    'pagecount' => 10,
                    'limit' => 20,
                    'total' => 200,
                    'current_category' => $categoryInfo['name'] ?? '',
                    'style' => ['type' => 'rect', 'ratio' => 0.75]
                ];
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            } else {
                $children = getChildCategories($t);
                
                if (empty($children)) {
                    $tid = $categoryInfo['tid'] ?? 0;
                    $videos = $biliSpider->getVideoList($tid, $pg);
                    
                    $data = [
                        'list' => $videos,
                        'page' => intval($pg),
                        'pagecount' => 10,
                        'limit' => 20,
                        'total' => 200,
                        'current_category' => $categoryInfo['name'] ?? '',
                        'style' => ['type' => 'rect', 'ratio' => 0.75]
                    ];
                } else {
                    $subList = [];
                    foreach ($children as $child) {
                        $subList[] = [
                            'vod_id' => $child['id'],
                            'vod_name' => $child['name'],
                            'vod_pic' => '',
                            'vod_remarks' => '分类'
                        ];
                    }
                    
                    $data = [
                        'is_sub' => true,
                        'list' => $subList,
                        'page' => intval($pg),
                        'pagecount' => 1,
                        'limit' => 20,
                        'total' => count($subList),
                        'parent_category' => $categoryInfo['name'] ?? '',
                        'style' => ['type' => 'rect', 'ratio' => 1.5]
                    ];
                }
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            }
        } else {
            $data = [
                'class' => $biliSpider->getLevel1Categories(),
                'list' => $biliSpider->getVideoList(0, 1), // 热门推荐
                'style' => ['type' => 'rect', 'ratio' => 1.33]
            ];
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case 'search':
        $videos = $biliSpider->getVideoList(0, $pg, $wd);
        $data = [
            'list' => $videos,
            'page' => intval($pg),
            'pagecount' => 10,
            'limit' => 20,
            'total' => count($videos)
        ];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        break;
        
    case 'play':
        $playInfo = $biliSpider->getPlayUrl($id);
        echo json_encode($playInfo, JSON_UNESCAPED_UNICODE);
        break;
    
    default:
        $data = ['error' => 'Unknown action: ' . $ac];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
?>