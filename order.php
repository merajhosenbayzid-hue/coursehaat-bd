<?php
require __DIR__ . '/config.php';

function fail($msg) {
    http_response_code(422);
    echo '<!DOCTYPE html><html lang="bn"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>আবার চেষ্টা করুন — CourseHaat BD</title><style>body{font-family:system-ui;background:#08131F;color:#fff;text-align:center;padding:60px 20px}a{color:#2DD4BF;font-weight:700}</style></head><body>';
    echo '<h2>⚠️ ' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</h2>';
    echo '<p><a href="index.php">← ফিরে গিয়ে আবার চেষ্টা করুন</a></p>';
    echo '</body></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$courseId = $_POST['course'] ?? '';
$method   = $_POST['method'] ?? '';
$name     = trim($_POST['name'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$trx      = strtoupper(trim($_POST['trx'] ?? ''));

$course = null;
foreach ($COURSES as $c) { if ($c['id'] === $courseId) { $course = $c; break; } }
if (!$course) fail('কোর্সটি পাওয়া যায়নি।');
if (!in_array($method, ['bkash', 'nagad'], true)) fail('পেমেন্ট মাধ্যম বেছে নিন।');
if (mb_strlen($name) < 3) fail('আপনার সঠিক নাম লিখুন।');
if (!preg_match('/^01\d{9}$/', $phone)) fail('সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন।');
if (strlen($trx) < 6) fail('সঠিক TrxID দিন।');

// ডুপ্লিকেট TrxID আটকানো + অর্ডার লগ (সার্ভার-সাইডে, ওয়েব-রুটের বাইরে রাখা হয়)
$ordersDir = getenv('ORDERS_DIR') ?: (__DIR__ . '/data');
$logFile = rtrim($ordersDir, '/') . '/orders.csv';
if (is_file($logFile)) {
    foreach (file($logFile, FILE_IGNORE_NEW_LINES) as $line) {
        $parts = str_getcsv($line);
        if (isset($parts[5]) && strtoupper($parts[5]) === $trx) {
            fail('এই TrxID দিয়ে আগেই অর্ডার করা হয়েছে। সমস্যা হলে Telegram গ্রুপে জানান।');
        }
    }
}
$row = [date('c'), $course['id'], $name, $phone, $method, $trx, $course['price']];
$fh = fopen($logFile, 'a');
if ($fh) { fputcsv($fh, $row); fclose($fh); }

$payNum = $method === 'bkash' ? $SITE['bkash'] : $SITE['nagad'];
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>অর্ডার সফল — CourseHaat BD</title>
<style>
 body{font-family:'Hind Siliguri',system-ui,sans-serif;background:#08131F;color:#fff;text-align:center;padding:56px 20px}
 .card{max-width:520px;margin:0 auto;background:linear-gradient(160deg,#0D2436,#081624);border:1px solid rgba(45,212,191,.3);border-radius:20px;padding:34px 26px}
 .big{font-size:52px}
 h1{font-size:26px;margin:10px 0}
 p{color:#CBD5E1;line-height:1.75}
 .join{display:inline-block;background:#229ed9;color:#fff;text-decoration:none;font-weight:800;padding:15px 30px;border-radius:12px;font-size:19px;margin-top:16px}
 .warn{font-size:14px;color:#FCD9B8}
</style>
</head>
<body>
<div class="card">
  <div class="big">🎉</div>
  <h1>অভিনন্দন, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>!</h1>
  <p><b><?= htmlspecialchars($course['name'], ENT_QUOTES, 'UTF-8') ?></b> কোর্সের অর্ডার পেয়ে গেছি।<br>
  পেমেন্ট: <?= $method === 'bkash' ? 'bKash' : 'Nagad' ?> · TrxID: <?= htmlspecialchars($trx, ENT_QUOTES, 'UTF-8') ?><br>
  পেমেন্ট যাচাই হলেই তোমার অ্যাক্সেস চালু থাকবে। নিচের বাটনে চাপ দিয়ে প্রাইভেট Telegram গ্রুপে জয়েন করো:</p>
  <?php if (!empty($SITE['telegram'])): ?>
  <a class="join" href="<?= htmlspecialchars($SITE['telegram'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Telegram গ্রুপে জয়েন করুন</a>
  <?php else: ?>
  <p><b>পেমেন্ট যাচাই শেষে তোমার মোবাইলে Telegram জয়েন লিংক পাঠিয়ে দেওয়া হবে।</b></p>
  <?php endif; ?>
  <p class="warn" style="margin-top:16px">⚠️ লিংকটা কারো সাথে শেয়ার করো না — শেয়ার করলে অ্যাক্সেস বন্ধ হয়ে যাবে।</p>
</div>
</body>
</html>
