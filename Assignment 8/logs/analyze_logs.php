<?php
/* Analyze Apache access + error logs and render stats + charts.
 * Drop-in server-side script. Print page to PDF for submission.
 * Author: Angela/Anxhela’s helper 💙
 */

ini_set('memory_limit', '1024M');
date_default_timezone_set('Europe/Berlin'); // adjust if needed

// ---------- CONFIG ----------
$defaultAccessCandidates = [
    '/var/log/apache2/access.log',
    '/var/log/apache2/access.log.1',
    '/var/log/httpd/access_log',
    getenv('HOME').'/access.log',
    getenv('HOME').'/logs/access.log',
];
$defaultErrorCandidates = [
    '/var/log/apache2/error.log',
    '/var/log/apache2/error.log.1',
    '/var/log/httpd/error_log',
    getenv('HOME').'/error.log',
    getenv('HOME').'/logs/error.log',
];

$accessPath = isset($_GET['access']) ? expandHome($_GET['access']) : firstReadable($defaultAccessCandidates);
$errorPath  = isset($_GET['error'])  ? expandHome($_GET['error'])  : firstReadable($defaultErrorCandidates);

$fromDate = isset($_GET['from']) ? $_GET['from'] : '';
$toDate   = isset($_GET['to'])   ? $_GET['to']   : '';
$bucket   = isset($_GET['bucket']) ? $_GET['bucket'] : 'day'; // 'day' or 'hour'

// ---------- Helpers ----------
function expandHome($p) {
    if ($p === null) return null;
    $home = getenv('HOME');
    return preg_replace('#^~#', $home, $p);
}
function firstReadable($cands) {
    foreach ($cands as $p) if (is_readable($p)) return $p;
    return null;
}
function openMaybeGz($path) {
    if (!$path) return null;
    if (preg_match('/\.gz$/', $path)) {
        if (function_exists('gzopen')) return gzopen($path, 'r');
        return null;
    }
    return fopen($path, 'r');
}
function fgetsAny($fh) {
    if (!$fh) return false;
    if (get_resource_type($fh) === 'stream') return fgets($fh);
    // gz
    return gzgets($fh);
}
function fcloseAny($fh) {
    if (!$fh) return;
    $t = get_resource_type($fh);
    if ($t === 'stream') fclose($fh); else gzclose($fh);
}
function parseApacheDate($s) {
    // Example: 10/Oct/2000:13:55:36 -0700
    $dt = DateTime::createFromFormat('d/M/Y:H:i:s O', $s);
    return $dt ?: null;
}
function parseErrorDate($s) {
    // Example: [Tue Oct 11 14:32:52.123456 2025]
    // We’ll accept fractional seconds optional.
    $s = trim($s, '[]');
    // Remove weekday if present
    $s = preg_replace('/^[A-Za-z]{3}\s+/', '', $s);
    // Normalize microseconds (optional)
    $s = preg_replace('/\.(\d+)\s+/', ' ', $s); // drop micros to keep simple
    $dt = DateTime::createFromFormat('M d H:i:s Y', $s);
    if (!$dt) {
        // Some distros use two-spaces padding for day <10; try a fallback
        $dt = DateTime::createFromFormat('M  d H:i:s Y', $s);
    }
    return $dt ?: null;
}
function uaToBrowser($ua) {
    $ua = strtolower($ua);
    if ($ua === '-' || $ua === '') return 'Other';
    if (strpos($ua, 'bot') !== false || strpos($ua, 'spider') !== false || strpos($ua, 'crawler') !== false) return 'Bots';
    if (strpos($ua, 'edg') !== false) return 'Edge';
    if (strpos($ua, 'opr') !== false || strpos($ua, 'opera') !== false) return 'Opera';
    if (strpos($ua, 'chrome') !== false && strpos($ua, 'chromium') === false && strpos($ua, 'edg') === false) return 'Chrome';
    if (strpos($ua, 'safari') !== false && strpos($ua, 'chrome') === false) return 'Safari';
    if (strpos($ua, 'firefox') !== false) return 'Firefox';
    return 'Other';
}
function bucketKey(DateTime $dt, $bucket) {
    if ($bucket === 'hour') return $dt->format('Y-m-d H:00');
    return $dt->format('Y-m-d');
}
function withinRange(?DateTime $dt, $from, $to) {
    if (!$dt) return false;
    if ($from && $dt < new DateTime($from.' 00:00:00')) return false;
    if ($to && $dt > new DateTime($to.' 23:59:59')) return false;
    return true;
}
function csvDownload($name, $rows, $headers) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$name.'.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);
    foreach ($rows as $r) fputcsv($out, $r);
    fclose($out); exit;
}

