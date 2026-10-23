<?php
// CourseHaat BD — ব্যাকএন্ড API ক্লায়েন্ট (Apps Script web app-এ কল করে)
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

function backend_url() {
    $u = getenv('BACKEND_URL');
    return $u ? trim($u) : (isset($GLOBALS['BACKEND_URL_FALLBACK']) ? $GLOBALS['BACKEND_URL_FALLBACK'] : '');
}
function backend($action, $data = []) {
    $url = backend_url();
    if (!$url) return ['ok' => false, 'error' => 'সার্ভার এখনো প্রস্তুত নয় (BACKEND_URL সেট হয়নি)।'];
    $data['action'] = $action;
    $payload = json_encode($data);
    $raw = null;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 40,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => $payload, 'timeout' => 40, 'follow_location' => 1]]);
        $raw = @file_get_contents($url, false, $ctx);
    }
    if ($raw === null || $raw === false || $raw === '') return ['ok' => false, 'error' => 'সার্ভারের সাথে যোগাযোগ ব্যর্থ হয়েছে। আবার চেষ্টা করুন।'];
    $j = json_decode($raw, true);
    if (!is_array($j)) return ['ok' => false, 'error' => 'সার্ভার থেকে অপ্রত্যাশিত উত্তর এসেছে।'];
    return $j;
}
function auth_token() { return isset($_SESSION['ch_token']) ? $_SESSION['ch_token'] : ''; }
function current_user($fresh = false) {
    static $cached = null;
    if (!$fresh && $cached !== null) return $cached;
    $t = auth_token();
    if (!$t) return $cached = null;
    $r = backend('getMe', ['token' => $t]);
    if (empty($r['ok'])) { unset($_SESSION['ch_token']); return $cached = null; }
    return $cached = $r['user'];
}
function login_with_token($token) { $_SESSION['ch_token'] = $token; }
function do_logout() {
    $t = auth_token();
    if ($t) backend('logout', ['token' => $t]);
    unset($_SESSION['ch_token']);
    session_regenerate_id(true);
}
function esc($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function site_url() {
    $u = getenv('SITE_URL');
    return $u ? rtrim($u, '/') : 'https://coursehaat-bd.onrender.com';
}
// রেফার কোড ধরা: ?ref=CODE এলে কুকিতে রাখা (সাইনআপে ব্যবহার হবে)
if (!empty($_GET['ref'])) {
    setcookie('ch_ref', preg_replace('/[^A-Za-z0-9]/', '', $_GET['ref']), time() + 30 * 24 * 3600, '/');
    $_COOKIE['ch_ref'] = preg_replace('/[^A-Za-z0-9]/', '', $_GET['ref']);
}
function saved_ref() { return isset($_COOKIE['ch_ref']) ? $_COOKIE['ch_ref'] : ''; }

function page_head($title) {
    $logo = 'assets/logo.webp';
    echo '<!DOCTYPE html><html lang="bn"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . esc($title) . ' — CourseHaat BD</title><link rel="icon" href="' . $logo . '">';
    echo '<style>
      *{margin:0;padding:0;box-sizing:border-box}
      body{font-family:\'Hind Siliguri\',\'Segoe UI\',system-ui,sans-serif;background:radial-gradient(1200px 700px at 50% -10%,#2a0a5e,#12042e 60%);color:#fff;min-height:100vh}
      .wrap{max-width:860px;margin:0 auto;padding:0 18px 50px}
      header{display:flex;align-items:center;gap:12px;padding:16px 0}
      header img{width:44px;height:44px;border-radius:11px}
      header .brand{font-size:21px;font-weight:800} header .brand span{color:#ff4fd8}
      header nav{margin-left:auto;display:flex;gap:14px;font-size:15px}
      header nav a{color:#d9cdf7;text-decoration:none;font-weight:600}
      .card{background:linear-gradient(160deg,rgba(34,16,77,.94),rgba(24,6,54,.94));border:1px solid rgba(255,79,216,.25);border-radius:18px;padding:24px;margin:16px 0}
      h1{font-size:26px;margin:10px 0 4px} h2{font-size:20px;margin:4px 0 10px}
      p{color:#d9cdf7;line-height:1.7} .muted{color:#b7a8de;font-size:14px}
      label{display:block;font-size:14px;font-weight:700;margin:14px 0 6px}
      input,select,textarea{width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,.25);background:#12042e;color:#fff;font-size:16px;font-family:inherit}
      .btn{display:inline-block;width:100%;text-align:center;background:linear-gradient(90deg,#c026d3,#e11dc0);color:#fff;border:none;border-radius:12px;padding:14px;font-size:17px;font-weight:800;cursor:pointer;font-family:inherit;margin-top:16px;text-decoration:none}
      .btn.secondary{background:#2a1152;border:1px solid rgba(255,255,255,.3)}
      .btn.small{width:auto;padding:9px 18px;font-size:15px;margin-top:8px}
      .btn.danger{background:#7f1d3a}
      .msg{border-radius:12px;padding:13px 16px;margin:14px 0;line-height:1.6}
      .msg.ok{background:rgba(34,197,94,.15);border:1px solid rgba(34,197,94,.55)}
      .msg.err{background:rgba(239,68,68,.13);border:1px solid rgba(239,68,68,.55)}
      .row{display:flex;gap:12px;flex-wrap:wrap}.row>*{flex:1;min-width:200px}
      .stat{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:16px;text-align:center}
      .stat b{font-size:24px;display:block}
      table{width:100%;border-collapse:collapse;font-size:14.5px}
      th,td{border-bottom:1px solid rgba(255,255,255,.12);padding:9px 7px;text-align:left;vertical-align:top}
      .pill{display:inline-block;padding:3px 11px;border-radius:999px;font-size:13px;font-weight:700}
      .pill.pending{background:rgba(234,179,8,.2);color:#fde047}.pill.approved{background:rgba(34,197,94,.2);color:#86efac}.pill.rejected{background:rgba(239,68,68,.2);color:#fca5a5}.pill.banned{background:rgba(239,68,68,.25);color:#fca5a5}.pill.active{background:rgba(34,197,94,.2);color:#86efac}
      .reflink{display:flex;gap:8px}.reflink input{flex:1}
      .avatar{width:74px;height:74px;border-radius:50%;object-fit:cover;background:#2a1152}
      .avatar-fallback{width:74px;height:74px;border-radius:50%;background:linear-gradient(135deg,#c026d3,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:900}
      a{color:#ff9de6}
    </style></head><body><div class="wrap"><header><img src="' . $logo . '" alt="CourseHaat BD"><div class="brand">Course<span>Haat</span> BD</div><nav><a href="index.php">হোম</a>';
    $u = current_user();
    if ($u) { echo '<a href="dashboard.php">ড্যাশবোর্ড</a><a href="logout.php">লগআউট</a>'; }
    else { echo '<a href="account.php">লগইন / সাইন আপ</a>'; }
    echo '</nav></header>';
}
function page_foot() { echo '<p class="muted" style="text-align:center;margin-top:26px">CourseHaat BD — বেকারত্ব দূর করাই মূল লক্ষ্য</p></div></body></html>'; }
