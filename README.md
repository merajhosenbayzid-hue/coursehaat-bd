# CourseHaat BD — Simple Version

A simple version made by leaving out the complex parts of the live site. All text is in English.

## What is included
- 2 courses, BDT 200 each: Facebook Page Monetization, YouTube Monetization
- 11 lessons in each course
- Buy steps: choose a course → Send Money to bKash 01774669723 / Nagad 01910190761 → name + mobile + TrxID → order successful → Telegram link
- Duplicate TrxID is blocked, orders are written to data/orders.csv

## Referral (added Oct 25, 2026, user's order)
- Account: name + phone + Gmail + password (min 6), no OTP. Login with phone or Gmail + password restores old data.
- Every account gets a referral code (CH + 6 digits) and a link: account.php?ref=CODE
- Signup referral code must match an existing code, otherwise no referrer is attached and no bonus counts.
- When a referred person buys a course (order submitted with TrxID), the referrer gets 100 BDT for their first successful referral and 50 BDT for every later referral, unlimited referrals.
- One buyer counts only once (their first order), even if they buy both courses.
- Dashboard (dashboard.php) shows referral code, link, successful referrals, bonus balance and history. Bonus is a balance shown in the dashboard; there is no automatic bKash/Nagad payout in this version.
- Data: data/users.csv, data/referrals.csv, data/orders.csv (data/.htaccess blocks web access).

## What is left out (on purpose)
- No Three.js / 3D animation, typing / lightning animation
- No OTP, admin panel
- Telegram link is not written in the code — it comes from Render's TELEGRAM_LINK env

## To put it live (only with the user's permission)
1. Replace the old php-site files with this folder's index.php, order.php, config.php, assets/logo.webp
2. Push to GitHub
3. Manual deploy on Render (no auto-deploy)
4. Render env TELEGRAM_LINK must stay as before

Warning: Render's free plan has no disk — orders.csv may be deleted on redeploy/restart, same as the earlier version.
