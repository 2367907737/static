<?php
header('Content-Type: application/json; charset=utf-8');
// http://127.0.0.1:9980/config.php

// 初始化站点计数器
$siteCount = 0;

// ==================
// 1. 生成固定配置
// ==================
$config = [
    // 站点数量提示
    "notice" => "提示：正在计算站源数量...",
    
    // 主jar配置（用于本地固定站点和本地生成站点）
    "spider" => "http://127.0.0.1:9978/file/江湖/jar/yt.jar",
    
    // 固定headers配置
    "headers" => [
        [
            "host" => "img\\d+.doubanio.com",
            "header" => [
                "Referer" => "https://movie.douban.com"
            ]
        ],
        [
            "host" => "img1.doubanio.com",
            "header" => [
                "Referer" => "https://movie.douban.com"
            ]
        ],
        [
            "host" => "img2.doubanio.com",
            "header" => [
                "Referer" => "https://movie.douban.com"
            ]
        ],
        [
            "host" => "img3.doubanio.com",
            "header" => [
                "Referer" => "https://movie.douban.com"
            ]
        ],
        [
            "host" => "img4.doubanio.com",
            "header" => [
                "Referer" => "https://movie.douban.com"
            ]
        ],
        [
            "host" => "img5.doubanio.com",
            "header" => [
                "Referer" => "https://movie.douban.com"
            ]
        ]
    ],
    
    // 固定lives配置
    "lives" => [
        [
            "name" => "litv",
            "url" => "http://127.0.0.1:8084/litv.php"
        ],
        [
            "name" => "直播-央视频",
            "url" => "http://127.0.0.1:8083/ysp.php?action=txt"
        ],
         [
            "name" => "直播-虎牙直播",
            "url" => "http://127.0.0.1:8084/huya.php"
        ],
        [
            "name" => "电影-虎牙一起看",
            "url" => "http://127.0.0.1:8084/hyyqk.php"
        ],
        [
            "name" => "直播-xx",
            "url" => "http://127.0.0.1:8080/xx.php"
        ],
        [
            "name" => "免费",
            "type" => 0,
            "url" => "https://live.catvod.com/tv.m3u",
            "epg" => "https://fy.188766.xyz/e.xml",
            "logo" => "https://epg.112114.xyz/logo/{name}.png",
            "ua" => "okhttp3.1"
        ],
        [
            "name" => "冰茶",
            "type" => 0,
            "playerType" => 2,
            "url" => "https://fy.188766.xyz/?ip=true&mima=bingcha1130&json=true&huikan=1",
            "epg" => "https://fy.188766.xyz/e.xml",
            "logo" => "https://epg.112114.xyz/logo/{name}.png",
            "ua" => "bingcha/1.1 (mianfeifenxiang) "
        ],
        [
            "name" => "电视家",
            "url" => "https://down.nigx.cn/dsj.zzong6599.workers.dev/",
            "header" => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
                "Referer" => "",
                "cookie" => ""
            ],
            "epg" => "https://iptv.crestekk.cn/epgphp/index.php"
        ],
        [
            "name" => "裤佬",
            "url" => "https://down.nigx.cn/raw.githubusercontent.com/Jsnzkpg/Jsnzkpg/Jsnzkpg/Jsnzkpg1",
            "header" => [
                "Referer" => "https://www.kds.tw/"
            ],
            "epg" => "https://iptv.crestekk.cn/epgphp/index.php"
        ],
        [
            "name" => "黄色",
            "url" => "https://down.nigx.cn/mpimg.cn/down.php/25da10b0cb7b90d422ae22852bd7d414.txt",
            "header" => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
                "Referer" => "",
                "cookie" => ""
            ],
            "epg" => "http://epg.112114.xyz/?ch={name}&date={date}"
        ],
        [
            "name" => "直播",
            "url" => "https://rt.http3.lol/index.php?q=aHR0cHM6Ly96Yi56em9uZzY1OTkud29ya2Vycy5kZXY="
        ],
        [
            "name" => "咪咕",
            "url" => "https://down.nigx.cn/raw.githubusercontent.com/develop202/migu_video/refs/heads/main/interface.txt",
            "header" => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
                "Referer" => "",
                "cookie" => ""
            ]
        ],
        [
            "name" => "日本",
            "url" => "https://down.nigx.cn/raw.githubusercontent.com/luongz/iptv-jp/refs/heads/main/jp_clean.m3u",
            "header" => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
                "Referer" => "",
                "cookie" => ""
            ]
        ],
        [
            "name" => "日本1",
            "url" => "https://down.nigx.cn/raw.githubusercontent.com/luongz/iptv-jp/refs/heads/main/jp.m3u",
            "header" => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
                "Referer" => "",
                "cookie" => ""
            ]
        ],
        [
            "name" => "随机亚麻跌",
            "type" => 0,
            "url" => "https://sjymd.zzong6599.workers.dev/",
            "header" => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
                "Referer" => "",
                "cookie" => ""
            ],
            "epg" => "http://diyp5.112114.xyz/?ch={name}&date={date}",
            "logo" => "http://diyp5.112114.xyz/{name}.png"
        ],
        [
            "name" => "秘密花园",
            "type" => 0,
            "url" => "https://down.nigx.cn/mmhy.zzong6599.workers.dev/",
            "header" => [
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
                "Referer" => "",
                "cookie" => ""
            ],
            "epg" => "http://diyp5.112114.xyz/?ch={name}&date={date}",
            "logo" => "http://diyp5.112114.xyz/{name}.png"
        ],
        [
            "name" => "SMT(py)",
            "type" => 3,
            "api" => "http://127.0.0.1:9978/file/江湖/py/lives-SMT.py",
            "ext" => []
        ],
        [
            "name" => "iptv345",
            "type" => 3,
            "api" => "http://127.0.0.1:9978/file/江湖/py/lives-iptv345.py",
            "ext" => []
        ],
        [
            "name" => "zxbv",
            "type" => 3,
            "api" => "http://127.0.0.1:9978/file/江湖/py/live_zxbv.py",
            "ext" => []
        ],
        [
            "name" => "kzb",
            "type" => 3,
            "api" => "http://127.0.0.1:9978/file/江湖/py/lives-kzb.py",
            "ext" => []
        ],
        [
            "name" => "直播bx5kge(js)",
            "type" => 3,
            "url" => "https://b6f56s23.bx5kge.com/assets/js/tv.js",
            "api" => "http://127.0.0.1:9978/file/江湖/普通js/直播bx5kge.min.js",
            "ext" => []
        ],
        [
            "name" => "快直播(js)",
            "type" => 3,
            "url" => "https://jzb5kqln.huajiaedu.com",
            "api" => "http://127.0.0.1:9978/file/江湖/普通js/快直播.min.js",
            "ext" => []
        ]
    ],
    
    // 固定parses配置
    "parses" => [
        [
            "name" => "五妹轮询",
            "type" => 0,
            "url" => "https://down.nigx.cn/jx.zzong6599.workers.dev/?url=",
            "ext" => [
                "flag" => [".*."],
                "header" => [
                    "User-Agent" => "okhttp/4.9.1"
                ]
            ]
        ],
        [
            "name" => "五妹自动",
            "type" => 0,
            "url" => "https://down.nigx.cn/xjx.zzong6599.workers.dev/?url="
        ],
        [
            "name" => "4K智家",
            "type" => 1,
            "url" => "http://125.208.23.251:1314/lunxun/?url="
        ],
        [
            "name" => "星睿⚡4K",
            "type" => 1,
            "url" => "http://sspa8.top:8100/api/?cat_ext=eyJmbGFnIjpbInFxIiwi6IW+6K6vIiwicWl5aSIsIueIseWlh+iJuiIsIuWlh+iJuiIsInlvdWt1Iiwi5LyY6YW3Iiwic29odSIsIuaQnOeLkCIsImxldHYiLCLkuZDop4YiLCJtZ3R2Iiwi6IqS5p6cIiwidG5tYiIsInNldmVuIiwiYmlsaWJpbGkiLCIxOTA1Il0sImhlYWRlciI6eyJVc2VyLUFnZW50Ijoib2todHRwLzQuOS4xIn19&key=星睿4k&url="
        ],
        [
            "name" => "HD⚡腾讯解析",
            "type" => 1,
            "url" => "http://shybot.top/v2/video/jx/?shykey=4595a71a4e7712568edcfa43949236b42fcfcb04997788ebe7984d6da2c6a51c&url="
        ],
        [
            "name" => "4k芒果⚡专解",
            "type" => 1,
            "url" => "http://shybot.top/v2/video/jx/?shykey=4595a71a4e7712568edcfa43949236b42fcfcb04997788ebe7984d6da2c6a51c&qn=max&url="
        ],
        [
            "name" => "岁岁解析8888次",
            "type" => 1,
            "url" => "http://sspa8.top:8100/api/?key=1060089351&url="
        ],
        [
            "name" => "解析1",
            "type" => 1,
            "url" => "https://kalbim.xatut.top/kalbim2025/781718/play/video_player.php?url="
        ]
    ],
    
    // 固定环境市场配置（没有jar字段，使用主jar）
    "sites" => [
        [
            "key" => "csp_BinMarket",
            "name" => "环境市场",
            "api" => "csp_BinMarket",
            "type" => "3"
        ],
        [
            "key" =>  "PHP爬虫第六代",
            "name" =>  "PHP爬虫第六代",
            "type" => 3,
            "searchable" =>  1,
            "quickSearch" => 1,
            "filterable" => 1,
            "api" => "csp_PhpServer",
            "timeout" => 300
        ],
        [
            "key" =>  "PHP爬虫第五代",
            "name" =>  "PHP爬虫第五代",
            "type" => 3,
            "searchable" =>  1,
            "quickSearch" => 1,
            "filterable" => 1,
            "api" => "csp_php第5代",
            "timeout" => 300
        ],
        [
            "key" =>  "PHP爬虫第四代",
            "name" =>  "PHP爬虫第四代",
            "type" => 3,
            "searchable" =>  1,
            "quickSearch" => 1,
            "filterable" => 1,
            "api" => "csp_php第4代",
            "timeout" => 300
        ],
        [
            "key" =>  "Cntvjar",
            "name" =>  "Cntvjar",
            "type" =>  3,
            "api" =>  "csp_CntvSpider",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "jable-jar",
            "name" =>  "jable-jar",
            "type" =>  3,
            "api" =>  "csp_Jable",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "PTTjar",
            "name" =>  "PTTjar",
            "type" =>  3,
            "api" =>  "csp_PTT",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "看球jar",
            "name" =>  "看球jar",
            "type" =>  3,
            "searchable" =>  1,
            "api" =>  "csp_Kanqiu",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ]
        ],
        [
            "key" =>  "动漫jar",
            "name" =>  "动漫jar",
            "type" =>  3,
            "api" =>  "csp_YHDM",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "爱看机器人jar",
            "name" =>  "爱看机器人jar",
            "type" =>  3,
            "searchable" =>  1,
            "api" =>  "csp_Ikanbot"
        ],
        [
            "key" =>  "本地文件",
            "name" =>  "本地文件",
            "type" =>  3,
            "searchable" =>  1,
            "api" =>  "csp_Local"
        ],
        [
            "key" =>  "服务器cctv",
            "name" =>  "服务器cctv",
            "type" =>  1,
            "api" =>  "http://zhangqun1818.serv00.net/cctv.php",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "服务器小苹果",
            "name" =>  "服务器小苹果",
            "type" =>  4,
            "api" =>  "http://zhangqun19.serv00.net/pingguo.php",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "哔哩lz",
            "name" =>  "哔哩lz",
            "type" =>  3,
            "api" =>  "csp_Bilibili",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1,
            "changeable" =>  1
        ],
        [
            "key" =>  "哔哩fm",
            "name" =>  "哔哩fm",
            "type" =>  3,
            "api" =>  "csp_Bili",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "ext" =>  [
                "cookie" =>  "",
                "type" =>  "沙雕仙逆#沙雕动画#风景#听书#太空#海洋#动物#一口气#综合#音乐#影视"
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "界影视",
            "name" =>  "界影视",
            "type" =>  3,
            "api" =>  "csp_JieYingShi",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "changeable" =>  1
        ],
        [
            "key" =>  "爱瓜电视",
            "name" =>  "爱瓜电视",
            "type" =>  3,
            "api" =>  "csp_AiguaTV1",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "changeable" =>  1
        ],
        [
            "key" =>  "苹果jar",
            "name" =>  "苹果jar",
            "type" =>  3,
            "api" =>  "csp_PingGuo",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "文件不正经m3u",
            "name" =>  "🔞文件不正经m3u",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=13",
            "ext" =>  "/storage/emulated/0/江湖/php/wj/不正经/",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "不正经json清单",
            "name" =>  "🔞不正经json清单",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=9",
            "ext" =>  "/storage/emulated/0/江湖/php/json/不正经/",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "完整版老张json",
            "name" =>  "完整版老张json",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=9",
            "ext" =>  "/storage/emulated/0/江湖/php/json/",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "完整版老张文件",
            "name" =>  "完整版老张文件",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=8",
            "ext" =>  "/storage/emulated/0/江湖/php/wj/",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "文件采集",
            "name" =>  "文件采集",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=8",
            "ext" =>  "/storage/emulated/0/江湖/php/wj/采集",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "在线json",
            "name" =>  "🔞在线json",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=8",
            "ext" =>  "/storage/emulated/0/江湖",
            "style" =>  [
                "type" =>  "rect",
                "ratio" =>  1.33
            ],
            "searchable" =>  1
        ],
        [
            "key" =>  "听书go",
            "name" =>  "听书go",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=11"
        ],
        [
            "key" =>  "采集go",
            "name" =>  "采集go",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=12",
            "ext" =>  "0"
        ],
        [
            "key" =>  "油管go",
            "name" =>  "油管go",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988"
        ],
        [
            "key" =>  "欧乐影院",
            "name" =>  "欧乐影院",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=4"
        ],
        [
            "key" =>  "采集集合",
            "name" =>  "采集集合",
            "type" =>  1,
            "api" =>  "http://127.0.0.1:1988/lb?lb=3"
        ],
        [
            "key" =>  "短剧",
            "name" =>  "短剧",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=2"
        ],
        [
            "key" =>  "服务器的油管",
            "name" =>  "服务器的油管",
            "type" =>  4,
            "api" =>  "http://127.0.0.1:1988/lb?lb=油管"
        ]
    ]
];

