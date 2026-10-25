<?php
// CourseHaat BD — simple account + referral system (Oct 25, 2026)
// Rules (user's order): every account gets a referral code (CH + 6 digits).
// If someone signs up with a matching code and then buys (order/TrxID submitted),
// the referrer gets 100 BDT for their FIRST successful referral, 50 BDT for
// every later one, unlimited referrals. A wrong/missing code = no bonus.
// One buyer is credited only once (their first order), even if they buy both courses.
// No OTP/verification. Data lives in data/ CSV files (blocked from web by data/.htaccess).
// NOTE: Render free plan has no persistent disk — users/referrals/orders in data/
// can be lost on redeploy/restart, same as orders.csv in the simple version.
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function ch_data_dir() {
    $d = getenv('ORDERS_DIR') ?: (__DIR__ . '/data');
    if (!is_dir($d)) { @mkdir($d, 0770, true); }
    return rtrim($d, '/');
}
function ch_users_file() { return ch_data_dir() . '/users.csv'; }
function ch_refs_file() { return ch_data_dir() . '/referrals.csv'; }

function ch_read_csv($file) {
    if (!is_file($file)) return [];
    $rows = [];
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $p = str_getcsv($line);
        if ($p) $rows[] = $p;
    }
    return $rows;
}
function ch_append_csv($file, $row) {
    $fh = fopen($file, 'a');
    if (!$fh) return false;
    if (flock($fh, LOCK_EX)) { fputcsv($fh, $row); flock($fh, LOCK_UN); }
    fclose($fh);
    return true;
}

// users.csv columns: created, name, phone, email, ref_code, referrer_code, pass_hash
function ch_all_users() {
    $out = [];
    foreach (ch_read_csv(ch_users_file()) as $p) {
        if (count($p) < 6) continue;
        $out[] = ['created' => $p[0], 'name' => $p[1], 'phone' => $p[2], 'email' => $p[3], 'ref_code' => $p[4], 'referrer_code' => $p[5], 'pass_hash' => ($p[6] ?? '')];
    }
    return $out;
}
function ch_write_all_users($users) {
    $fh = fopen(ch_users_file(), 'w');
    if (!$fh) return false;
    if (flock($fh, LOCK_EX)) {
        foreach ($users as $u) fputcsv($fh, [$u['created'], $u['name'], $u['phone'], $u['email'], $u['ref_code'], $u['referrer_code'], $u['pass_hash']]);
        flock($fh, LOCK_UN);
    }
    fclose($fh);
    return true;
}
function ch_find_user_by_phone($phone) { foreach (ch_all_users() as $u) if ($u['phone'] === $phone) return $u; return null; }
function ch_find_user_by_email($email) { foreach (ch_all_users() as $u) if (strcasecmp($u['email'], $email) === 0) return $u; return null; }
function ch_find_user_by_code($code) { foreach (ch_all_users() as $u) if ($u['ref_code'] === $code) return $u; return null; }
function ch_make_ref_code() {
    do { $c = 'CH' . str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT); }
    while (ch_find_user_by_code($c) !== null);
    return $c;
}
function ch_login_user($user) { $_SESSION['ch_user_phone'] = $user['phone']; }
function ch_current_user() {
    $ph = $_SESSION['ch_user_phone'] ?? '';
    if ($ph === '') return null;
    return ch_find_user_by_phone($ph);
}
function ch_logout() { unset($_SESSION['ch_user_phone']); }

// referrals.csv columns: date, referrer_code, buyer_phone, buyer_name, course_id, amount
function ch_referral_rows($code = null) {
    $out = [];
    foreach (ch_read_csv(ch_refs_file()) as $p) {
        if (count($p) < 6) continue;
        $r = ['date' => $p[0], 'referrer_code' => $p[1], 'buyer_phone' => $p[2], 'buyer_name' => $p[3], 'course_id' => $p[4], 'amount' => (int)$p[5]];
        if ($code === null || $r['referrer_code'] === $code) $out[] = $r;
    }
    return $out;
}
function ch_referral_balance($code) { $s = 0; foreach (ch_referral_rows($code) as $r) $s += $r['amount']; return $s; }
function ch_has_credit_for_buyer($buyerPhone) {
    foreach (ch_referral_rows() as $r) if ($r['buyer_phone'] === $buyerPhone) return true;
    return false;
}
// Buyer's first order credits the referrer: first successful referral 100, later 50 each.
// Same buyer is never credited twice.
function ch_credit_referral($buyerPhone, $buyerName, $courseId) {
    $buyer = ch_find_user_by_phone($buyerPhone);
    if (!$buyer || empty($buyer['referrer_code'])) return 0;
    if (ch_has_credit_for_buyer($buyerPhone)) return 0;
    $referrer = ch_find_user_by_code($buyer['referrer_code']);
    if (!$referrer) return 0;
    $prior = count(ch_referral_rows($referrer['ref_code']));
    $amount = ($prior === 0) ? 100 : 50;
    ch_append_csv(ch_refs_file(), [date('c'), $referrer['ref_code'], $buyerPhone, $buyerName, $courseId, $amount]);
    return $amount;
}
function ch_site_url() {
    $host = $_SERVER['HTTP_HOST'] ?? 'coursehaat-bd.onrender.com';
    $sch = 'http';
    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || substr($host, -13) === '.onrender.com') $sch = 'https';
    return $sch . '://' . $host . '/';
}
function ch_ref_link($code) { return ch_site_url() . 'account.php?ref=' . urlencode($code); }
function ch_esc($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function ch_save_ref_from_request() {
    if (isset($_GET['ref']) && preg_match('/^CH\d{6}$/', $_GET['ref'])) $_SESSION['ch_ref'] = $_GET['ref'];
}
function ch_saved_ref() { return $_SESSION['ch_ref'] ?? ''; }
