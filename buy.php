<?php
require __DIR__ . '/api.php';
$u = current_user();
$courseId = $_GET['course'] ?? ($_POST['course'] ?? '');
$course = null;
foreach ($COURSES as $c) { if ($c['id'] === $courseId) { $course = $c; break; } }
if (!$course) { header('Location: index.php'); exit; }
if (!$u) { header('Location: account.php'); exit; }

$msg = ''; $err = '';
$enrolled = in_array($courseId, $u['enrolled'] ?? [], true);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$enrolled) {
    $r = backend('createOrder', [
        'token' => auth_token(), 'courseId' => $courseId,
        'method' => $_POST['method'] ?? '', 'senderPhone' => trim($_POST['senderPhone'] ?? ''), 'trx' => trim($_POST['trx'] ?? ''),
    ]);
    if (!empty($r['ok'])) { $msg = $r['message'] ?? 'অর্ডার জমা হয়েছে।'; }
    else { $err = $r['error'] ?? 'সমস্যা হয়েছে।'; $u = current_user(true); }
}
page_head('কোর্স কিনুন');
?>
<h1><?= esc($course['name']) ?></h1>
<p>কোর্স ফি: <b><?= (int)$course['price'] ?> টাকা</b></p>
<?php if ($msg) echo '<div class="msg ok">' . esc($msg) . '</div>'; ?>
<?php if ($err) echo '<div class="msg err">' . esc($err) . '</div>'; ?>

<?php if ($enrolled): ?>
<div class="card">
  <h2>✅ এই কোর্সটি আপনার কেনা আছে</h2>
  <?php if (!empty($SITE['telegram'])): ?>
  <p>নিচের বাটনে চাপ দিয়ে প্রাইভেট Telegram গ্রুপে জয়েন করুন:</p>
  <a class="btn" href="<?= esc($SITE['telegram']) ?>" target="_blank" rel="noopener">Telegram গ্রুপে জয়েন করুন</a>
  <?php endif; ?>
</div>
<?php elseif ($msg): ?>
<div class="card">
  <h2>অর্ডার জমা হয়েছে</h2>
  <p>ট্রানজেকশন যাচাই শেষে অ্যাডমিন Approve করলেই এই পেজে ও ড্যাশবোর্ডে Telegram জয়েন বাটন দেখতে পাবেন। <a href="dashboard.php">ড্যাশবোর্ডে যান</a></p>
</div>
<?php else: ?>
<div class="card">
  <h2>পেমেন্ট করে অর্ডার করুন</h2>
  <p>প্রথমে নিচের যেকোনো নম্বরে <b><?= (int)$course['price'] ?> টাকা Send Money</b> করুন, তারপর ফর্মটি পূরণ করুন। যাচাইয়ের পর Approve হলেই অ্যাক্সেস চালু হবে।</p>
  <p style="margin-top:8px">bKash (Personal): <b><?= esc($SITE['bkash']) ?></b><br>Nagad (Personal): <b><?= esc($SITE['nagad']) ?></b></p>
  <form method="post">
    <input type="hidden" name="course" value="<?= esc($courseId) ?>">
    <label>পেমেন্ট মাধ্যম</label>
    <select name="method" required>
      <option value="">— বেছে নিন —</option>
      <option value="bkash">bKash</option>
      <option value="nagad">Nagad</option>
    </select>
    <label>যে নম্বর থেকে টাকা পাঠিয়েছেন</label>
    <input name="senderPhone" required pattern="01[0-9]{9}" inputmode="numeric" placeholder="01XXXXXXXXX">
    <label>TrxID</label>
    <input name="trx" required minlength="6" placeholder="যেমন: 9HX7XXXXXX">
    <p class="muted" style="margin-top:10px">সতর্কতা: এক TrxID দিয়ে একটাই অর্ডার করা যাবে। একই TrxID আবার ব্যবহারের চেষ্টা করলে অ্যাকাউন্ট Ban হয়ে যাবে।</p>
    <button class="btn" type="submit">অর্ডার জমা দিন</button>
  </form>
</div>
<?php endif; ?>
<?php page_foot(); ?>
