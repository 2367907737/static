<?php
// 设置返回为 JSON
// http://127.0.0.1:9980/config.php
header('Content-Type: application/json; charset=utf-8');

// 当前目录
$dir = __DIR__;

// 当前脚本名
$self = basename(__FILE__);

// 扫描目录
$files = scandir($dir);

$sites = [];

foreach ($files as $file) {
    // 只处理 php 文件
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
        continue;
    }

    // 排除自身和 index.php
    if ($file === $self || $file === 'index.php') {
        continue;
    }

    // 文件名（不含 .php）
    $filename = pathinfo($file, PATHINFO_FILENAME);

    $sites[] = [
        "key"          => "php_" . $filename,
        "name"         => $filename . "(PHP)",
        "type"         => 4,
        "api"          => "http://127.0.0.1:9980/" . $filename . ".php",
        "searchable"   => 1,
        "quickSearch"  => 1,
        "changeable"   => 0
    ];
}

// 输出 JSON
echo json_encode(
    ["sites" => $sites],
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);