// 计算固定站点数量
$siteCount = count($config['sites']);

// ==================
// 2. 生成 /storage/emulated/0/江湖/wwwroot/php/ 目录的 PHP sites（无jar）
// ==================
$phpDir = "/storage/emulated/0/江湖/wwwroot/php/";

if (is_dir($phpDir)) {
    $files = scandir($phpDir);
    
    // 扫描指定目录的PHP文件（无jar）
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
            continue;
        }

        // 跳过index.php或其他不需要的文件
        if ($file === 'index.php') {
            continue;
        }

        $filename = pathinfo($file, PATHINFO_FILENAME);

        $config['sites'][] = [
            "key"          => "php_" . $filename,
            "name"         => $filename . "(PHP)",
            "type"         => 4,
            "api"          => "http://127.0.0.1:8901/php/" . $filename . ".php",
            "searchable"   => 1,
            "quickSearch"  => 1,
            "changeable"   => 0
        ];
        
        $siteCount++;
    }
} else {
    // 如果PHP目录不存在，记录日志
    error_log("PHP目录不存在: " . $phpDir);
}

// ==================
// 3. 生成 PY sites
// ==================
$pyDir = "/storage/emulated/0/江湖/py";

if (is_dir($pyDir)) {
    $pyFiles = scandir($pyDir);
    
    foreach ($pyFiles as $pyFile) {
        if (pathinfo($pyFile, PATHINFO_EXTENSION) !== 'py') {
            continue;
        }
        
        if ($pyFile === '__init__.py' || $pyFile === '__pycache__') {
            continue;
        }
        
        $pyFilename = pathinfo($pyFile, PATHINFO_FILENAME);
        
        $config['sites'][] = [
            "key"          => "py_" . $pyFilename,
            "name"         => $pyFilename . "(py)",
            "type"         => 3,
            "api"          => "http://127.0.0.1:9978/file/江湖/py/" . $pyFile,
            "searchable"   => 1,
            "quickSearch"  => 1,
            "changeable"   => 0
        ];
        
        $siteCount++;
    }
} else {
    // 如果PY目录不存在，可以记录日志或忽略
    error_log("PY目录不存在: " . $pyDir);
}

