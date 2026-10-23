<?php
// ====== CourseHaat BD — মালিকের সেটিংস (শুধু এই ফাইল বদলালেই হবে) ======
$SITE = [
    'name'     => 'CourseHaat BD',
    'tagline'  => 'ডিজিটাল কোর্সের হাট',
    // পেমেন্ট নম্বর (দুটোই Personal) — চেকআউটে ক্রেতাকে দেখানো হয়
    'bkash'    => '01774669723',
    'nagad'    => '01910190761',
    // Telegram প্রাইভেট গ্রুপের লিংক — পাবলিক repo-তে রাখা হয় না।
    // Render-এ TELEGRAM_LINK env var থেকে আসে; লোকালে config.local.php (git-এর বাইরে) থেকে।
    'telegram' => getenv('TELEGRAM_LINK') ?: '',
];
if ($SITE['telegram'] === '' && is_file(__DIR__ . '/config.local.php')) {
    $LOCAL = require __DIR__ . '/config.local.php';
    if (!empty($LOCAL['telegram'])) $SITE['telegram'] = $LOCAL['telegram'];
}

$COURSES = [
    [
        'id'    => 'facebook-monetization',
        'icon'  => '📘',
        'name'  => 'Facebook Page Monetization',
        'desc'  => 'ফেসবুক পেজ থেকে আয় শুরু করার পুরো প্রসেস, একদম শুরু থেকে।',
        'price' => 100,
    ],
    [
        'id'    => 'youtube-monetization',
        'icon'  => '▶️',
        'name'  => 'YouTube Monetization',
        'desc'  => 'ইউটিউব চ্যানেল মনিটাইজেশন পাওয়ার পুরো প্রসেস, একদম শুরু থেকে।',
        'price' => 100,
    ],
];

// ১০০ টাকার প্যাকেজে যা যা শিখতে পারবে (১৫টি সিস্টেম)
$SYSTEMS = [
    'Professional Facebook Page সেটআপ — সব সেটিংস',
    '৭–১০ দিনের পেজ মনিটাইজেশন রোডম্যাপ',
    'YouTube চ্যানেল তৈরি ও পূর্ণ সেটআপ',
    'YouTube মনিটাইজেশন সিস্টেম (সাব + ওয়াচ-টাইম প্ল্যান)',
    'Faceless ভিডিও তৈরির টিউটোরিয়াল',
    'AI Automation — স্ক্রিপ্ট, ভয়েস, এডিট AI টুল দিয়ে',
    'AI ভয়েসওভার টিউটোরিয়াল',
    'মোবাইলে ভিডিও এডিটিং (CapCut)',
    'মোবাইলে থাম্বনেইল ডিজাইন',
    'Reels / Shorts ভাইরাল সিস্টেম',
    'কনটেন্ট আইডিয়া ও নিশ বাছাই সিস্টেম',
    'কপিরাইট-সেফ কনটেন্টের নিয়ম (reused content এড়ানো)',
    'টাইটেল, ডেসক্রিপশন, হ্যাশট্যাগ SEO',
    'আপলোড টাইমিং ও কনসিস্টেন্সি প্ল্যান',
    'মনিটাইজেশন Apply স্টেপ বাই স্টেপ + রিজেকশন ফিক্স',
];
