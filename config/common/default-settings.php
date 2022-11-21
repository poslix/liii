<?php

return [
    // LINKS
    ['name' => 'links.default_type', 'value' => 'direct'],
    ['name' => 'links.enable_type', 'value' => true],
    ['name' => 'links.redirect_time', 'value' => 10],
    ['name' => 'links.retargeting', 'value' => true],
    ['name' => 'links.pixels', 'value' => true],
    ['name' => 'links.homepage_creation', 'value' => true],
    ['name' => 'links.homepage_stats', 'value' => true],
    ['name' => 'links.home_expiration', 'value' => '1day'],
    ['name' => 'links.alias_min', 'value' => 5],
    ['name' => 'links.alias_max', 'value' => 10],
    ['name' => 'links.min_len', 'value' => 3],
    ['name' => 'links.max_len', 'value' => 1000],
    ['name' => 'links.alias_content', 'value' => 'alpha_dash'],

    // HOMEPAGE APPEARANCE
    ['name' => 'homepage.appearance', 'value' => json_encode([
        'headerTitle' => 'Create Click-Worthy Links',
        'headerSubtitle' => 'BeLink helps you maximize the impact of every digital initiative with industry-leading features and tools.',
        'headerImage' => 'client/assets/images/landing/landing-bg.svg',
        'headerImageOpacity' => 1,
        'headerOverlayColor1' => null,
        'headerOverlayColor2' => null,
        'footerTitle' => 'The easiest way to get more clicks with custom links.',
        'footerSubtitle' => 'Attract More Clicks Now',
        'footerImage' => 'client/assets/images/landing/landing-bg.svg',
        'actions' => [
            'inputText' => 'Paste a long url',
            'inputButton' => 'Shorten',
            'cta1' => 'Get Started',
            'cta2' => 'Learn More',
        ],
        'primaryFeatures' => [
            [
                'title' => 'Password Protect',
                'subtitle' => 'Set a password to protect your links from unauthorized access.',
                'image' => 'authentication.svg',
            ],
            [
                'title' => 'Retargeting',
                'subtitle' => 'Add retargeting pixels to your links and turn every URL into perfectly targeted ads.',
                'image' => 'right-direction.svg',
            ],
            [
                'title' => 'Groups',
                'subtitle' => 'Group links together for easier management and analytics for a group as well as individual links.',
                'image' => 'add-file.svg',
            ]
        ],
        'secondaryFeatures' => [
            [
                'title' => "The only link you'll ever need.",
                'subtitle' => 'Link in bio',
                'description' => 'Biolink is the launchpad to your latest video,  song, article, recipe, tour, store, website, social post - everywhere you are online.',
                'image' => 'client/assets/images/landing/biolink.png',
            ],
            [
                'title' => 'Monitor your link performance.',
                'subtitle' => 'ADVANCED ANALYTICS',
                'description' => 'Full analytics for individual links and link groups, including geo and device information, referrers, browser, ip and more.',
                'image' => 'client/assets/images/landing/stats.png',
            ],
            [
                'title' => 'Manage your links.',
                'subtitle' => 'FULLY-FEATURED DASHBOARD',
                'description' => 'Control everything from the dashboard. Manage your URLs, groups, custom pages, pixels, custom domains and more.',
                'image' => 'client/assets/images/landing/dashboard.png',
            ]
        ]
    ])],

    // menus
    ['name' => 'menus', 'value' => json_encode([
        [
            'name' => 'User Dashboard',
            'position' => 'dashboard-sidebar',
            'items' => [
                ['type' => 'route', 'position' => 0, 'activeExact' => true, 'label' => 'Dashboard', 'action' => 'dashboard', 'icon' => 'home'],
                ['type' => 'route', 'position' => 1, 'label' => 'Links', 'action' => 'dashboard/links', 'icon' => 'link'],
                ['type' => 'route', 'position' => 2, 'label' => 'Biolinks', 'action' => 'dashboard/biolinks', 'icon' => 'instagram'],
                ['type' => 'route', 'position' => 3, 'label' => 'Link Groups', 'action' => 'dashboard/link-groups', 'icon' => 'dashboard'],
                ['type' => 'route', 'position' => 4, 'label' => 'Custom Domains', 'action' => 'dashboard/custom-domains', 'icon' => 'www'],
                ['type' => 'route', 'position' => 5, 'label' => 'Link Overlays', 'action' => 'dashboard/link-overlays', 'icon' => 'tooltip'],
                ['type' => 'route', 'position' => 6, 'label' => 'Link Pages', 'action' => 'dashboard/link-pages', 'icon' => 'page'],
                ['type' => 'route', 'position' => 7, 'label' => 'Tracking Pixels', 'action' => 'dashboard/pixels', 'icon' => 'tracking'],
                ['type' => 'route', 'position' => 8, 'label' => 'Workspaces', 'action' => 'dashboard/workspaces', 'icon' => 'people']
            ]
        ],
        [
            'name' => 'footer',
            'position' => 'footer',
            'items' => [
                ['type' => 'route', 'position' => 1, 'label' => 'Developers', 'action' => '/api-docs', 'condition' => 'auth'],
                ['type' => 'route', 'position' => 2, 'label' => 'Privacy Policy', 'action' => '/pages/1/privacy-policy'],
                ['type' => 'route', 'position' => 3, 'label' => 'Terms of Service', 'action' => '/pages/2/terms-of-service'],
                ['type' => 'route', 'position' => 4, 'label' => 'Contact Us', 'action' => '/contact']
            ],
        ],
        [
            'name' => 'Footer Social',
            'position' => 'footer-secondary',
            'items' => [
                ['type' => 'link', 'position' => 1, 'icon' => 'facebook-square', 'action' => 'https://facebook.com'],
                ['type' => 'link', 'position' => 2, 'icon' => 'twitter', 'action' => 'https://twitter.com'],
                ['type' => 'link', 'position' => 3, 'icon' => 'instagram', 'action' => 'https://instagram.com'],
                ['type' => 'link', 'position' => 4, 'icon' => 'youtube', 'action' => 'https://youtube.com'],
            ],
        ]
    ])],

    // custom domains
    ['name' => 'custom_domains.allow_select', 'value' => true],
];
