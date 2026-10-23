<?php
require __DIR__ . '/api.php';
$u = current_user();
if (!$u) { header('Location: account.php'); exit; }

$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'profile') {
    $r = backend('updateProfile', ['token' => auth_token(), 'name' => trim($_POST['name'] ?? ''), 'avatar' => trim($_POST['avatar'] ?? '')]);
    if (!empty($r['ok'])) { $msg = 'প্রোফাইল আপডেট হয়েছে।'; $u = current_user(true); }
    else $err = $r['error'] ?? 'সমস্যা হয়েছে।';
}
$ref = backend('referralInfo', ['token' => auth_token()]);
$orders = backend('myOrders', ['token' => auth_token()]);
$refLink = site_url() . '/account.php?ref=' . urlencode($u['refCode']);
$courseNames = [];
foreach ($COURSES as $c) $courseNames[$c['id']] = $c['name'];

page_head('ড্যাশবোর্ড');
?>
<h1>আমার ড্যাশবোর্ড</h1>
<?php if ($msg) echo '<div class="msg ok">' . esc($msg) . '</div>'; ?>
<?php if ($err) echo '<div class="msg err">' . esc($err) . '</div>'; ?>

<div class="card">
  <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap">
    <?php if (!empty($u['avatar']) && preg_match('/^https?:\/\//', $u['avatar'])): ?>
      <img class="avatar" src="<?= esc($u['avatar']) ?>" alt="প্রোফাইল ছবি">
    <?php else: ?>
      <div class="avatar-fallback"><?= esc(function_exists('mb_substr') ? mb_substr($u['name'], 0, 1) : '👤') ?></div>
    <?php endif; ?>
    <div>
      <h2 style="margin:0"><?= esc($u['name']) ?></h2>
      <p class="muted"><?= esc($u['email']) ?> · <?= esc($u['phone']) ?><br>
      স্ট্যাটাস: <span class="pill <?= $u['status'] === 'banned' ? 'banned' : 'active' ?>"><?= $u['status'] === 'banned' ? 'Ban' : 'Active' ?></span></p>
    </div>
  </div>
  <form method="post" style="margin-top:10px">
    <input type="hidden" name="do" value="profile">
    <div class="row">
      <div><label>নাম বদলাতে চাইলে</label><input name="name" value="<?= esc($u['name']) ?>"></div>
      <div><label>প্রোফাইল ছবির লিংক (URL)</label><input name="avatar" value="<?= esc(preg_match('/^https?:\/\//', $u['avatar'] ?? '') ? $u['avatar'] : '') ?>" placeholder="https://..."></div>
    </div>
    <button class="btn small" type="submit">প্রোফাইল সেভ করুন</button>
  </form>
</div>

<div class="card">
  <h2>আমার কোর্স</h2>
  <?php if (!empty($u['enrolled'])): ?>
    <?php foreach ($u['enrolled'] as $cid): ?>
      <p style="margin:10px 0">✅ <b><?= esc($courseNames[$cid] ?? $cid) ?></b>
      <?php if (!empty($SITE['telegram'])): ?>
        — <a href="<?= esc($SITE['telegram']) ?>" target="_blank" rel="noopener"><b>Telegram গ্রুপে জয়েন করুন</b></a>
      <?php endif; ?></p>
    <?php endforeach; ?>
  <?php else: ?>
    <p>এখনো কোনো কোর্স কেনা হয়নি। <a href="index.php">কোর্স দেখুন</a></p>
  <?php endif; ?>

  <h2 style="margin-top:22px">আমার অর্ডার</h2>
  <?php if (!empty($orders['orders'])): ?>
  <table>
    <tr><th>কোর্স</th><th>TrxID</th><th>অবস্থা</th></tr>
    <?php foreach ($orders['orders'] as $o): ?>
    <tr>
      <td><?= esc($courseNames[$o['courseId']] ?? $o['courseId']) ?></td>
      <td><?= esc($o['trx']) ?></td>
      <td><span class="pill <?= esc($o['status']) ?>"><?= $o['status'] === 'pending' ? 'যাচাই চলছে' : ($o['status'] === 'approved' ? 'Approve হয়েছে' : 'Reject হয়েছে') ?></span>
      <?php if ($o['status'] === 'rejected' && !empty($o['reason'])) echo '<br><span class="muted">কারণ: ' . esc($o['reason']) . '</span>'; ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?><p class="muted">কোনো অর্ডার নেই।</p><?php endif; ?>
</div>

<div class="card">
  <h2>রেফার করে আয় করুন</h2>
  <p>আপনার লিংক দিয়ে কেউ অ্যাকাউন্ট খুলে যেকোনো কোর্স কিনলেই — <b>প্রথম সফল রেফারে ১০০ টাকা</b>, পরের প্রতিটিতে <b>৫০ টাকা</b> করে বোনাস আপনার ব্যালেন্সে যাবে।</p>
  <label>আপনার রেফার লিংক</label>
  <div class="reflink">
    <input id="reflink" readonly value="<?= esc($refLink) ?>">
    <button class="btn small" type="button" onclick="navigator.clipboard.writeText(document.getElementById('reflink').value);this.textContent='কপি হয়েছে ✓'">কপি</button>
  </div>
  <div class="row" style="margin-top:16px">
    <div class="stat"><b><?= (int)($ref['totalReferrals'] ?? 0) ?></b>মোট রেফার</div>
    <div class="stat"><b><?= (int)($ref['successfulReferrals'] ?? 0) ?></b>সফল রেফার</div>
    <div class="stat"><b><?= (int)($ref['bonusBalance'] ?? 0) ?> টাকা</b>বোনাস ব্যালেন্স</div>
  </div>
  <?php if (!empty($ref['referrals'])): ?>
  <table style="margin-top:14px">
    <tr><th>নাম</th><th>যোগ দিয়েছে</th><th>কোর্স কিনেছে?</th></tr>
    <?php foreach ($ref['referrals'] as $r): ?>
    <tr><td><?= esc($r['name']) ?></td><td><?= esc(substr((string)$r['joined'], 0, 10)) ?></td><td><?= !empty($r['purchased']) ? '✅ হ্যাঁ' : '— এখনো না' ?></td></tr>
    <?php endforeach; ?>
  </table>
  <?php endif; ?>
</div>
<?php page_foot(); ?>
