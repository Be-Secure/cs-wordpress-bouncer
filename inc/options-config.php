<?php

use CrowdSecBouncer\Constants;

function getCrowdSecOptionsConfig()
{
    return [
        ['name' => 'crowdsec_api_url', 'default' => '', 'autoInit' => true],
        ['name' => 'crowdsec_api_key', 'default' => '', 'autoInit' => true],
        ['name' => 'crowdsec_bouncing_level', 'default' => Constants::BOUNCING_LEVEL_NORMAL, 'autoInit' => true],
        ['name' => 'crowdsec_public_website_only', 'default' => true, 'autoInit' => true],
        ['name' => 'crowdsec_stream_mode', 'default' => false, 'autoInit' => true],
        ['name' => 'crowdsec_stream_mode_refresh_frequency', 'default' => 60, 'autoInit' => true],
        ['name' => 'crowdsec_cache_system', 'default' => Constants::CACHE_SYSTEM_PHPFS, 'autoInit' => true],
        ['name' => 'crowdsec_redis_dsn', 'default' => '', 'autoInit' => true],
        ['name' => 'crowdsec_memcached_dsn', 'default' => '', 'autoInit' => true],
        ['name' => 'crowdsec_clean_ip_cache_duration', 'default' => Constants::CACHE_EXPIRATION_FOR_CLEAN_IP, 'autoInit' => true],
        ['name' => 'crowdsec_bad_ip_cache_duration', 'default' => Constants::CACHE_EXPIRATION_FOR_BAD_IP, 'autoInit' => true],
        ['name' => 'crowdsec_fallback_remediation', 'default' => Constants::REMEDIATION_CAPTCHA, 'autoInit' => true],
        ['name' => 'crowdsec_hide_mentions', 'default' => false, 'autoInit' => true],
        ['name' => 'crowdsec_trust_ip_forward', 'default' => '', 'autoInit' => true],
        ['name' => 'crowdsec_trust_ip_forward_array', 'default' => [], 'autoInit' => true],
        ['name' => 'crowdsec_theme_color_text_primary', 'default' => 'black', 'autoInit' => true],
        ['name' => 'crowdsec_theme_color_text_secondary', 'default' => '#AAA', 'autoInit' => true],
        ['name' => 'crowdsec_theme_color_text_button', 'default' => 'white', 'autoInit' => true],
        ['name' => 'crowdsec_theme_color_text_error_message', 'default' => '#b90000', 'autoInit' => true],
        ['name' => 'crowdsec_theme_color_background_page', 'default' => '#eee', 'autoInit' => true],
        ['name' => 'crowdsec_theme_color_background_container', 'default' => 'white', 'autoInit' => true],
        ['name' => 'crowdsec_theme_color_background_button', 'default' => '#626365', 'autoInit' => true],
        ['name' => 'crowdsec_theme_color_background_button_hover', 'default' => '#333', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_captcha_wall_tab_title', 'default' => 'Oops..', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_captcha_wall_title', 'default' => 'Hmm, sorry but...', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_captcha_wall_subtitle', 'default' => 'Please complete the security check.', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_captcha_wall_refresh_image_link', 'default' => 'refresh image', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_captcha_wall_captcha_placeholder', 'default' => 'Type here...', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_captcha_wall_send_button', 'default' => 'CONTINUE', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_captcha_wall_error_message', 'default' => 'Please try again.', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_captcha_wall_footer', 'default' => '', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_ban_wall_tab_title', 'default' => 'Oops..', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_ban_wall_title', 'default' => '🤭 Oh!', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_ban_wall_subtitle', 'default' => 'This page is protected against cyber attacks and your IP has been banned by our system.', 'autoInit' => true],
        ['name' => 'crowdsec_theme_text_ban_wall_footer', 'default' => '', 'autoInit' => true],
        ['name' => 'crowdsec_theme_custom_css', 'default' => '', 'autoInit' => true],
        ['name' => 'crowdsec_random_log_folder', 'default' => bin2hex(random_bytes(64)), 'autoInit' => false],
        ['name' => 'crowdsec_debug_mode', 'default' => false, 'autoInit' => true],
		['name' => 'crowdsec_display_errors', 'default' => false, 'autoInit' => true],
    ];
}
