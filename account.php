<?php
// CourseHaat BD — অ্যাকাউন্ট খুলুন / লগইন
// সাইনআপ: নাম + মোবাইল + Gmail + পাসওয়ার্ড + রেফার কোড (যদি থাকে)।
// রেফার বোনাস শুধু তখনই গোনা হবে যখন রেফার কোডটি কোনো আসল ব্যবহারকারীর কোডের সাথে মিলবে;
// কোড না দিলে বা না মিললে বোনাস পাওয়া যাবে না (তবে অ্যাকাউন্ট ঠিকই খুলবে)।
require __DIR__ . '/lib.php';
require __DIR__ . '/config.php';
ch_save_ref_from_request();
if (ch_current_user()) { header('Location: dashboard.php'); exit; }

$err = '';
$ref = ch_saved_ref();
$refUser = ($ref !== '') ? ch_find_user_by_code($ref) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do = $_POST['do'] ?? 'signup';
    if ($do === 'login') {
        $loginId = trim($_POST['login_id'] ?? '');
        $password = $_POST['password'] ?? '';
        $found = null;
        if (preg_match('/^01\d{9}$/', $loginId)) $found = ch_find_user_by_phone($loginId);
        elseif (filter_var($loginId, FILTER_VALIDATE_EMAIL)) $found = ch_find_user_by_email($loginId);
        if (!$found) $err = 'এই নম্বর/Gmail দিয়ে কোনো অ্যাকাউন্ট পাওয়া যায়নি। নিচে নতুন অ্যাকাউন্ট খুলুন।';
        else {
            $chk = ch_check_password($found, $password);
            if ($chk === 'ok' || $chk === 'legacy') {
                ch_login_user($found);
                header('Location: dashboard.php'); exit;
            }
            $err = 'ভুল পাসওয়ার্ড। আবার চেষ্টা করুন।';
        }
    } else {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $refCodeIn = strtoupper(trim($_POST['ref_code'] ?? ''));
        if (mb_strlen($name) < 3) $err = 'আপনার সঠিক নাম লিখুন।';
        elseif (!preg_match('/^01\d{9}$/', $phone)) $err = 'সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন।';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'সঠিক Gmail ঠিকানা দিন।';
        elseif (mb_strlen($password) < 6) $err = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
        else {
            $existing = ch_find_user_by_phone($phone) ?: ch_find_user_by_email($email);
            if ($existing && !empty($existing['pass_hash'])) {
                $err = 'এই নম্বর/Gmail দিয়ে অ্যাকাউন্ট আছে। উপরের লগইন ফর্ম দিয়ে ঢুকুন।';
            } else {
                if ($existing) {
                    // লিগ্যাসি অ্যাকাউন্ট (পাসওয়ার্ড ছিল না) — পুরনো ডেটা রেখেই পাসওয়ার্ড বসিয়ে লগইন
                    ch_set_password($existing['phone'], $password);
                    ch_login_user($existing);
                    header('Location: dashboard.php'); exit;
                }
                $referrer = '';
                $refMatched = false;
                if ($refCodeIn !== '') {
                    $ru = ch_find_user_by_code($refCodeIn);
                    if ($ru) { $referrer = $ru['ref_code']; $refMatched = true; }
                }
                $refCode = ch_make_ref_code();
                ch_append_csv(ch_users_file(), [date('c'), $name, $phone, $email, $refCode, $referrer, password_hash($password, PASSWORD_DEFAULT)]);
                if ($refCodeIn !== '' && !$refMatched) $_SESSION['ch_notice'] = 'রেফার কোডটি মেলেনি — তাই কোনো রেফারার যুক্ত হয়নি, রেফার বোনাস পাওয়া যাবে না। সঠিক কোড থাকলে আবার জানিও।';
                ch_login_user(['phone' => $phone]);
                header('Location: dashboard.php'); exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>অ্যাকাউন্ট — CourseHaat BD</title>
<style>
 body{font-family:'Hind Siliguri',system-ui,sans-serif;background:#08131F;color:#fff;min-height:100vh;margin:0;padding:24px 16px}
 .wrap{max-width:520px;margin:0 auto}
 a{color:#2DD4BF;font-weight:700;text-decoration:none}
 .card{background:linear-gradient(160deg,#0D2436,#081624);border:1px solid rgba(45,212,191,.3);border-radius:20px;padding:28px 24px;margin-bottom:18px}
 h1{font-size:26px;margin:0 0 6px} h2{font-size:20px;margin:0 0 14px}
 p{color:#CBD5E1;line-height:1.75}
 label{display:block;margin:14px 0 6px;font-weight:700}
 input{width:100%;padding:13px;border-radius:10px;border:1px solid rgba(255,255,255,.25);background:#08131F;color:#fff;font-size:16px;font-family:inherit;box-sizing:border-box}
 button{width:100%;margin-top:18px;background:linear-gradient(90deg,#2DD4BF,#38BDF8);color:#08131F;font-weight:800;font-size:18px;border:none;border-radius:12px;padding:14px;cursor:pointer}
 .err{background:rgba(248,113,113,.15);border:1px solid rgba(248,113,113,.5);border-radius:10px;padding:12px;color:#FECACA}
 .refnote{background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.5);border-radius:10px;padding:12px;color:#FEF3C7}
 .hint{font-size:14px;color:#A8C0D8}
 .top{text-align:center;margin-bottom:20px}
</style>
</head>
<body>
<div class="wrap">
  <div class="top"><a href="index.php">← হোমে ফিরুন</a></div>
  <?php if ($err): ?><div class="card"><div class="err"><?= ch_esc($err) ?></div></div><?php endif; ?>
  <?php if ($refUser): ?><div class="card"><div class="refnote">🎉 তুমি <b><?= ch_esc($refUser['name']) ?></b>-এর রেফার লিংক দিয়ে এসেছো — কোডটি নিচে বসানো আছে।</div></div><?php endif; ?>

  <div class="card">
    <h1>অ্যাকাউন্ট খুলুন</h1>
    <p>নাম, মোবাইল, Gmail আর পাসওয়ার্ড দিলেই হবে — <b>কোনো OTP বা ভেরিফিকেশন লাগবে না।</b></p>
    <form method="post">
      <input type="hidden" name="do" value="signup">
      <label>আপনার নাম</label>
      <input name="name" required minlength="3" placeholder="যেমন: মোহাম্মদ বায়েজিদ" value="<?= ch_esc($_POST['name'] ?? '') ?>">
      <label>মোবাইল নম্বর</label>
      <input name="phone" required pattern="01[0-9]{9}" inputmode="numeric" placeholder="01XXXXXXXXX" value="<?= ch_esc($_POST['phone'] ?? '') ?>">
      <label>Gmail ঠিকানা</label>
      <input name="email" required type="email" placeholder="you@gmail.com" value="<?= ch_esc($_POST['email'] ?? '') ?>">
      <label>পাসওয়ার্ড <span class="hint">(কমপক্ষে ৬ অক্ষর — এটা দিয়েই পরে লগইন করবে)</span></label>
      <input name="password" required type="password" minlength="6" placeholder="পাসওয়ার্ড বেছে নিন">
      <label>রেফার কোড <span class="hint">(যদি থাকে — কোডটি আসল হতে হবে, না মিললে বোনাস পাবে না)</span></label>
      <input name="ref_code" placeholder="যেমন: CH123456" value="<?= ch_esc($_POST['ref_code'] ?? $ref) ?>">
      <button type="submit">অ্যাকাউন্ট খুলুন</button>
    </form>
  </div>

  <div class="card">
    <h2>লগইন</h2>
    <p>আগে অ্যাকাউন্ট খুলে থাকলে নম্বর <b>অথবা</b> Gmail + সঠিক পাসওয়ার্ড দিলেই ঢুকে যাবে।</p>
    <form method="post">
      <input type="hidden" name="do" value="login">
      <label>মোবাইল নম্বর অথবা Gmail</label>
      <input name="login_id" required placeholder="01XXXXXXXXX অথবা you@gmail.com">
      <label>পাসওয়ার্ড</label>
      <input name="password" required type="password" placeholder="তোমার পাসওয়ার্ড">
      <button type="submit">লগইন করুন</button>
    </form>
  </div>
</div>
</body>
</html>