// ==================
// 4. 生成普通 JS sites（无jar）
// ==================
$jsDir = "/storage/emulated/0/江湖/quickjs";

if (is_dir($jsDir)) {
    $jsFiles = scandir($jsDir);
    
    foreach ($jsFiles as $jsFile) {
        if (pathinfo($jsFile, PATHINFO_EXTENSION) !== 'js') {
            continue;
        }
        
        // 跳过隐藏文件和目录
        if ($jsFile[0] === '.') {
            continue;
        }
        
        $jsFilename = pathinfo($jsFile, PATHINFO_FILENAME);
        
        $config['sites'][] = [
            "key"          => "js_" . $jsFilename,
            "name"         => $jsFilename . "(quickjs)",
            "type"         => 3,
            "api"          => "http://127.0.0.1:9978/file/江湖/quickjs/" . $jsFile,
            "searchable"   => 1,
            "quickSearch"  => 1,
            "filterable"   => 1
        ];
        
        $siteCount++;
    }
} else {
    // 如果JS目录不存在，可以记录日志或忽略
    error_log("QUICKJS目录不存在: " . $jsDir);
}

// ==================
// 5. 生成需要外挂jar的 JS sites (NodeJS类型) - 使用主jar
// ==================
$nodeJsDir = "/storage/emulated/0/江湖/files";

