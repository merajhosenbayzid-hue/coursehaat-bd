# CourseHaat BD

PHP ওয়েবসাইট — Facebook Page Monetization ও YouTube Monetization কোর্স বিক্রি (প্রতিটি ১০০ টাকা), প্যাকেজে ১৫টি সিস্টেম।

## গঠন
- `index.php` — হোমপেজ (Three.js ফুল-পেজ 3D অ্যানিমেশন: একটাই ফিক্সড ক্যানভাস উপর থেকে নিচ পর্যন্ত, মাউস প্যারালাক্স + স্ক্রল ক্যামেরা), কোর্স কার্ড, ১৫ সিস্টেমের তালিকা, কেনার মডাল
- `order.php` — অর্ডার গ্রহণ: নাম/মোবাইল/TrxID যাচাই, ডুপ্লিকেট TrxID আটকায়, অর্ডার `data/orders.csv`-তে লেখে, সফল হলে Telegram জয়েন বাটন দেখায়
- `config.php` — সব সেটিংস এক জায়গায়: পেমেন্ট নম্বর, Telegram লিংক (সার্ভার-সাইড, পেজ সোর্সে থাকে না), কোর্স ও সিস্টেমের তালিকা
- `Dockerfile` + `start.sh` — Render-এ PHP (Apache) চালানোর জন্য, Render-এর `$PORT` ব্যবহার করে
- `render.yaml` — Render Blueprint (নাম: coursehaat-bd, Free, Singapore, Docker)

## Render-এ ডেপ্লয়
1. এই repo GitHub-এ পুশ করুন (public) — Telegram লিংক repo-তে থাকে না
2. Render → New → Web Service → public repo URL দিন (অথবা Blueprint দিয়ে render.yaml ব্যবহার করুন)
3. Environment: Docker, Plan: Free → Create
4. সার্ভিসের Environment-এ যোগ করুন: `TELEGRAM_LINK` = প্রাইভেট Telegram গ্রুপের invite লিংক (order.php শুধু অর্ডারের পর এটা দেখায়)
5. লাইভ URL হবে: https://coursehaat-bd.onrender.com

নোট: Render Free প্ল্যানে সার্ভিস ঘুমিয়ে যায় (প্রথম লোডে ৩০–৬০ সেকেন্ড লাগতে পারে) এবং ফাইলসিস্টেম স্থায়ী নয় — সার্ভিস রিস্টার্ট হলে `data/orders.csv` মুছে যেতে পারে।
