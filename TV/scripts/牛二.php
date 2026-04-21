<?php
// 设置时区为上海（东八区）
date_default_timezone_set('Asia/Shanghai');

// 快速排序.php - TVBox 站点排序删除管理（最终纯净版）
// ✅ 左滑 >1/3 宽度 → 自动删除（无确认）
// ✅ 删除后立即上移
// ✅ 重置 = 删除缓存 + 重新拉取
// ✅ 保存后自动刷新
// ✅ 来源：天机阁
// ✅ 时间：北京时间（东八区）

header('Content-Type: text/html; charset=utf-8');

// ========== 处理 ?reset=1 ==========
if (isset($_GET['reset']) && $_GET['reset'] === '1') {
    @unlink(__DIR__ . '/cache/niu.json');
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// ========== 配置 ==========
$urls = [
    "https://9280.kstore.vip/newwex.json",
];
$cache_file = __DIR__ . '/cache/niu.json';
$cache_ttl = 300;

// 创建缓存目录
if (!is_dir(dirname($cache_file))) {
    @mkdir(dirname($cache_file), 0755, true);
}

// ========== 辅助函数 ==========
function fetch_json($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => trim($url),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 1.5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'okhttp/3.15',
    ]);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);    
    if ($code >= 200 && $code < 400 && $data) {
        $json = json_decode($data, true);
        if (json_last_error() === JSON_ERROR_NONE && !empty($json['sites'])) {
            return $json;
        }
    }
    return null;
}

function get_sites_data() {
    global $urls, $cache_file, $cache_ttl;
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) <= $cache_ttl) {
        $data = json_decode(file_get_contents($cache_file), true);
        if (json_last_error() === JSON_ERROR_NONE && !empty($data['sites'])) {
            return $data;
        }
    }
    
    foreach ($urls as $i => $url) {
        if ($data = fetch_json($url)) {
            $data['_source'] = '天机阁';
            $data['_fetched_at'] = date('Y-m-d H:i:s');
            file_put_contents($cache_file, json_encode($data, JSON_UNESCAPED_UNICODE));
            return $data;
        }
    }
    
    return ['sites' => [], '_source' => '天机阁', '_fetched_at' => date('Y-m-d H:i:s')];
}

// ========== UA 检测：TVBox 直接返回 JSON ==========
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (stripos($ua, 'okhttp') !== false || stripos($ua, 'TVBox') !== false || stripos($ua, 'FongMi') !== false) {
    $data = get_sites_data();
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// ========== 保存处理 ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    $order = json_decode($_POST['order'], true);
    $all_sites = json_decode($_POST['all_sites'], true);
    
    if (!$order || !$all_sites) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '❌ 数据格式错误']);
        exit;    }
    
    $new_sites = [];
    foreach ($order as $key) {
        if (isset($all_sites[$key])) {
            $new_sites[] = $all_sites[$key];
        }
    }
    
    $original_data = get_sites_data();
    $output = array_merge($original_data, [
        'sites' => $new_sites,
        '_custom_sorted' => true,
        '_sorted_at' => date('Y-m-d H:i:s'),
        '_source' => '天机阁'
    ]);
    
    if (file_put_contents($cache_file, json_encode($output, JSON_UNESCAPED_UNICODE)) !== false) {
        echo json_encode(['success' => true, 'message' => '✅ 保存成功！' . count($new_sites) . '个站点已更新']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '❌ 保存失败！检查 cache 目录权限']);
    }
    exit;
}

// 获取数据
$data = get_sites_data();
$sites = $data['sites'] ?? [];
$source_info = $data['_source'] ?? '天机阁';
$fetched_at = $data['_fetched_at'] ?? date('Y-m-d H:i:s');