if (is_dir($nodeJsDir)) {
    $nodeJsFiles = scandir($nodeJsDir);
    
    foreach ($nodeJsFiles as $jsFile) {
        // 只处理js文件
        if (pathinfo($jsFile, PATHINFO_EXTENSION) !== 'js') {
            continue;
        }
        
        // 跳过隐藏文件和目录
        if ($jsFile[0] === '.') {
            continue;
        }
        
        $jsFilename = pathinfo($jsFile, PATHINFO_FILENAME);
        
        $config['sites'][] = [
            "key"          => "Nodejs_" . $jsFilename,
            "name"         => "(NodeJs)" . $jsFilename,
            "api"          => "csp_FileLoader",
            "type"         => "3",
            "searchable"   => 1,
            "quickSearch"  => 1,
            "filterable"   => 1,
            "ext"          => "/storage/emulated/0/江湖/files/" . $jsFile
            // 注意：这里没有jar字段，使用主jar
        ];
        
        $siteCount++;
    }
} else {
    // 如果files目录不存在，可以记录日志或忽略
    error_log("NodeJS目录不存在: " . $nodeJsDir);
}

// ==================
// 6. 生成需要外挂jar的 PHP sites - 使用主jar
// ==================
$nodePhpDir = "/storage/emulated/0/江湖/scripts";