// ---------- Parse access log (Combined Log Format) ----------
$accessStats = [
    'byPage' => [],           // page => hits
    'byPageUniqueIPs' => [],  // page => set[ip]
    'byIP' => [],             // ip => hits
    'timeline' => [],         // bucket => hits
    'browsers' => [],         // browser => hits
    'status' => [],           // status => hits
];
$accessRows = []; // for CSV

if ($accessPath && is_readable($accessPath)) {
    $fh = openMaybeGz($accessPath);
    if ($fh) {
        while (($line = fgetsAny($fh)) !== false) {
            // CLF with quotes:
            // 1.2.3.4 - - [10/Oct/2000:13:55:36 -0700] "GET /index.html HTTP/1.1" 200 2326 "ref" "ua"
            if (!preg_match('#^(\S+) \S+ \S+ \[([^\]]+)\] "([A-Z]+) ([^"]*?) HTTP/[^"]+" (\d{3}) \S+ "([^"]*)" "([^"]*)"#', $line, $m)) {
                continue;
            }
            $ip = $m[1];
            $dt = parseApacheDate($m[2]);
            if (!withinRange($dt, $fromDate, $toDate)) continue;
            $method = $m[3];
            $path   = $m[4] ?: '/';
            $status = $m[5];
            $ua     = $m[7];

            $page = preg_replace('#\?.*$#', '', $path); // drop query for grouping
            $bucketKey = bucketKey($dt, $bucket);
            $browser = uaToBrowser($ua);

            // aggregate
            $accessStats['byPage'][$page] = ($accessStats['byPage'][$page] ?? 0) + 1;
            $accessStats['byPageUniqueIPs'][$page][$ip] = true;
            $accessStats['byIP'][$ip] = ($accessStats['byIP'][$ip] ?? 0) + 1;
            $accessStats['timeline'][$bucketKey] = ($accessStats['timeline'][$bucketKey] ?? 0) + 1;
            $accessStats['browsers'][$browser] = ($accessStats['browsers'][$browser] ?? 0) + 1;
            $accessStats['status'][$status] = ($accessStats['status'][$status] ?? 0) + 1;

            $accessRows[] = [$dt->format('Y-m-d H:i:s'), $ip, $method, $path, $status, $browser];
        }
        fcloseAny($fh);
    }
}

// ---------- Parse error log ----------
$errorStats = [
    'byLevel' => [],     // error level keyword counts (error, warn, notice, etc.)
    'byIP' => [],        // ip => count
    'timeline' => [],    // bucket => count
];
$errorRows = [];

if ($errorPath && is_readable($errorPath)) {
    $fh = openMaybeGz($errorPath);
    if ($fh) {
        while (($line = fgetsAny($fh)) !== false) {
            // Example:
            // [Tue Oct 11 14:32:52.123456 2025] [core:error] [pid 123] [client 1.2.3.4:54321] AH... message
            if (!preg_match('#^\[([^\]]+)\]\s+\[[^\]]*?(error|warn|notice|info|debug)[^\]]*\](?:.*?\[client ([^\]:]+)(?::\d+)?\])?#i', $line, $m)) {
                continue;
            }
            $dt = parseErrorDate($m[1]);
            if (!withinRange($dt, $fromDate, $toDate)) continue;
            $level = strtolower($m[2]);
            $ip = isset($m[3]) && $m[3] ? $m[3] : 'unknown';

            $errorStats['byLevel'][$level] = ($errorStats['byLevel'][$level] ?? 0) + 1;
            $errorStats['byIP'][$ip] = ($errorStats['byIP'][$ip] ?? 0) + 1;
            $errorStats['timeline'][bucketKey($dt, $bucket)] = ($errorStats['timeline'][bucketKey($dt, $bucket)] ?? 0) + 1;

            $errorRows[] = [$dt->format('Y-m-d H:i:s'), $level, $ip, trim($line)];
        }
        fcloseAny($fh);
    }
}

