<?php
// CourseHaat BD — settings for the simple version
// Change price / numbers only in this file.
$SITE = [
    'name'   => 'CourseHaat BD',
    'tagline'=> 'Digital Course Marketplace',
    'bkash'  => '01774669723', // bKash Personal
    'nagad'  => '01910190761', // Nagad Personal
    // The Telegram link is not written here. It comes from the Render TELEGRAM_LINK env.
    'telegram' => getenv('TELEGRAM_LINK') ?: '',
];

$COURSES = [
    [
        'id'    => 'facebook-monetization',
        'icon'  => '📘',
        'name'  => 'Facebook Page Monetization',
        'desc'  => 'The full process of starting to earn from a Facebook page, right from the beginning.',
        'price' => 200,
    ],
    [
        'id'    => 'youtube-monetization',
        'icon'  => '▶️',
        'name'  => 'YouTube Monetization',
        'desc'  => 'The full process of getting monetization for a YouTube channel, right from the beginning.',
        'price' => 200,
    ],
];

$FB_SYSTEMS = [
    'Professional Page setup and all important settings',
    '7–10 day monetization roadmap',
    'Complete tutorial for creating faceless videos',
    'AI Automation: script, voice and editing',
    'AI voiceover tutorial',
    'Mobile video editing (CapCut)',
    'Mobile thumbnail design',
    'Reels viral strategies',
    'Content ideas and niche selection',
    'Copyright-safe content rules',
    'Monetization application and rejection solutions',
];

$YT_SYSTEMS = [
    'Channel creation and complete setup',
    'Monetization plan: subscribers and watch time',
    'Complete tutorial for creating faceless videos',
    'AI Automation: script, voice and editing',
    'AI voiceover tutorial',
    'Mobile video editing (CapCut)',
    'Mobile thumbnail design',
    'Shorts viral strategies',
    'Title, description and hashtag SEO',
    'Copyright-safe content rules',
    'Monetization application and rejection solutions',
];