$sites_map = [];
foreach ($sites as $site) {
    $key = $site['key'] ?? md5(json_encode($site));
    $sites_map[$key] = $site;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>📺 TVBox 站点排序删除管理</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;-webkit-tap-highlight-color:transparent}
        body{font-family:-apple-system,BlinkMacSystemFont,"PingFang SC","Microsoft YaHei",sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#333;padding:env(safe-area-inset-top) env(safe-area-inset-right) 90px env(safe-area-inset-left);min-height:100vh}
        .header{background:white;padding:15px 20px;text-align:center;box-shadow:0 2px 15px rgba(0,0,0,0.1);position:sticky;top:0;z-index:100;border-radius:0 0 16px 16px}
        .header h1{font-size:22px;font-weight:700;color:#4361ee;display:flex;align-items:center;justify-content:center;gap:8px}
        .stats{display:flex;justify-content:space-around;margin-top:8px;font-size:13px;color:#64748b;background:#f8fafc;border-radius:12px;padding:8px 0}        .list-container{background:white;border-radius:20px;margin:15px;box-shadow:0 5px 25px rgba(0,0,0,0.12);max-height:calc(100vh - 230px);overflow-y:auto}
        .list-header{padding:14px 20px;background:linear-gradient(to right,#4361ee,#3a0ca3);color:white;font-weight:600;font-size:16px;border-radius:18px 18px 0 0;display:flex;justify-content:space-between;align-items:center}
        .item{position:relative;display:flex;align-items:center;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fff;touch-action:pan-y;overflow:hidden;cursor:grab}
        .item:last-child{border-bottom:none}
        .item-content{flex:1;min-width:0;padding-right:10px}
        .name{font-size:17px;color:#1e293b;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .item-delete{position:absolute;right:-60px;top:0;height:100%;width:60px;background:linear-gradient(135deg,#ff6b6b,#ee5a5a);color:white;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;z-index:10;transition:right 0.2s ease;pointer-events:none}
        .action-bar{position:fixed;bottom:0;left:0;right:0;background:white;padding:12px 15px env(safe-area-inset-bottom);box-shadow:0 -3px 20px rgba(0,0,0,0.15);display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;z-index:200}
        .main-btn{height:52px;border-radius:14px;border:none;font-size:17px;font-weight:600;color:white;display:flex;align-items:center;justify-content:center;gap:6px;box-shadow:0 3px 12px rgba(0,0,0,0.15);touch-action:manipulation;transition:all 0.2s}
        .save-btn{background:linear-gradient(to right,#4361ee,#3a0ca3)}
        .refresh-btn{background:linear-gradient(to right,#4cc9f0,#4361ee)}
        .restore-btn{background:linear-gradient(to right,#7209b7,#480ca8)}
        .toast{position:fixed;bottom:90px;left:50%;transform:translateX(-50%) translateY(100px);background:white;color:#1e293b;padding:14px 28px;border-radius:50px;box-shadow:0 5px 25px rgba(0,0,0,0.25);font-size:17px;font-weight:500;z-index:1000;opacity:0;transition:all 0.3s cubic-bezier(0.23,1,0.32,1);text-align:center;max-width:85%}
        .toast.show{transform:translateX(-50%) translateY(0);opacity:1}
        .toast.success{background:linear-gradient(to right,#10b981,#0da271);color:white}
        .toast.error{background:linear-gradient(to right,#ef4444,#dc2626);color:white}
        .empty-state{text-align:center;padding:40px 20px;color:#94a3b8}
    </style>
</head>
<body>
    <div class="header">
        <h1>📺 TVBox站点排序删除管理</h1>
        <div class="stats">
            <div>📍 来源: <strong><?= htmlspecialchars($source_info) ?></strong></div>
            <div>⏰ <?= date('H:i') ?></div> <!-- 使用当前北京时间 -->
            <div>📊 <strong id="count"><?= count($sites) ?></strong>个</div>
        </div>
    </div>
    
    <div class="list-container">
        <div class="list-header">
            <span>↑ 拖拽排序左滑删除 (<span id="visible-count"><?= count($sites) ?></span>)</span>
            <span style="background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:10px;font-size:13px">URL#1 → URL#2</span>
        </div>
        <div id="list">
            <?php if (empty($sites)): ?>
                <div class="empty-state">
                    <div style="font-size:48px;margin-bottom:10px">📭</div>
                    <div>暂无站点数据</div>
                    <div>点击【重置】获取最新数据</div>
                </div>
            <?php else: foreach ($sites as $site): 
                $key = htmlspecialchars($site['key'] ?? '');
                $name = htmlspecialchars($site['name'] ?? $key);
            ?>
                <div class="item" data-key="<?= $key ?>" draggable="true">
                    <div class="item-content"><div class="name"><?= $name ?></div></div>
                    <div class="item-delete">×</div>
                </div>
            <?php endforeach; endif; ?>        </div>
    </div>
    
    <div class="action-bar">
        <button class="main-btn refresh-btn" onclick="refreshData()"><span>🔄</span> 刷新</button>
        <button class="main-btn restore-btn" onclick="restoreAll()"><span>↺</span> 重置</button>
        <button class="main-btn save-btn" onclick="saveData()"><span>💾</span> 保存</button>
    </div>
    
    <div class="toast" id="toast"></div>

    <script>
        const allSites = <?= json_encode($sites_map, JSON_UNESCAPED_UNICODE) ?>;
        const list = document.getElementById('list');

        // ========== 桌面拖拽 ==========
        document.querySelectorAll('.item').forEach(item => {
            item.addEventListener('dragstart', () => setTimeout(() => item.classList.add('dragging'), 0));
            item.addEventListener('dragend', () => item.classList.remove('dragging'));
            item.addEventListener('dragover', e => e.preventDefault());
            item.addEventListener('drop', e => {
                e.preventDefault();
                const dragged = document.querySelector('.dragging');
                if (dragged && dragged !== item) {
                    const rect = item.getBoundingClientRect();
                    const mid = rect.top + rect.height / 2;
                    if (e.clientY < mid) {
                        item.parentNode.insertBefore(dragged, item);
                    } else {
                        item.parentNode.insertBefore(dragged, item.nextSibling);
                    }
                    updateVisibleCount();
                }
            });
        });

        // ========== 手机触摸：左滑删除 + 立即上移 ==========
        let activeItem = null;
        list.addEventListener('touchstart', e => {
            const item = e.target.closest('.item');
            if (item && e.touches.length === 1) {
                activeItem = item;
                item.dataset.startX = e.touches[0].clientX;
                document.body.style.touchAction = 'none';
            }
        });

        list.addEventListener('touchmove', e => {
            if (activeItem && e.touches.length === 1) {
                const dx = e.touches[0].clientX - activeItem.dataset.startX;                if (Math.abs(dx) > 10) {
                    e.preventDefault();
                    activeItem.querySelector('.item-delete').style.right = dx < 0 ? `-${Math.min(-dx, 60)}px` : '-60px';
                }
            }
        });

        list.addEventListener('touchend', e => {
            if (activeItem) {
                const dx = e.changedTouches[0].clientX - activeItem.dataset.startX;
                if (dx < -40) {
                    const name = activeItem.querySelector('.name').textContent || '未知站点';
                    activeItem.remove();
                    updateVisibleCount();
                    // 强制重排（关键！）
                    list.parentElement.style.pointerEvents = 'none';
                    void list.parentElement.offsetHeight;
                    list.parentElement.style.pointerEvents = 'auto';
                    showToast(`🗑️ 已删除: ${name}`, 'success');
                } else {
                    activeItem.querySelector('.item-delete').style.right = '-60px';
                }
                activeItem = null;
                document.body.style.touchAction = '';
            }
        });

        // ========== 功能 ==========
        function saveData() {
            const items = document.querySelectorAll('.item');
            if (items.length === 0) return showToast('⚠️ 无站点可保存', 'error');
            
            const order = Array.from(items).map(el => el.dataset.key);
            const btn = document.querySelector('.save-btn');
            btn.innerHTML = '<span>⏳</span> 保存中...';
            btn.disabled = true;

            fetch(location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=save&order=${encodeURIComponent(JSON.stringify(order))}&all_sites=${encodeURIComponent(JSON.stringify(allSites))}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showToast(data.message, 'error');
                }            })
            .catch(err => showToast('❌ 保存失败: ' + err.message, 'error'))
            .finally(() => {
                btn.innerHTML = '<span>💾</span> 保存';
                btn.disabled = false;
            });
        }

        function refreshData() {
            if (!confirm('确定刷新？将覆盖当前编辑内容！')) return;
            location.href = location.href.split('?')[0] + '?t=' + Date.now();
        }

        function restoreAll() {
            if (!confirm('确定重置？\n将删除缓存并重新拉取最新数据！')) return;
            location.href = location.href.split('?')[0] + '?reset=1';
        }

        function updateVisibleCount() {
            const n = document.querySelectorAll('.item').length;
            document.getElementById('visible-count').textContent = n;
            document.getElementById('count').textContent = n;
        }

        function showToast(msg, type = 'success') {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className = `toast ${type} show`;
            setTimeout(() => t.classList.remove('show'), 2800);
        }
    </script>
</body>
</html>