if (is_dir($nodePhpDir)) {
    $nodePhpFiles = scandir($nodePhpDir);
    
    foreach ($nodePhpFiles as $phpFile) {
        // 只处理php文件
        if (pathinfo($phpFile, PATHINFO_EXTENSION) !== 'php') {
            continue;
        }
        
        // 跳过隐藏文件和目录
        if ($phpFile[0] === '.') {
            continue;
        }
        
        $phpFilename = pathinfo($phpFile, PATHINFO_FILENAME);
        
        $config['sites'][] = [
            "key"          => "NodePhp_" . $phpFilename,
            "name"         => "(NodePhp)" . $phpFilename,
            "api"          => "csp_FileLoader",
            "type"         => "3",
            "searchable"   => 1,
            "quickSearch"  => 1,
            "filterable"   => 1,
            "ext"          => "/storage/emulated/0/江湖/scripts/" . $phpFile
            // 注意：这里没有jar字段，使用主jar
        ];
        
        $siteCount++;
    }
} else {
    // 如果files目录不存在，可以记录日志或忽略
    error_log("NodePHP目录不存在: " . $nodePhpDir);
}

// ==================
// 7. 生成需要外挂jar的 WV JS sites (csp_WvSpider6类型) - 使用主jar
// ==================
$wvJsDir = "/storage/emulated/0/江湖/wv";

