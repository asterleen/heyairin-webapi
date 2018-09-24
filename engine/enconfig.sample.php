<?php
/*

                HeyAirin Web API
    Licensed under GNU GPL v3, see LICENSE file
       by Asterleen ~ https://asterleen.com
	      
*/

/*
	This is a configuration file sample.
	Modify it with your preferences and
	rename to enconfig.php
*/

// General
define ('BASE_DOMAIN', 'example.com');

// Database
define ('DB_HOST', 'localhost');
define ('DB_NAME', 'database');
define ('DB_USER', 'user');
define ('DB_PASSWORD', 'hackme');

// VK Auth
define ('VK_CLIENT_ID', 31337);
define ('VK_CLIENT_SECRET', '_top_secret_');
define ('VK_REDIRECT_URI', 'https://'.BASE_DOMAIN.'/api/auth/vk');
define ('VK_API_VERSION', '5.73');

// Facebook Auth
define ('FB_CLIENT_ID', 31337);
define ('FB_CLIENT_SECRET', '_top_secret_');
define ('FB_REDIRECT_URI', 'https://'.BASE_DOMAIN.'/api/auth/fb');


// System settings
define ('DEFAULT_AMOUNT', 50);
define ('MAX_MESSAGE_AMOUNT', 1024);
define ('TEMP_CODE_SALT', 'change_this_to_pure_randomness');
define ('CURRENT_TRACK_FILE', '');
define ('CURRENT_TAG_FILE', '');