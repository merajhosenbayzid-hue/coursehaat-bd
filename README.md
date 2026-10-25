# CourseHaat BD — Simple Version

A simple version made by leaving out the complex parts of the live site. All text is in English.

## What is included
- 2 courses, BDT 200 each: Facebook Page Monetization, YouTube Monetization
- 11 lessons in each course
- Buy steps: choose a course → Send Money to bKash 01774669723 / Nagad 01910190761 → name + mobile + TrxID → order successful → Telegram link
- Duplicate TrxID is blocked, orders are written to data/orders.csv

## What is left out (on purpose)
- No Three.js / 3D animation, typing / lightning animation
- No account, login, OTP, dashboard, referral bonus, admin panel
- Telegram link is not written in the code — it comes from Render's TELEGRAM_LINK env

## To put it live (only with the user's permission)
1. Replace the old php-site files with this folder's index.php, order.php, config.php, assets/logo.webp
2. Push to GitHub
3. Manual deploy on Render (no auto-deploy)
4. Render env TELEGRAM_LINK must stay as before

Warning: Render's free plan has no disk — orders.csv may be deleted on redeploy/restart, same as the earlier version.
