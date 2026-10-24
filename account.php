<?php
// CourseHaat BD — অ্যাকাউন্ট খুলুন / লগইন (কোনো OTP/ভেরিফিকেশন নেই)
require __DIR__ . '/lib.php';
require __DIR__ . '/config.php';
ch_save_ref_from_request();
if (ch_current_user()) { header('Location: dashboard.php'); exit; }

$msg = ''; $err = '';
$ref = ch_saved_ref();
$refUser = ($ref !== '') ? ch_find_user_by_code($ref) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do = $_POST['do'] ?? 'signup';
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if ($do === 'login') {
        $found = ch_find_user_by_phone($phone);
        if ($found && strcasecmp($found['email'], $email) === 0) {
            ch_login_user($found);
            header('Location: dashboard.php'); exit;
        }
        $err = 'এই মোবাইল + Gmail দিয়ে কোনো অ্যাকাউন্ট পাওয়া যায়নি। নিচে নতুন অ্যাকাউন্ট খুলুন।';
    } else {
        if (mb_strlen($name) < 3) $err = 'আপনার সঠিক নাম লিখুন।';
        elseif (!preg_match('/^01\d{9}$/', $phone)) $err = 'সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন।';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'সঠিক Gmail ঠিকানা দিন।';
        else {
            $existing = ch_find_user_by_phone($phone) ?: ch_find_user_by_email($email);
            if ($existing) {
                ch_login_user($existing);
                header('Location: dashboard.php'); exit;
            }
            $refCode = ch_make_ref_code();
            $referrer = $refUser ? $refUser['ref_code'] : '';
            ch_append_csv(ch_users_file(), [date('c'), $name, $phone, $email, $refCode, $referrer]);
            ch_login_user(['phone' => $phone]);
            header('Location: dashboard.php'); exit;
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
 .top{text-align:center;margin-bottom:20px}
</style>
</head>
<body>
<div class="wrap">
  <div class="top"><a href="index.php">← হোমে ফিরুন</a></div>
  <?php if ($err): ?><div class="card"><div class="err"><?= ch_esc($err) ?></div></div><?php endif; ?>
  <?php if ($refUser): ?><div class="card"><div class="refnote">🎉 তুমি <b><?= ch_esc($refUser['name']) ?></b>-এর রেফার লিংক দিয়ে এসেছো। অ্যাকাউন্ট খুললেই রেফার যুক্ত হয়ে যাবে।</div></div><?php endif; ?>

  <div class="card">
    <h1>অ্যাকাউন্ট খুলুন</h1>
    <p>শুধু নাম, মোবাইল নম্বর আর Gmail দিলেই হবে — <b>কোনো OTP বা ভেরিফিকেশন লাগবে না।</b> অ্যাকাউন্ট খুললেই তোমার রেফার লিংক আর ৳ বোনাস বক্স পেয়ে যাবে।</p>
    <form method="post">
      <input type="hidden" name="do" value="signup">
      <label>আপনার নাম</label>
      <input name="name" required minlength="3" placeholder="যেমন: মোহাম্মদ বায়েজিদ" value="<?= ch_esc($_POST['name'] ?? '') ?>">
      <label>মোবাইল নম্বর</label>
      <input name="phone" required pattern="01[0-9]{9}" inputmode="numeric" placeholder="01XXXXXXXXX" value="<?= ch_esc($_POST['phone'] ?? '') ?>">
      <label>Gmail ঠিকানা</label>
      <input name="email" required type="email" placeholder="you@gmail.com" value="<?= ch_esc($_POST['email'] ?? '') ?>">
      <button type="submit">অ্যাকাউন্ট খুলুন</button>
    </form>
  </div>

  <div class="card">
    <h2>আগে অ্যাকাউন্ট খুলেছো? লগইন করো</h2>
    <p>যে মোবাইল আর Gmail দিয়ে খুলেছিলে, সেটা দিলেই ঢুকে যাবে।</p>
    <form method="post">
      <input type="hidden" name="do" value="login">
      <label>মোবাইল নম্বর</label>
      <input name="phone" required pattern="01[0-9]{9}" inputmode="numeric" placeholder="01XXXXXXXXX">
      <label>Gmail ঠিকানা</label>
      <input name="email" required type="email" placeholder="you@gmail.com">
      <button type="submit">লগইন করুন</button>
    </form>
  </div>
</div>
</body>
</html>
