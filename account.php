<?php
require __DIR__ . '/config.php';
require __DIR__ . '/lib.php';
ch_save_ref_from_request();
$ref = ch_saved_ref();
$refUser = ($ref !== '') ? ch_find_user_by_code($ref) : null;
$err = ''; $notice = '';
if (isset($_SESSION['ch_notice'])) { $notice = $_SESSION['ch_notice']; unset($_SESSION['ch_notice']); }

// Login: phone OR Gmail + password. Existing data is restored on login.
if (($_POST['form'] ?? '') === 'login') {
    $loginId = trim($_POST['login_id'] ?? '');
    $password = $_POST['password'] ?? '';
    if (preg_match('/^01\d{9}$/', $loginId)) $found = ch_find_user_by_phone($loginId);
    elseif (filter_var($loginId, FILTER_VALIDATE_EMAIL)) $found = ch_find_user_by_email($loginId);
    else $found = null;
    if (!$found) $err = 'No account found with this number/Gmail. Create a new account below.';
    elseif (empty($found['pass_hash']) || !password_verify($password, $found['pass_hash'])) $err = 'Wrong password.';
    else { ch_login_user($found); header('Location: dashboard.php'); exit; }
}

// Signup: name + phone + Gmail + password (>=6) + optional referral code.
// Referral code must match an existing code, otherwise no referrer is attached.
if (($_POST['form'] ?? '') === 'signup') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $refCodeIn = strtoupper(trim($_POST['ref_code'] ?? ''));
    if (mb_strlen($name) < 3) $err = 'Enter your correct name.';
    elseif (!preg_match('/^01\d{9}$/', $phone)) $err = 'Enter a correct 11-digit mobile number.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Enter a correct Gmail address.';
    elseif (mb_strlen($password) < 6) $err = 'Password must be at least 6 characters.';
    else {
        $existing = ch_find_user_by_phone($phone) ?: ch_find_user_by_email($email);
        if ($existing) $err = 'An account already exists with this number/Gmail. Login above.';
        else {
            $referrer = ''; $refMatched = false;
            if ($refCodeIn !== '') {
                $ru = ch_find_user_by_code($refCodeIn);
                if ($ru) { $referrer = $ru['ref_code']; $refMatched = true; }
            }
            $refCode = ch_make_ref_code();
            ch_append_csv(ch_users_file(), [date('c'), $name, $phone, $email, $refCode, $referrer, password_hash($password, PASSWORD_DEFAULT)]);
            if ($refCodeIn !== '' && !$refMatched) $_SESSION['ch_notice'] = 'Referral code did not match — so no referrer was attached and no referral bonus will count. If you have the correct code, let us know.';
            ch_login_user(['phone' => $phone]);
            header('Location: dashboard.php'); exit;
        }
    }
}
$user = ch_current_user();
if ($user) { header('Location: dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Account — CourseHaat BD</title>
<style>
 body{font-family:system-ui,sans-serif;background:#f1f5f9;color:#0f172a;line-height:1.7;margin:0}
 .wrap{max-width:520px;margin:0 auto;padding:20px 18px 40px}
 a{color:#0d9488;font-weight:700;text-decoration:none}
 .card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;margin:16px 0}
 label{display:block;font-size:14px;font-weight:700;margin:12px 0 6px}
 input{width:100%;padding:12px;border-radius:10px;border:1px solid #cbd5e1;font-size:16px;font-family:inherit;box-sizing:border-box}
 button{width:100%;background:#0d9488;color:#fff;border:none;border-radius:12px;padding:14px;font-size:17px;font-weight:800;cursor:pointer;margin-top:16px}
 .err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:10px;padding:12px}
 .ok{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;border-radius:10px;padding:12px}
 .note{background:#fffbeb;border:1px solid #fde68a;color:#92400e;border-radius:10px;padding:12px}
 .muted{color:#475569;font-size:14px}
</style></head><body><div class="wrap">
<p><a href="index.php">← Back to Home</a></p>
<h1>My Account</h1>
<p class="muted">Login with your mobile number or Gmail + password, and your old data will come back. No OTP needed.</p>
<?php if ($err): ?><div class="err"><?= ch_esc($err) ?></div><?php endif; ?>
<?php if ($notice): ?><div class="ok"><?= ch_esc($notice) ?></div><?php endif; ?>
<?php if ($refUser): ?><div class="card"><div class="note">🎉 You came with <b><?= ch_esc($refUser['name']) ?></b>'s referral link — the code is filled in below.</div></div><?php endif; ?>

<div class="card"><h2>Login</h2>
<form method="post">
<input type="hidden" name="form" value="login">
<label>Mobile number or Gmail</label><input name="login_id" required placeholder="01XXXXXXXXX or you@gmail.com">
<label>Password</label><input name="password" required type="password" placeholder="Your password">
<button type="submit">Login</button>
</form></div>

<div class="card"><h2>Create Account</h2>
<form method="post">
<input type="hidden" name="form" value="signup">
<label>Your Name</label><input name="name" required minlength="3" placeholder="Your name" value="<?= ch_esc($_POST['name'] ?? '') ?>">
<label>Mobile Number</label><input name="phone" required pattern="01[0-9]{9}" inputmode="numeric" placeholder="01XXXXXXXXX" value="<?= ch_esc($_POST['phone'] ?? '') ?>">
<label>Gmail Address</label><input name="email" required type="email" placeholder="you@gmail.com" value="<?= ch_esc($_POST['email'] ?? '') ?>">
<label>Password</label><input name="password" required type="password" minlength="6" placeholder="Choose a password (min 6)">
<label>Referral Code (optional)</label><input name="ref_code" placeholder="e.g. CH123456" value="<?= ch_esc($_POST['ref_code'] ?? $ref) ?>">
<p class="muted">Referral bonus counts ONLY if this code matches an existing account. Wrong or empty code = no bonus.</p>
<button type="submit">Create Account</button>
</form></div>
</div></body></html>