// ---------- CSV downloads ----------
if (isset($_GET['csv'])) {
    $which = $_GET['csv'];
    if ($which === 'access') csvDownload('access_rows', $accessRows, ['timestamp','ip','method','path','status','browser']);
    if ($which === 'errors') csvDownload('error_rows', $errorRows, ['timestamp','level','ip','line']);
    if ($which === 'pages') {
        $rows = [];
        foreach ($accessStats['byPage'] as $p=>$c) {
            $uniq = isset($accessStats['byPageUniqueIPs'][$p]) ? count($accessStats['byPageUniqueIPs'][$p]) : 0;
            $rows[] = [$p, $c, $uniq];
        }
        csvDownload('pages', $rows, ['page','hits','unique_ips']);
    }
    if ($which === 'ips') {
        $rows = [];
        foreach ($accessStats['byIP'] as $ip=>$c) $rows[] = [$ip,$c];
        csvDownload('ips', $rows, ['ip','hits']);
    }
    if ($which === 'errors_by_ip') {
        $rows = [];
        foreach ($errorStats['byIP'] as $ip=>$c) $rows[] = [$ip,$c];
        csvDownload('errors_by_ip', $rows, ['ip','errors']);
    }
    exit;
}

// ---------- Prepare chart data ----------
ksort($accessStats['timeline']);
ksort($errorStats['timeline']);

function topN($arr, $n=15) {
    arsort($arr);
    return array_slice($arr, 0, $n, true);
}