if (is_dir($wvJsDir)) {
    $wvJsFiles = scandir($wvJsDir);
    
    foreach ($wvJsFiles as $jsFile) {
        // 只处理js文件
        if (pathinfo($jsFile, PATHINFO_EXTENSION) !== 'js') {
            continue;
        }
        
        // 跳过隐藏文件和目录
        if ($jsFile[0] === '.') {
            continue;
        }
        
        $jsFilename = pathinfo($jsFile, PATHINFO_FILENAME);
        
        $config['sites'][] = [
            "key"          => $jsFilename . "wv_",
            "name"         => $jsFilename . "(wv)",
            "api"          => "csp_WvSpider6",
            "type"         => "3",
            "ext"          => "http://127.0.0.1:9978/file/江湖/wv/" . $jsFile,
            "searchable"   => 1,
            "filterable"   => 1,
            "switchable"   => 1
            // 注意：这里没有jar字段，使用主jar
        ];
        
        $siteCount++;
    }
} else {
    // 如果wv目录不存在，可以记录日志或忽略
    error_log("WV JS目录不存在: " . $wvJsDir);
}

// ==================
// 8. 生成需要外挂jar的 XBPQ JSON sites (csp_XBPQ类型) - 使用主jar
// ==================
$xbpqDir = "/storage/emulated/0/江湖/xbpq";

if (is_dir($xbpqDir)) {
    $xbpqFiles = scandir($xbpqDir);
    
    foreach ($xbpqFiles as $jsonFile) {
        // 只处理json文件
        if (pathinfo($jsonFile, PATHINFO_EXTENSION) !== 'json') {
            continue;
        }
        
        // 跳过隐藏文件和目录
        if ($jsonFile[0] === '.') {
            continue;
        }
        
        $jsonFilename = pathinfo($jsonFile, PATHINFO_FILENAME);
        
        $config['sites'][] = [
            "key"          => $jsonFilename . "xbpq_",
            "name"         => $jsonFilename . "(xbpq)",
            "type"         => 3,
            "api"          => "csp_XBPQ",
            "searchable"   => 1,
            "quickSearch"  => 1,
            "filterable"   => 1,
            "ext"          => "http://127.0.0.1:9978/file/江湖/xbpq/" . $jsonFile
            // 注意：这里没有jar字段，使用主jar
        ];
        
        $siteCount++;
    }
} else {
    // 如果xbpq目录不存在，可以记录日志或忽略
    error_log("XBPQ目录不存在: " . $xbpqDir);
}

// ==================
// 9. 生成需要外挂jar的 XYQ JSON sites (csp_XYQHiker类型) - 使用主jar
// ==================
$xyqDir = "/storage/emulated/0/江湖/xyq";

if (is_dir($xyqDir)) {
    $xyqFiles = scandir($xyqDir);
    
    foreach ($xyqFiles as $jsonFile) {
        // 只处理json文件
        if (pathinfo($jsonFile, PATHINFO_EXTENSION) !== 'json') {
            continue;
        }
        
        // 跳过隐藏文件和目录
        if ($jsonFile[0] === '.') {
            continue;
        }
        
        $jsonFilename = pathinfo($jsonFile, PATHINFO_FILENAME);
        
        $config['sites'][] = [
            "key"          => $jsonFilename . "xyq_",
            "name"         => $jsonFilename . "(xyq)",
            "type"         => 3,
            "api"          => "csp_XYQHiker",
            "searchable"   => 1,
            "quickSearch"  => 1,
            "filterable"   => 1,
            "ext"          => "http://127.0.0.1:9978/file/江湖/xyq/" . $jsonFile
            // 注意：这里没有jar字段，使用主jar
        ];
        
        $siteCount++;
    }
} else {
    // 如果xyq目录不存在，可以记录日志或忽略
    error_log("XYQ目录不存在: " . $xyqDir);
}

// ==================
// 10. 更新notice字段，显示站点总数
// ==================
$config['notice'] = "提示：共生成" . $siteCount . "个站源";

