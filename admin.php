<?php
require __DIR__ . '/api.php';
$u = current_user();
if (!$u) { header('Location: account.php'); exit; }

$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do = $_POST['do'] ?? '';
    if ($do === 'decide') {
        $r = backend('adminDecide', ['token' => auth_token(), 'orderId' => $_POST['orderId'] ?? '', 'decision' => $_POST['decision'] ?? '', 'reason' => trim($_POST['reason'] ?? '')]);
    } elseif ($do === 'ban') {
        $r = backend('adminSetBan', ['token' => auth_token(), 'userId' => $_POST['userId'] ?? '', 'banned' => (($_POST['banned'] ?? '') === '1')]);
    } elseif ($do === 'appeal_decide') {
        $r = backend('adminDecideAppeal', ['token' => auth_token(), 'appealId' => $_POST['appealId'] ?? '', 'decision' => $_POST['decision'] ?? '']);
    } else { $r = ['ok' => false, 'error' => 'অজানা কাজ।']; }
    if (!empty($r['ok'])) $msg = $r['message'] ?? 'হয়ে গেছে।'; else $err = $r['error'] ?? 'সমস্যা হয়েছে।';
}
$orders = backend('adminOrders', ['token' => auth_token()]);
if (empty($orders['ok'])) {
    page_head('অ্যাডমিন');
    echo '<div class="card"><h2>অ্যাডমিন প্যানেল</h2><div class="msg err">' . esc($orders['error'] ?? 'এই পেজ শুধু ওয়েবসাইটের মালিকের জন্য।') . '</div><p class="muted">মালিকের Gmail (' . esc('merajhosenbayzid@gmail.com') . ') দিয়ে লগইন করলে তবেই এই প্যানেল খুলবে।</p></div>';
    page_foot(); exit;
}
$users = backend('adminUsers', ['token' => auth_token()]);
$appeals = backend('adminAppeals', ['token' => auth_token()]);
$courseNames = [];
foreach ($COURSES as $c) $courseNames[$c['id']] = $c['name'];

page_head('অ্যাডমিন প্যানেল');
?>
<h1>অ্যাডমিন প্যানেল</h1>
<p class="muted">অর্ডার যাচাই, ইউজার ও রেফার হিসাব, Ban ও আবেদনের সিদ্ধান্ত — সব এখান থেকে।</p>
<?php if ($msg) echo '<div class="msg ok">' . esc($msg) . '</div>'; ?>
<?php if ($err) echo '<div class="msg err">' . esc($err) . '</div>'; ?>

<div class="card">
  <h2>যাচাইয়ের অপেক্ষায় থাকা অর্ডার (<?= count($orders['pending'] ?? []) ?>)</h2>
  <?php if (!empty($orders['pending'])): ?>
  <table>
    <tr><th>ক্রেতা</th><th>কোর্স</th><th>পেমেন্ট</th><th>সিদ্ধান্ত</th></tr>
    <?php foreach ($orders['pending'] as $o): ?>
    <tr>
      <td><?= esc($o['buyerName']) ?><br><span class="muted"><?= esc($o['buyerPhone']) ?> · <?= esc(substr((string)$o['createdAt'], 0, 16)) ?></span></td>
      <td><?= esc($o['courseName']) ?><br><b><?= (int)$o['amount'] ?> টাকা</b></td>
      <td><?= $o['method'] === 'bkash' ? 'bKash' : 'Nagad' ?><br><span class="muted">থেকে: <?= esc($o['senderPhone']) ?><br>TrxID: <b><?= esc($o['trx']) ?></b></span></td>
      <td>
        <form method="post" style="display:flex;gap:6px;flex-wrap:wrap">
          <input type="hidden" name="do" value="decide">
          <input type="hidden" name="orderId" value="<?= esc($o['id']) ?>">
          <button class="btn small" name="decision" value="approve" type="submit">Approve</button>
          <input name="reason" placeholder="Reject-এর কারণ" style="width:150px;padding:8px">
          <button class="btn small danger" name="decision" value="reject" type="submit">Reject</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?><p class="muted">কোনো অপেক্ষমাণ অর্ডার নেই।</p><?php endif; ?>
</div>

<div class="card">
  <h2>সাম্প্রতিক অর্ডার</h2>
  <?php if (!empty($orders['recent'])): ?>
  <table>
    <tr><th>ক্রেতা</th><th>কোর্স</th><th>TrxID</th><th>অবস্থা</th></tr>
    <?php foreach ($orders['recent'] as $o): ?>
    <tr><td><?= esc($o['buyerName']) ?></td><td><?= esc($o['courseName']) ?></td><td><?= esc($o['trx']) ?></td>
    <td><span class="pill <?= esc($o['status']) ?>"><?= esc($o['status']) ?></span><?php if (!empty($o['reason'])) echo '<br><span class="muted">' . esc($o['reason']) . '</span>'; ?></td></tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?><p class="muted">এখনো কোনো অর্ডার আসেনি।</p><?php endif; ?>
</div>

<div class="card">
  <h2>সব ইউজার ও রেফার হিসাব</h2>
  <?php if (!empty($users['users'])): ?>
  <table>
    <tr><th>ইউজার</th><th>রেফার</th><th>বোনাস</th><th>অবস্থা</th><th>কাজ</th></tr>
    <?php foreach ($users['users'] as $x): ?>
    <tr>
      <td><?= esc($x['name']) ?><br><span class="muted"><?= esc($x['email']) ?><br><?= esc($x['phone']) ?> · কোড: <?= esc($x['refCode']) ?></span></td>
      <td>সফল: <b><?= (int)$x['successfulReferrals'] ?></b></td>
      <td><b><?= (int)$x['bonusBalance'] ?> টাকা</b></td>
      <td><span class="pill <?= $x['status'] === 'banned' ? 'banned' : 'active' ?>"><?= esc($x['status']) ?></span></td>
      <td>
        <form method="post">
          <input type="hidden" name="do" value="ban">
          <input type="hidden" name="userId" value="<?= esc($x['id']) ?>">
          <?php if ($x['status'] === 'banned'): ?>
            <button class="btn small" name="banned" value="0" type="submit">Active করুন</button>
          <?php else: ?>
            <button class="btn small danger" name="banned" value="1" type="submit">Ban করুন</button>
          <?php endif; ?>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?><p class="muted">কোনো ইউজার নেই।</p><?php endif; ?>
</div>

<div class="card">
  <h2>Active-এর আবেদন</h2>
  <?php $pendingAppeals = array_filter($appeals['appeals'] ?? [], function($a){ return $a['status'] === 'pending'; }); ?>
  <?php if (!empty($pendingAppeals)): ?>
  <table>
    <tr><th>আবেদনকারী</th><th>বক্তব্য</th><th>সিদ্ধান্ত</th></tr>
    <?php foreach ($pendingAppeals as $a): ?>
    <tr>
      <td><?= esc($a['email']) ?><br><span class="muted"><?= esc($a['phone']) ?></span></td>
      <td><?= esc($a['message']) ?></td>
      <td>
        <form method="post" style="display:flex;gap:6px">
          <input type="hidden" name="do" value="appeal_decide">
          <input type="hidden" name="appealId" value="<?= esc($a['id']) ?>">
          <button class="btn small" name="decision" value="approve" type="submit">Active করুন</button>
          <button class="btn small danger" name="decision" value="reject" type="submit">Reject</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?><p class="muted">কোনো অপেক্ষমাণ আবেদন নেই।</p><?php endif; ?>
</div>
<?php page_foot(); ?>
