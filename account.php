<?php
require __DIR__ . '/api.php';
if (current_user()) { header('Location: dashboard.php'); exit; }

$msg = ''; $err = '';
$step = 'form'; // form | otp | phone (google complete)
$pendingEmail = isset($_SESSION['ch_pending_email']) ? $_SESSION['ch_pending_email'] : '';
$googleIdToken = isset($_SESSION['ch_google_token']) ? $_SESSION['ch_google_token'] : '';
$googleName = isset($_SESSION['ch_google_name']) ? $_SESSION['ch_google_name'] : '';
$googleEmail = isset($_SESSION['ch_google_email']) ? $_SESSION['ch_google_email'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do = $_POST['do'] ?? '';
    if ($do === 'request_otp') {
        $purpose = ($_POST['purpose'] ?? 'signup') === 'login' ? 'login' : 'signup';
        $r = backend('requestOtp', [
            'purpose' => $purpose,
            'name' => trim($_POST['name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'refCode' => saved_ref(),
        ]);
        if (!empty($r['ok'])) {
            $_SESSION['ch_pending_email'] = trim($_POST['email'] ?? '');
            $pendingEmail = $_SESSION['ch_pending_email'];
            $step = 'otp'; $msg = $r['message'] ?? 'কোড পাঠানো হয়েছে।';
        } else { $err = $r['error'] ?? 'সমস্যা হয়েছে।'; }
    }
    if ($do === 'verify_otp') {
        $r = backend('verifyOtp', ['email' => $pendingEmail, 'code' => trim($_POST['code'] ?? '')]);
        if (!empty($r['ok'])) {
            login_with_token($r['token']);
            unset($_SESSION['ch_pending_email']);
            header('Location: dashboard.php'); exit;
        } else { $err = $r['error'] ?? 'ভুল কোড।'; $step = 'otp'; }
    }
    if ($do === 'google') {
        $idToken = trim($_POST['id_token'] ?? '');
        $r = backend('googleLogin', ['idToken' => $idToken]);
        if (!empty($r['ok']) && !empty($r['needPhone'])) {
            $_SESSION['ch_google_token'] = $idToken;
            $_SESSION['ch_google_name'] = $r['name'] ?? '';
            $_SESSION['ch_google_email'] = $r['email'] ?? '';
            $googleIdToken = $idToken; $googleName = $_SESSION['ch_google_name']; $googleEmail = $_SESSION['ch_google_email'];
            $step = 'phone'; $msg = 'Google অ্যাকাউন্ট যাচাই হয়েছে। অ্যাকাউন্ট খুলতে মোবাইল নম্বরটা দিন (এক নম্বরে একটাই অ্যাকাউন্ট)।';
        } elseif (!empty($r['ok'])) {
            login_with_token($r['token']);
            header('Location: dashboard.php'); exit;
        } else { $err = $r['error'] ?? 'Google লগইন ব্যর্থ হয়েছে।'; }
    }
    if ($do === 'google_complete') {
        $r = backend('googleComplete', [
            'idToken' => $googleIdToken, 'name' => $googleName,
            'phone' => trim($_POST['phone'] ?? ''), 'refCode' => saved_ref(),
        ]);
        if (!empty($r['ok'])) {
            login_with_token($r['token']);
            unset($_SESSION['ch_google_token'], $_SESSION['ch_google_name'], $_SESSION['ch_google_email']);
            header('Location: dashboard.php'); exit;
        } else { $err = $r['error'] ?? 'সমস্যা হয়েছে।'; $step = 'phone'; }
    }
    if ($do === 'appeal') {
        $r = backend('appeal', [
            'email' => trim($_POST['email'] ?? ''), 'phone' => trim($_POST['phone'] ?? ''), 'message' => trim($_POST['message'] ?? ''),
        ]);
        if (!empty($r['ok'])) $msg = $r['message'] ?? 'আবেদন জমা হয়েছে।';
        else $err = $r['error'] ?? 'সমস্যা হয়েছে।';
    }
}
$clientId = getenv('GOOGLE_CLIENT_ID') ?: '';
page_head('অ্যাকাউন্ট');
?>
<h1>অ্যাকাউন্ট</h1>
<p>Google দিয়ে অথবা Gmail-এ কোড নিয়ে — যেভাবে সুবিধা। এক নম্বরে ও এক Gmail দিয়ে একটাই অ্যাকাউন্ট।</p>
<?php if ($msg) echo '<div class="msg ok">' . esc($msg) . '</div>'; ?>
<?php if ($err) echo '<div class="msg err">' . esc($err) . '</div>'; ?>

<?php if ($step === 'otp'): ?>
<div class="card">
  <h2>যাচাইকরণ কোড বসান</h2>
  <p class="muted"><?= esc($pendingEmail) ?> ঠিকানায় ৬ ডিজিটের কোড পাঠানো হয়েছে (৫ মিনিট মেয়াদ)।</p>
  <form method="post">
    <input type="hidden" name="do" value="verify_otp">
    <label>কোড</label>
    <input name="code" inputmode="numeric" maxlength="6" placeholder="______" required>
    <button class="btn" type="submit">যাচাই করে ঢুকুন</button>
  </form>
</div>
<?php elseif ($step === 'phone'): ?>
<div class="card">
  <h2>মোবাইল নম্বর দিন</h2>
  <p class="muted"><?= esc($googleEmail) ?> — এই Google অ্যাকাউন্টের জন্য নতুন CourseHaat অ্যাকাউন্ট তৈরি হবে।</p>
  <form method="post">
    <input type="hidden" name="do" value="google_complete">
    <label>মোবাইল নম্বর</label>
    <input name="phone" inputmode="numeric" placeholder="01XXXXXXXXX" required>
    <button class="btn" type="submit">অ্যাকাউন্ট খুলুন</button>
  </form>
</div>
<?php else: ?>
<?php if ($clientId): ?>
<div class="card" style="text-align:center">
  <h2>Google দিয়ে এক ক্লিকে</h2>
  <div id="gbtn" style="display:flex;justify-content:center;margin-top:10px"></div>
</div>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
  window.addEventListener('load', function(){
    if (!window.google) return;
    google.accounts.id.initialize({ client_id: <?= json_encode($clientId) ?>, callback: function(resp){
      var f = document.createElement('form'); f.method = 'post';
      f.innerHTML = '<input type="hidden" name="do" value="google"><input type="hidden" name="id_token">';
      f.querySelector('[name=id_token]').value = resp.credential;
      document.body.appendChild(f); f.submit();
    }});
    google.accounts.id.renderButton(document.getElementById('gbtn'), { theme: 'filled_blue', size: 'large', text: 'continue_with' });
  });
</script>
<?php endif; ?>

<div class="row">
  <div class="card">
    <h2>নতুন অ্যাকাউন্ট (সাইন আপ)</h2>
    <form method="post">
      <input type="hidden" name="do" value="request_otp">
      <input type="hidden" name="purpose" value="signup">
      <label>আপনার নাম</label>
      <input name="name" required minlength="3" placeholder="আপনার নাম">
      <label>মোবাইল নম্বর</label>
      <input name="phone" required pattern="01[0-9]{9}" inputmode="numeric" placeholder="01XXXXXXXXX">
      <label>Gmail ঠিকানা (কোড যাবে এই ঠিকানায়)</label>
      <input name="email" required type="email" placeholder="you@gmail.com">
      <?php if (saved_ref()) echo '<p class="muted" style="margin-top:10px">রেফার কোড যুক্ত আছে: <b>' . esc(saved_ref()) . '</b></p>'; ?>
      <button class="btn" type="submit">কোড পাঠান</button>
    </form>
  </div>
  <div class="card">
    <h2>লগইন</h2>
    <form method="post">
      <input type="hidden" name="do" value="request_otp">
      <input type="hidden" name="purpose" value="login">
      <label>আপনার Gmail ঠিকানা</label>
      <input name="email" required type="email" placeholder="you@gmail.com">
      <button class="btn secondary" type="submit">কোড পাঠান</button>
    </form>
    <p class="muted" style="margin-top:12px">কোড দিয়ে লগইন করলেই ড্যাশবোর্ডে চলে যাবেন — পাসওয়ার্ড মনে রাখার ঝামেলা নেই।</p>
  </div>
</div>

<div class="card">
  <h2>অ্যাকাউন্ট Ban হয়ে গেছে?</h2>
  <p class="muted">ভুলবশত Ban হলে Active করার আবেদন করুন — অ্যাডমিন যাচাই করে সিদ্ধান্ত নেবেন।</p>
  <form method="post">
    <input type="hidden" name="do" value="appeal">
    <div class="row">
      <div><label>Gmail ঠিকানা</label><input name="email" required type="email" placeholder="you@gmail.com"></div>
      <div><label>মোবাইল নম্বর</label><input name="phone" required placeholder="01XXXXXXXXX"></div>
    </div>
    <label>কী হয়েছিল, সংক্ষেপে লিখুন</label>
    <textarea name="message" rows="3" placeholder="আপনার বক্তব্য"></textarea>
    <button class="btn secondary" type="submit">আবেদন জমা দিন</button>
  </form>
</div>
<?php endif; ?>
<?php page_foot(); ?>