// ==================
// 11. 定义多个远程配置URL
// ==================
$remoteConfigUrls = [
    "http://127.0.0.1:5757/config/1",
    // 可以添加OK接口，pg接口，各大屌接口
    // 可以添加更多远程配置URL
    // "http://example.com/config/2",
    // "http://example.com/config/3",
];

// ==================
// 12. 加载多个远程配置并合并（修改关键部分）
// ==================
$remoteSiteCount = 0;
$remoteSites = [];
$allRemoteConfigs = [];
$remoteMainJars = []; // 存储每个远程配置的主jar

foreach ($remoteConfigUrls as $index => $remoteConfigUrl) {
    // 使用 cURL 获取远程配置
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remoteConfigUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10秒超时
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // 最大重定向次数

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 检查是否成功获取远程配置
    if ($response && $httpCode === 200) {
        $json = json_decode($response, true);
        
        // JSON 合法并且是数组
        if (is_array($json)) {
            $allRemoteConfigs[] = $json;
            
            // 保存远程配置的主jar
            if (isset($json['spider'])) {
                $remoteMainJars[$index] = $json['spider'];
            }
            
            // 收集远程站点
            if (isset($json['sites']) && is_array($json['sites'])) {
                foreach ($json['sites'] as $remoteSite) {
                    $remoteSites[] = [
                        'site' => $remoteSite,
                        'config_index' => $index // 记录站点来自哪个远程配置
                    ];
                }
            }
        }
    }
}

// 如果有远程配置
if (!empty($allRemoteConfigs)) {
    // 1. 创建本地固定站点唯一标识映射
    $localSiteMap = [];
    foreach ($config['sites'] as $site) {
        if (isset($site['api']) && isset($site['name'])) {
            $key = $site['api'] . '|' . $site['name'];
            $localSiteMap[$key] = $site;
        }
    }
    
    // 2. 去重处理远程站点，并处理jar字段
    $uniqueRemoteSites = [];
    $remoteSiteMap = []; // 用于远程站点内部去重
    
    foreach ($remoteSites as $remoteItem) {
        $remoteSite = $remoteItem['site'];
        $configIndex = $remoteItem['config_index'];
        
        if (isset($remoteSite['api']) && isset($remoteSite['name'])) {
            $key = $remoteSite['api'] . '|' . $remoteSite['name'];
            
            // 如果远程站点与本地固定站点重复，跳过（使用本地配置）
            if (isset($localSiteMap[$key])) {
                continue;
            }
            
            // 如果远程站点在远程配置中重复，跳过（只保留第一个）
            if (isset($remoteSiteMap[$key])) {
                continue;
            }
            
            // 重要：jar字段处理逻辑
            // 如果远程站点有自己的jar字段，保留它
            // 如果远程站点没有jar字段，但对应的远程配置有主jar，则添加主jar
            if (!isset($remoteSite['jar']) && isset($remoteMainJars[$configIndex])) {
                $remoteSite['jar'] = $remoteMainJars[$configIndex];
            }
            // 如果远程站点没有jar字段，且远程配置也没有主jar，则不添加jar字段
            // （这样的站点会使用本地主jar或其他默认jar）
            
            $uniqueRemoteSites[] = $remoteSite;
            $remoteSiteMap[$key] = $remoteSite;
            $remoteSiteCount++;
        }
    }
    
    // 3. 合并站点：本地站点 + 去重后的远程站点
    $config['sites'] = array_merge($config['sites'], $uniqueRemoteSites);
    
    // 4. 合并其他配置（但本地固定配置优先）
    foreach ($allRemoteConfigs as $remoteConfig) {
        foreach ($remoteConfig as $key => $value) {
            // 跳过sites和spider，sites已经处理，spider保留本地
            if (!in_array($key, ['headers', 'lives', 'parses', 'sites', 'spider']) && !isset($config[$key])) {
                $config[$key] = $value;
            }
        }
    }
    
    // 5. 更新notice字段
    $totalSiteCount = $siteCount + $remoteSiteCount;
    $config['notice'] = "提示：共生成" . $totalSiteCount . "个站源（本地" . $siteCount . "个 + 去重后远程" . $remoteSiteCount . "个）";
}

// ==================
// 13. 输出最终配置
// ==================
echo json_encode(
    $config,
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);