<?php
// CourseHaat BD — ড্যাশবোর্ড: ৳ রেফার বোনাস বক্স + রেফার লিংক + আমার অর্ডার
require __DIR__ . '/lib.php';
require __DIR__ . '/config.php';
$u = ch_current_user();
if (!$u) { header('Location: account.php'); exit; }

$balance = ch_referral_balance($u['ref_code']);
$rows = ch_referral_rows($u['ref_code']);
$link = ch_ref_link($u['ref_code']);
$courseNames = [];
foreach ($COURSES as $c) $courseNames[$c['id']] = $c['name'];
$orders = [];
foreach (ch_read_csv(ch_data_dir() . '/orders.csv') as $p) {
    if (isset($p[3]) && $p[3] === $u['phone']) $orders[] = $p;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ড্যাশবোর্ড — CourseHaat BD</title>
<style>
 body{font-family:'Hind Siliguri',system-ui,sans-serif;background:#08131F;color:#fff;min-height:100vh;margin:0;padding:24px 16px}
 .wrap{max-width:640px;margin:0 auto}
 a{color:#2DD4BF;font-weight:700;text-decoration:none}
 .card{background:linear-gradient(160deg,#0D2436,#081624);border:1px solid rgba(45,212,191,.3);border-radius:20px;padding:26px 24px;margin-bottom:18px}
 h1{font-size:26px;margin:0 0 6px} h2{font-size:20px;margin:0 0 12px}
 p,li{color:#CBD5E1;line-height:1.8}
 .money{text-align:center;border:2px solid rgba(251,191,36,.7);background:rgba(251,191,36,.1);border-radius:20px;padding:24px;margin-bottom:18px}
 .money .lbl{font-size:17px;color:#FEF3C7;font-weight:700}
 .money .amt{font-size:52px;font-weight:900;color:#FBBF24;line-height:1.2}
 .money .sub{color:#CBD5E1;font-size:15px}
 .linkbox{display:flex;gap:8px;margin-top:12px}
 .linkbox input{flex:1;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,.25);background:#08131F;color:#fff;font-size:14px;min-width:0}
 .linkbox button{background:#2DD4BF;color:#08131F;font-weight:800;border:none;border-radius:10px;padding:12px 18px;cursor:pointer}
 table{width:100%;border-collapse:collapse;font-size:15px}
 th,td{text-align:left;padding:9px 6px;border-bottom:1px solid rgba(255,255,255,.12)}
 th{color:#2DD4BF}
 .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px}
</style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <a href="index.php">← হোমে ফিরুন</a>
    <a href="logout.php">লগআউট</a>
  </div>

  <div class="card">
    <h1>স্বাগতম, <?= ch_esc($u['name']) ?> 👋</h1>
    <p>মোবাইল: <?= ch_esc($u['phone']) ?> · Gmail: <?= ch_esc($u['email']) ?></p>
  </div>

  <div class="money">
    <div class="lbl">💰 রেফার বোনাস ব্যালেন্স</div>
    <div class="amt">৳<?= number_format($balance, 0, '.', ',') ?></div>
    <div class="sub">প্রথম সফল রেফারে ৳১০০, পরের প্রতিটিতে ৳৫০ করে জমা হয়</div>
  </div>

  <div class="card">
    <h2>🔗 তোমার রেফার লিংক</h2>
    <p>এই লিংক বন্ধুদের পাঠাও। লিংক দিয়ে কেউ অ্যাকাউন্ট খুলে কোর্স কিনলেই তোমার বক্সে বোনাস জমা হবে।</p>
    <div class="linkbox">
      <input id="reflink" readonly value="<?= ch_esc($link) ?>">
      <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('reflink').value);this.textContent='কপি হয়েছে ✓'">কপি করুন</button>
    </div>
    <p style="margin-top:14px">কীভাবে কাজ করে:</p>
    <ol>
      <li>বন্ধু তোমার লিংক দিয়ে অ্যাকাউন্ট খুলবে (নাম + নম্বর + Gmail, কোনো ভেরিফিকেশন নেই)</li>
      <li>বন্ধু যেকোনো কোর্স কিনবে (২০০ টাকা)</li>
      <li>প্রথম সফল রেফারে তোমার বক্সে <b>৳১০০</b>, পরের প্রতিটি রেফারে <b>৳৫০</b> জমা হবে</li>
    </ol>
  </div>

  <div class="card">
    <h2>👥 আমার রেফার (<?= count($rows) ?> জন)</h2>
    <?php if (!$rows): ?>
      <p>এখনো কেউ তোমার লিংক দিয়ে কেনেনি। লিংকটা শেয়ার করলেই এখানে দেখা যাবে।</p>
    <?php else: ?>
      <table>
        <tr><th>তারিখ</th><th>বন্ধু</th><th>কোর্স</th><th>বোনাস</th></tr>
        <?php foreach (array_reverse($rows) as $r): ?>
          <tr>
            <td><?= ch_esc(substr($r['date'], 0, 10)) ?></td>
            <td><?= ch_esc($r['buyer_name']) ?> (<?= ch_esc(ch_mask_phone($r['buyer_phone'])) ?>)</td>
            <td><?= ch_esc($courseNames[$r['course_id']] ?? $r['course_id']) ?></td>
            <td><b>৳<?= (int)$r['amount'] ?></b></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>🛒 আমার অর্ডার</h2>
    <?php if (!$orders): ?>
      <p>এখনো কোনো কোর্স কেনোনি। <a href="index.php">কোর্স দেখুন →</a></p>
    <?php else: ?>
      <table>
        <tr><th>তারিখ</th><th>কোর্স</th><th>TrxID</th></tr>
        <?php foreach (array_reverse($orders) as $o): ?>
          <tr>
            <td><?= ch_esc(substr($o[0], 0, 10)) ?></td>
            <td><?= ch_esc($courseNames[$o[1]] ?? $o[1]) ?></td>
            <td><?= ch_esc($o[5] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