$topPages = topN($accessStats['byPage'] ?? [], 20);
$topIPs   = topN($accessStats['byIP'] ?? [], 15);
$topErrIPs= topN($errorStats['byIP'] ?? [], 15);

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// ---------- HTML ----------
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Web Log Evaluation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Inter,Arial,sans-serif;margin:24px;line-height:1.35;}
    h1,h2{margin:8px 0;}
    .grid{display:grid;gap:16px;}
    .cols-2{grid-template-columns:repeat(2,minmax(0,1fr));}
    .card{border:1px solid #eee;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.04);}
    table{width:100%;border-collapse:collapse;margin-top:8px;font-size:14px}
    th,td{padding:8px;border-bottom:1px solid #eee;text-align:left;vertical-align:top;}
    th{background:#fafafa;}
    .muted{color:#666;font-size:13px}
    .controls{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:8px}
    input,select{padding:6px 8px;border:1px solid #ddd;border-radius:8px}
    .btn{padding:6px 10px;border:1px solid #ddd;border-radius:8px;text-decoration:none;color:#111;background:#f7f7f7}
    .btn:hover{background:#eee}
    @media print {.controls,.btn{display:none !important;}}
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
</head>
<body>
  <h1>Web Log Evaluation</h1>
  <p class="muted">
    Source access log: <b><?=h($accessPath ?: 'not found')?></b> |
    Error log: <b><?=h($errorPath ?: 'not found')?></b>
  </p>

  <form class="controls" method="get">
    <label>Access: <input name="access" size="40" value="<?=h($accessPath)?>"></label>
    <label>Error: <input name="error" size="40" value="<?=h($errorPath)?>"></label>
    <label>From: <input type="date" name="from" value="<?=h($fromDate)?>"></label>
    <label>To: <input type="date" name="to" value="<?=h($toDate)?>"></label>
    <label>Bucket:
      <select name="bucket">
        <option value="day" <?= $bucket==='day'?'selected':''?>>day</option>
        <option value="hour" <?= $bucket==='hour'?'selected':''?>>hour</option>
      </select>
    </label>
    <button class="btn" type="submit">Apply</button>
    <a class="btn" href="?<?=http_build_query(array_merge($_GET,['csv'=>'pages']))?>">Download CSVs: Pages</a>
    <a class="btn" href="?<?=http_build_query(array_merge($_GET,['csv'=>'ips']))?>">IPs</a>
    <a class="btn" href="?<?=http_build_query(array_merge($_GET,['csv'=>'access']))?>">Access Rows</a>
    <a class="btn" href="?<?=http_build_query(array_merge($_GET,['csv'=>'errors']))?>">Error Rows</a>
    <a class="btn" href="?<?=http_build_query(array_merge($_GET,['csv'=>'errors_by_ip']))?>">Errors by IP</a>
  </form>

  <div class="grid cols-2">
    <div class="card">
      <h2>Traffic timeline (<?=h($bucket)?>)</h2>
      <canvas id="traffic"></canvas>
    </div>
    <div class="card">
      <h2>Error timeline (<?=h($bucket)?>)</h2>
      <canvas id="errors"></canvas>
    </div>
    <div class="card">
      <h2>Browser distribution</h2>
      <canvas id="browsers"></canvas>
      <p class="muted">Bots are detected by common keywords (“bot”, “spider”, “crawler”).</p>
    </div>
    <div class="card">
      <h2>HTTP status codes</h2>
      <canvas id="status"></canvas>
    </div>
  </div>

  <div class="grid" style="margin-top:16px">
    <div class="card">
      <h2>Top pages</h2>
      <table>
        <tr><th>Page</th><th>Hits</th><th>Unique IPs</th></tr>
        <?php foreach ($topPages as $p=>$c): ?>
          <tr>
            <td><?=h($p)?></td>
            <td><?=h($c)?></td>
            <td><?= isset($accessStats['byPageUniqueIPs'][$p]) ? count($accessStats['byPageUniqueIPs'][$p]) : 0 ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="card">
      <h2>Top visitor IPs</h2>
      <table>
        <tr><th>IP</th><th>Hits</th></tr>
        <?php foreach ($topIPs as $ip=>$c): ?>
          <tr><td><?=h($ip)?></td><td><?=h($c)?></td></tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="card">
      <h2>Errors by IP</h2>
      <table>
        <tr><th>IP</th><th>Errors</th></tr>
        <?php foreach ($topErrIPs as $ip=>$c): ?>
          <tr><td><?=h($ip)?></td><td><?=h($c)?></td></tr>
        <?php endforeach; ?>
      </table>
    </div>

    <div class="card">
      <h2>Error levels</h2>
      <table>
        <tr><th>Level</th><th>Count</th></tr>
        <?php foreach (topN($errorStats['byLevel'] ?? [], 10) as $lvl=>$c): ?>
          <tr><td><?=h($lvl)?></td><td><?=h($c)?></td></tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>

  <script>
    const trafficLabels = <?=json_encode(array_keys($accessStats['timeline'] ?? []))?>;
    const trafficData   = <?=json_encode(array_values($accessStats['timeline'] ?? []))?>;

    const errorLabels   = <?=json_encode(array_keys($errorStats['timeline'] ?? []))?>;
    const errorData     = <?=json_encode(array_values($errorStats['timeline'] ?? []))?>;

    const browserLabels = <?=json_encode(array_keys($accessStats['browsers'] ?? []))?>;
    const browserData   = <?=json_encode(array_values($accessStats['browsers'] ?? []))?>;

    const statusLabels  = <?=json_encode(array_keys($accessStats['status'] ?? []))?>;
    const statusData    = <?=json_encode(array_values($accessStats['status'] ?? []))?>;

    function lineChart(id, labels, data) {
      new Chart(document.getElementById(id), {
        type: 'line',
        data: { labels, datasets: [{ label: id, data, fill:false, tension:0.2 }]},
        options: { responsive:true, plugins:{ legend:{ display:false }}, scales:{ x:{ ticks:{ autoSkip:true }}} }
      });
    }
    function pieChart(id, labels, data) {
      new Chart(document.getElementById(id), {
        type: 'pie',
        data: { labels, datasets: [{ data }]},
        options: { responsive:true }
      });
    }
    function barChart(id, labels, data) {
      new Chart(document.getElementById(id), {
        type: 'bar',
        data: { labels, datasets: [{ data }]},
        options: { responsive:true, plugins:{ legend:{ display:false }}} 
      });
    }

    lineChart('traffic', trafficLabels, trafficData);
    lineChart('errors', errorLabels, errorData);
    pieChart('browsers', browserLabels, browserData);
    barChart('status', statusLabels, statusData);
  </script>

 
</body>
</html>
