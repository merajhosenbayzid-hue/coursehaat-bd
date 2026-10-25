<?php
require __DIR__ . '/config.php';

function fail($msg) {
    http_response_code(422);
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Try Again — CourseHaat BD</title><style>body{font-family:system-ui;background:#f1f5f9;color:#0f172a;text-align:center;padding:60px 20px}a{color:#0d9488;font-weight:700}</style></head><body>';
    echo '<h2>⚠️ ' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</h2>';
    echo '<p><a href="index.php">← Go back and try again</a></p>';
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
if (!$course) fail('Course not found.');
if (!in_array($method, ['bkash', 'nagad'], true)) fail('Choose a payment method.');
if (mb_strlen($name) < 3) fail('Enter your correct name.');
if (!preg_match('/^01\d{9}$/', $phone)) fail('Enter a correct 11-digit mobile number.');
if (strlen($trx) < 6) fail('Enter a correct TrxID.');

// Block duplicate TrxID + simple order log
$ordersDir = getenv('ORDERS_DIR') ?: (__DIR__ . '/data');
if (!is_dir($ordersDir)) mkdir($ordersDir, 0775, true);
$logFile = rtrim($ordersDir, '/') . '/orders.csv';
if (is_file($logFile)) {
    foreach (file($logFile, FILE_IGNORE_NEW_LINES) as $line) {
        $parts = str_getcsv($line);
        if (isset($parts[5]) && strtoupper($parts[5]) === $trx) {
            fail('This TrxID has already been used for an order. If there is a problem, let us know in the Telegram group.');
        }
    }
}
$fh = fopen($logFile, 'a');
if ($fh) { fputcsv($fh, [date('c'), $course['id'], $name, $phone, $method, $trx, $course['price']]); fclose($fh); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Successful — CourseHaat BD</title>
<style>
 body{font-family:system-ui,sans-serif;background:#f1f5f9;color:#0f172a;text-align:center;padding:56px 20px}
 .card{max-width:520px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:34px 26px}
 h1{font-size:26px;margin:10px 0}
 p{color:#475569;line-height:1.75}
 .join{display:inline-block;background:#229ed9;color:#fff;text-decoration:none;font-weight:800;padding:15px 30px;border-radius:12px;font-size:19px;margin-top:16px}
 .warn{font-size:14px;color:#92400e}
</style>
</head>
<body>
<div class="card">
  <div style="font-size:52px">🎉</div>
  <h1>Congratulations, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>!</h1>
  <p>We have received your order for <b><?= htmlspecialchars($course['name'], ENT_QUOTES, 'UTF-8') ?></b>.<br>
  Payment: <?= $method === 'bkash' ? 'bKash' : 'Nagad' ?> · TrxID: <?= htmlspecialchars($trx, ENT_QUOTES, 'UTF-8') ?><br>
  Your access will stay active once payment is verified. Tap the button below to join the private Telegram group:</p>
  <?php if (!empty($SITE['telegram'])): ?>
  <a class="join" href="<?= htmlspecialchars($SITE['telegram'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Join Telegram Group</a>
  <?php else: ?>
  <p><b>After payment verification, the Telegram join link will be sent to your mobile.</b></p>
  <?php endif; ?>
  <p class="warn" style="margin-top:16px">⚠️ Do not share the link with anyone — sharing will close your access.</p>
</div>
</body>
</html>
