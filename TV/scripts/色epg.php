<?php
/**
 * EPG 节目表获取插件 - 最终全兼容修复版
 */

error_reporting(0);
date_default_timezone_set('Asia/Taipei');

$CHANNELS = [
  // 凯擘大宽频(kbro)
    "松视1" => "kbro@@297",
    "松视2" => "kbro@@298",
    "松视3" => "kbro@@299",
    "松视4" => "kbro@@300",
    "彩虹电影" => "kbro@@292",
    "彩虹E" => "kbro@@291",
    "彩虹K" => "kbro@@293",
    "潘朵啦玩美" => "kbro@@904",
    "潘朵啦粉红" => "kbro@@905",
    "惊艳" => "kbro@@906",
    "香蕉成人" => "kbro@@907",
    "极限电影" => "kbro@@912",
// 中嘉宽频(homeplus)
    "彩虹" => "homeplus@@730@@301", 
    "松视4" => "homeplus@@730@@302",   
    "潘朵啦玩美" => "homeplus@@730@@303", 
    "潘朵啦粉红" => "homeplus@@730@@304",   
    "松视1" => "homeplus@@730@@305",      
    "松视2" => "homeplus@@730@@306",      
    "松视3" => "homeplus@@730@@307",      
    "彩虹E" => "homeplus@@730@@308",      
    "彩虹MOVIE" => "homeplus@@730@@309",   
    "彩虹K" => "homeplus@@730@@310",      
    "HOT" => "homeplus@@730@@311",      
    "HAPPY" => "homeplus@@730@@312",   
    "玩家" => "homeplus@@730@@313",   
    "惊艳成人电影" => "homeplus@@730@@314",  
    "香蕉成人" => "homeplus@@730@@315",   
    "乐活" => "homeplus@@730@@316",   
];

$id = $_GET['id'] ?? $_GET['ch'] ?? '';
$date = $_GET['date'] ?? date("Y-m-d");
$format = isset($_GET['id']) ? 1 : 2;

if (!$id) {
    header('Content-Type: application/json');
    exit(json_encode(["code" => 500, "msg" => "参数缺失"]));
}

function matchChannel($query, $channels) {
    $cleaner = function($str) {
        $str = strtoupper(urldecode($str));
        $map = ["視"=>"视", "樂"=>"乐", "驚"=>"惊", "艷"=>"艳", "電影"=>"电影", "頻"=>"频", "極"=>"极"];
        $str = strtr($str, $map);
        $junk = ['JSTAR', 'KBRO', 'HOMEPLUS', '高清', 'HD', '频道', '頻道', '台', '电影', '電影', ' '];
        return str_replace($junk, '', $str);
    };

    $input = $cleaner($query);
    foreach ($channels as $key => $val) {
        $target = $cleaner($key);
        if ($input !== '' && (strpos($input, $target) !== false || strpos($target, $input) !== false)) {
            return $val;
        }
    }
    return null;
}

$config = matchChannel($id, $CHANNELS);

if (!$config) {
    header('Content-Type: application/json');
    exit(json_encode(["code" => 500, "msg" => "未匹配"]));
}

$params = explode("@@", $config);
$source = $params[0];

function epg_kbro($tvid, $date) {
    $url = "https://www.kbro.com.tw/do/getpage_catvtable.php?action=get_channelprogram&channelid=$tvid&showtime=" . date('Ymd', strtotime($date));
    $res = json_decode(curl_request($url), true);
    $list = [];
    foreach (($res['data'] ?? []) as $row) {
        $list[] = ["title" => $row["programname"], "start" => date("H:i", strtotime($row["starttime"])), "end" => date("H:i", strtotime($row["endtime"]))];
    }
    return $list;
}

function epg_homeplus($so, $channelid, $date) {
    $url = "https://www.homeplus.net.tw/cable/Product_introduce/digital_tv/get_channel_content";
    $res = json_decode(curl_request($url, ['so' => $so, 'channelid' => $channelid]), true);
    $list = [];
    foreach (($res['date_program'][$date] ?? []) as $programs) {
        foreach ($programs as $p) {
            $list[] = ["title" => $p['name'], "start" => date("H:i", strtotime($p['beginTime']))];
        }
    }
    for ($i = 0; $i < count($list); $i++) {
        $list[$i]['end'] = ($i < count($list) - 1) ? $list[$i+1]['start'] : "00:00";
    }
    return $list;
}

function curl_request($url, $post = null) {
    $ch = curl_init($url);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

$list = ($source === 'kbro') ? epg_kbro($params[1], $date) : epg_homeplus($params[1], $params[2], $date);

header('Content-Type: application/json');
if ($format === 1) {
    $data = [];
    foreach ($list as $row) { $data[] = ["name" => $row["title"], "starttime" => $row["start"]]; }
    echo json_encode(["code" => $list ? 200 : 500, "name" => $id, "date" => $date, "data" => $data], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["code" => $list ? 200 : 500, "channel_name" => $id, "date" => $date, "epg_data" => $list], JSON_UNESCAPED_UNICODE);
}