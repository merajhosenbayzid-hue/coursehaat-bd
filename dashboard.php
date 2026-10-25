<?php
require __DIR__ . '/config.php';
require __DIR__ . '/lib.php';
$user = ch_current_user();
if (!$user) { header('Location: account.php'); exit; }
$rows = ch_referral_rows($user['ref_code']);
$balance = ch_referral_balance($user['ref_code']);
$link = ch_ref_link($user['ref_code']);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — CourseHaat BD</title>
<style>
 body{font-family:system-ui,sans-serif;background:#f1f5f9;color:#0f172a;line-height:1.7;margin:0}
 .wrap{max-width:640px;margin:0 auto;padding:20px 18px 40px}
 a{color:#0d9488;font-weight:700;text-decoration:none}
 .card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;margin:16px 0}
 .big{font-size:32px;font-weight:900;color:#0d9488}
 .code{font-size:22px;font-weight:900;letter-spacing:1px}
 .muted{color:#475569;font-size:14px}
 table{width:100%;border-collapse:collapse;font-size:14px}
 th,td{text-align:left;padding:8px 6px;border-bottom:1px solid #f1f5f9}
 input{width:100%;padding:10px;border-radius:10px;border:1px solid #cbd5e1;font-size:14px;box-sizing:border-box}
</style></head><body><div class="wrap">
<p><a href="index.php">← Home</a> · <a href="logout.php">Logout</a></p>
<h1>Hello, <?= ch_esc($user['name']) ?>!</h1>
<p class="muted">Mobile: <?= ch_esc($user['phone']) ?><?= $user['referrer_code'] ? ' · Referred by code: ' . ch_esc($user['referrer_code']) : '' ?></p>

<div class="card"><h2>Your Referral Bonus</h2>
<div class="big"><?= $balance ?> BDT</div>
<p class="muted">First successful referral = 100 BDT, every later referral = 50 BDT. Unlimited referrals. One buyer counts only once (their first order).</p>
<p>Successful referrals: <b><?= count($rows) ?></b></p></div>

<div class="card"><h2>Your Referral Code &amp; Link</h2>
<div class="code"><?= ch_esc($user['ref_code']) ?></div>
<p class="muted">Share this link — anyone who signs up with it and buys a course will earn you the bonus:</p>
<input readonly value="<?= ch_esc($link) ?>" onclick="this.select()">
<p class="muted">Bonus is added when the referred person buys a course (order submitted with TrxID).</p></div>

<div class="card"><h2>Referral History</h2>
<?php if (!$rows): ?><p class="muted">No successful referrals yet.</p>
<?php else: ?><table><tr><th>Date</th><th>Buyer</th><th>Course</th><th>Bonus</th></tr>
<?php foreach (array_reverse($rows) as $r): ?><tr><td><?= ch_esc(substr($r['date'],0,10)) ?></td><td><?= ch_esc($r['buyer_name']) ?></td><td><?= ch_esc($r['course_id']) ?></td><td><b><?= (int)$r['amount'] ?> BDT</b></td></tr><?php endforeach; ?>
</table><?php endif; ?></div>
</div></body></html>
