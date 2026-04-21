<?php
// 记得先修改node_api改成你自己的ip
$node_api = 'http://192.168.100.1:3000';
$my_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";

$backup_url = 'https://cdn.jsdelivr.net/gh/jerainlvjing/tvlive@demo/mfc1.mp4';

ini_set('default_socket_timeout', 10);

if (isset($_GET['id']) && !isset($_GET['play']) && !isset($_GET['player'])) {
    $target = $_GET['id'];
    $json = fetchUrl($node_api . '/api/play?id=' . urlencode($target));
    $res = json_decode($json, true);
    
    if (isset($res['url']) && !empty($res['url'])) {
        header("Location: " . $res['url']);
    } else {
        header("Location: " . $backup_url);
    }
    exit;
}
elseif (isset($_GET['list'])) {
    $items = getListData($node_api);
    header('Content-Type: text/plain; charset=utf-8');
    echo "#EXTM3U\n";
    foreach ($items as $item) {
        $play_link = "{$my_url}?id={$item['vod_id']}";
        echo "#EXTINF:-1 tvg-logo=\"{$item['vod_pic']}\" group-title=\"mfc-live\", {$item['vod_name']}\n";
        echo "{$play_link}\n";
    }
    exit;
}
elseif (isset($_GET['play']) || isset($_GET['player'])) {
    $items = getListData($node_api);
    $auto_play_id = isset($_GET['id']) ? $_GET['id'] : '';
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>MFC Player</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        <style>
            body { margin: 0; background: #000; color: #fff; display: flex; height: 100vh; font-family: sans-serif; overflow: hidden; }
            #player { flex: 1; display: flex; align-items: center; justify-content: center; background: #111; position: relative;}
            video { width: 100%; height: 100%; max-height: 100vh; }
            #sidebar { width: 300px; background: #1a1a1a; overflow-y: auto; border-left: 1px solid #333; display: flex; flex-direction: column; }
            .list-item { padding: 10px; display: flex; align-items: center; cursor: pointer; border-bottom: 1px solid #333; transition: 0.2s; }
            .list-item:hover { background: #333; }
            .list-item.active { background: #e91e63; }
            .list-item img { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; }
            .list-item .name { font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            #loading { display: none; position: absolute; color: #ccc; font-size: 20px; }
            @media (max-width: 768px) { body { flex-direction: column; } #player { height: 40vh; flex: none; } #sidebar { flex: 1; width: 100%; } }
        </style>
    </head>
    <body>
        <div id="player">
            <div id="loading">Loading...</div>
            <video id="video" controls autoplay playsinline></video>
        </div>
        <div id="sidebar">
            <div style="padding:15px;text-align:center;background:#222;">
                Online (<?php echo count($items); ?>)
            </div>
            <?php foreach ($items as $item): ?>
            <div class="list-item" id="item-<?php echo $item['vod_id']; ?>" onclick="playChannel('<?php echo $item['vod_id']; ?>', this)">
                <img src="<?php echo $item['vod_pic']; ?>" loading="lazy">
                <div class="name"><?php echo $item['vod_name']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <script>
            var hls = new Hls();
            var video = document.getElementById('video');
            var loading = document.getElementById('loading');
            
            function playChannel(id, el) {
                document.querySelectorAll('.list-item').forEach(i => i.classList.remove('active'));
                if(el) el.classList.add('active');
                
                loading.style.display = 'block';
                var playUrl = '<?php echo $my_url; ?>?id=' + encodeURIComponent(id);

                if(Hls.isSupported()){ 
                    hls.loadSource(playUrl); 
                    hls.attachMedia(video); 
                    hls.on(Hls.Events.MANIFEST_PARSED,function(){ loading.style.display = 'none'; video.play(); });
                    hls.on(Hls.Events.ERROR, function (event, data) {
                        if (data.fatal) {
                            hls.destroy();
                            video.src = playUrl; 
                            video.play();
                            loading.style.display = 'none';
                        }
                    });
                } else { 
                    video.src = playUrl; 
                    video.play(); 
                    loading.style.display = 'none';
                }
            }
            var autoId = "<?php echo $auto_play_id; ?>";
            if(autoId) {
                var targetEl = document.getElementById('item-' + autoId);
                playChannel(autoId, targetEl);
            }
        </script>
    </body>
    </html>
    <?php
    exit;
}

function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function getListData($node_api) {
    $json = fetchUrl($node_api . '/api/list');
    $data = json_decode($json, true);
    if (isset($data['data']) && is_array($data['data'])) {
        return $data['data'];
    }
    return [];
}
?>