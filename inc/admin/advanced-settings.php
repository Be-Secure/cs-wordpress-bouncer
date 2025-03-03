<?php

use CrowdSecBouncer\BouncerException;
use CrowdSecBouncer\Constants;
use IPLib\Factory;

function adminAdvancedSettings()
{
    /***************************
     ** Section "Stream mode" **
     **************************/

    add_settings_section('crowdsec_admin_advanced_stream_mode', 'Communication mode to the API', function () {
    }, 'crowdsec_advanced_settings');

    // Field "crowdsec_stream_mode"
    addFieldCheckbox('crowdsec_stream_mode', 'Enable the "Stream" mode', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_stream_mode', function () {
        // Stream mode just activated.
        $bouncer = getBouncerInstance();
        $result = $bouncer->warmBlocklistCacheUp()['count'];
        $message = __('As the stream mode is enabled, the cache has just been warmed up, '.($result > 0 ? 'there are now '.$result.' decisions' : 'there is now '.$result.' decision').' in cache.');
        AdminNotice::displaySuccess($message);
        scheduleBlocklistRefresh();
    }, function () {
        // Stream mode just deactivated.
        unscheduleBlocklistRefresh();
    }, '
    <p>With the stream mode, every decision is retrieved in an asynchronous way. 3 advantages: <br>&nbsp;1) Inivisible latency when loading pages<br>&nbsp;2) The IP verifications works even if your CrowdSec is not reachable.<br>&nbsp;3) The API can never be overloaded by the WordPress traffic</p>
    <p>Note: This method has one limit: all the decisions updates since the previous resync will not be taken in account until the next resync.</p>'.
        (get_option('crowdsec_stream_mode') ?
            '<p><input id="crowdsec_refresh_cache" style="margin-right:10px" type="button" value="Refresh the cache now" class="button button-secondary button-small" onclick="document.getElementById(\'crowdsec_action_refresh_cache\').submit();"></p>' :
            '<p><input id="crowdsec_refresh_cache" style="margin-right:10px" type="button" disabled="disabled" value="Refresh the cache now" class="button button-secondary button-small"></p>'));

    // Field "crowdsec_stream_mode_refresh_frequency"
    addFieldString('crowdsec_stream_mode_refresh_frequency', 'Resync decisions each<br>(stream mode only)', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_stream_mode', function ($input) {
        $input = (int) $input;
        if ($input < 1) {
            $input = 1;
            add_settings_error('Resync decisions each', 'crowdsec_error', 'The "Resync decisions each" value should be more than 1sec (WP_CRON_LOCK_TIMEOUT). We just reset the frequency to 1 seconds.');

            return $input;
        }

        // Update wp-cron schedule.
        if ((bool) get_option('crowdsec_stream_mode')) {
            $bouncer = getBouncerInstance();
            $result = $bouncer->warmBlocklistCacheUp()['count'];
            $message = __('As the stream mode refresh duration changed, the cache has just been warmed up, '.($result > 0 ? 'there are now '.$result.' decisions' : 'there is now '.$result.' decision').' in cache.');
            AdminNotice::displaySuccess($message);
            scheduleBlocklistRefresh();
        }

        return $input;
    }, ' seconds. <p>Our advice is 60 seconds (as WordPress ignores durations under this value <a href="https://wordpress.stackexchange.com/questions/100104/better-handling-of-wp-cron-server-load-abuse" target="_blank">see WP_CRON_LOCK_TIMEOUT</a>).<br>'.
    ' If you need a shorter delay between each resync, you can <strong>go down to 1 sec</strong>.<br>'.
    ' But as mentionned is the WordPress Developer Documentation, you should considere hooking WP-Cron Into the System Task Scheduler'.
    ' by yourself and reduce the WP_CRON_LOCK_TIMEOUT value to the same value as you set here. '.
    '<a href="https://developer.wordpress.org/plugins/cron/hooking-wp-cron-into-the-system-task-scheduler/" target="_blank">'.
    'Here is explained how</a>.</p>', '...', 'width: 115px;', 'number');

    /*********************
     ** Section "Cache" **
     ********************/

    add_settings_section('crowdsec_admin_advanced_cache', 'Caching configuration <input id="crowdsec_clear_cache" style="margin-left: 7px;margin-top: -3px;" type="button" value="Clear now" class="button button-secondary button-small" onclick="if (confirm(\'Are you sure you want to completely clear the cache?\')) document.getElementById(\'crowdsec_action_clear_cache\').submit();">', function () {
        ?>
        <p>Polish the decisions cache settings by selecting the best technology or the cache durations best suited to your use.</p>
<?php
    }, 'crowdsec_advanced_settings');

    // Field "crowdsec_redis_dsn"
    addFieldString('crowdsec_redis_dsn', 'Redis DSN<br>(if applicable)', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_cache', function ($input) {
        return $input;
    }, '<p>Fill in this field only if you have chosen the Redis cache.<br>Example of DSN: redis://localhost:6379.', 'redis://...', '');

    // Field "crowdsec_memcached_dsn"
    addFieldString('crowdsec_memcached_dsn', 'Memcached DSN<br>(if applicable)', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_cache', function ($input) {
        return $input;
    }, '<p>Fill in this field only if you have chosen the Memcached cache.<br>Example of DSN: memcached://localhost:11211.', 'memcached://...', '');

    // Field "crowdsec_cache_system"
    addFieldSelect('crowdsec_cache_system', 'Technology', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_cache', function ($input) {
        if (!in_array($input, [Constants::CACHE_SYSTEM_PHPFS, Constants::CACHE_SYSTEM_REDIS, Constants::CACHE_SYSTEM_MEMCACHED])) {
            $input = Constants::CACHE_SYSTEM_PHPFS;
            add_settings_error('Technology', 'crowdsec_error', 'Technology: Incorrect cache technology selected.');
        }

        try {
            $bouncer = getBouncerInstance();
            try {
                $bouncer->clearCache();
            } catch (BouncerException $e) {
                $cacheSystem = esc_attr(get_option('crowdsec_cache_system'));
                switch ($cacheSystem) {
                    case Constants::CACHE_SYSTEM_MEMCACHED:
                        throw new WordpressCrowdSecBouncerException('Unable to connect Memcached.'.
                            ' Please fix the Memcached DSN or select another cache technology.');
                        break;

                    case Constants::CACHE_SYSTEM_REDIS:
                        throw new WordpressCrowdSecBouncerException('Unable to connect Redis.'.
                            ' Please fix the Redis DSN or select another cache technology.');
                    default:
                    throw new WordpressCrowdSecBouncerException('Unable to connect the cache system: '.$e->getMessage());
                }
            }

            $message = __('Cache system changed. Previous cache data has been cleared.');
        } catch (WordpressCrowdSecBouncerException $e) {
        }

        try {
            // Reload bouncer instance with the new cache system and so test if dsn is correct.
            getCacheAdapterInstance($input);
            try {
                // Try the adapter connection (Redis or Memcached will crash if the connection is incorrect)
                $bouncer = getBouncerInstance($input);
                $bouncer->testConnection();
            } catch (BouncerException $e) {
                throw new WordpressCrowdSecBouncerException($e->getMessage());
            }
        } catch (WordpressCrowdSecBouncerException $e) {
            AdminNotice::displayError($e->getMessage());
        }

        try {
            try {
                //Update wp-cron schedule if stream mode is enabled
                if ((bool) get_option('crowdsec_stream_mode')) {
                    $bouncer = getBouncerInstance($input); // Reload bouncer instance with the new cache system
                    $result = $bouncer->warmBlocklistCacheUp();
                    $count = $result['count'];
                    $message = __('As the stream mode is enabled, the cache has just been warmed up, '.($count > 0 ? 'there are now '.$count.' decisions' : 'there is now '.$count.' decision').' in cache.');
                    AdminNotice::displaySuccess($message);
                    scheduleBlocklistRefresh();
                }
            } catch (WordpressCrowdSecBouncerException $e) {
                AdminNotice::displayError($e->getMessage());
            }
        } catch (BouncerException $e) {
            AdminNotice::displayError($e->getMessage());
        }

        return $input;
    }, ((Constants::CACHE_SYSTEM_PHPFS === get_option('crowdsec_cache_system')) ?
        '<input style="margin-right:10px" type="button" id="crowdsec_prune_cache" value="Prune now" class="button button-secondary" onclick="document.getElementById(\'crowdsec_action_prune_cache\').submit();">' : '').
        '<p>The File system cache is faster than calling LAPI. Redis or Memcached is faster than the File System cache.</p>', [
        Constants::CACHE_SYSTEM_PHPFS => 'File system',
        Constants::CACHE_SYSTEM_REDIS => 'Redis',
        Constants::CACHE_SYSTEM_MEMCACHED => 'Memcached',
    ]);

    // Field "crowdsec_clean_ip_cache_duration"
    addFieldString('crowdsec_clean_ip_cache_duration', 'Recheck clean IPs each<br>(live mode only)', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_cache', function ($input) {
        if (!get_option('crowdsec_stream_mode') && (int) $input <= 0) {
            add_settings_error('Recheck clean IPs each', 'crowdsec_error', 'Recheck clean IPs each: Minimum is 1 second.');

            return '1';
        }

        return $input;
    }, ' seconds. <p>The duration between re-asking LAPI about an already checked clean IP.<br>Minimum 1 second.<br> Note that this setting can not be apply in stream mode.', '...', 'width: 115px;', 'number', (bool) get_option('crowdsec_stream_mode'));

    // Field "crowdsec_bad_ip_cache_duration"
    addFieldString('crowdsec_bad_ip_cache_duration', 'Recheck bad IPs each<br>(live mode only)', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_cache', function ($input) {
        if (!get_option('crowdsec_stream_mode') && (int) $input <= 0) {
            add_settings_error('Recheck bad IPs each', 'crowdsec_error', 'Recheck bad IPs each: Minimum is 1 second.');

            return '1';
        }

        return $input;
    }, ' seconds. <p>The duration between re-asking LAPI about an already checked bad IP.<br>Minimum 1 second.<br> Note that this setting can not be apply in stream mode.', '...', 'width: 115px;', 'number', (bool) get_option('crowdsec_stream_mode'));

    /***************************
     ** Section "Remediation" **
     **************************/

    add_settings_section('crowdsec_admin_advanced_remediations', 'Remediations', function () {
        echo 'Configuration some details about remediations.';
    }, 'crowdsec_advanced_settings');

    // Field "crowdsec_fallback_remediation"
    $choice = [];
    foreach (Constants::ORDERED_REMEDIATIONS as $remediation) {
        $choice[$remediation] = $remediation;
    }
    addFieldSelect('crowdsec_fallback_remediation', 'Fallback to', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings',
    'crowdsec_admin_advanced_remediations', function ($input) {
        if (!in_array($input, Constants::ORDERED_REMEDIATIONS)) {
            $input = Constants::BOUNCING_LEVEL_DISABLED;
            add_settings_error('Fallback to', 'crowdsec_error', 'Fallback to: Incorrect Fallback selected.');
        }

        return $input;
    }, '<p>Which remediation to apply when CrowdSec advises unhandled remediation.</p>', $choice);

    function convertInlineIpRangesToComparableIpBounds(string $inlineIpRanges): array
    {
        $comparableIpBoundsList = [];
        $stringRangeArray = explode(',', $inlineIpRanges);
        foreach ($stringRangeArray as $stringRange) {
            $stringRange = trim($stringRange);
            if (false !== strpos($stringRange, '/')) {
                $range = Factory::rangeFromString($stringRange);
                if (null === $range) {
                    throw new WordpressCrowdSecBouncerException('Invalid IP List format.');
                }
                $bounds = [$range->getComparableStartString(), $range->getComparableEndString()];
                $comparableIpBoundsList = array_merge($comparableIpBoundsList, [$bounds]);
            } else {
                $address = Factory::addressFromString($stringRange);
                if (null === $address) {
                    throw new WordpressCrowdSecBouncerException('Invalid IP List format.');
                }
                $comparableString = $address->getComparableString();
                $comparableIpBoundsList = array_merge($comparableIpBoundsList, [[$comparableString, $comparableString]]);
            }
        }

        return $comparableIpBoundsList;
    }

    // Field "crowdsec_trust_ip_forward"
    addFieldString('crowdsec_trust_ip_forward_list', 'Trust these CDN IPs<br>(or Load Balancer, HTTP Proxy)', 'crowdsec_plugin_advanced_settings',
    'crowdsec_advanced_settings', 'crowdsec_admin_advanced_remediations', function ($input) {
        try {
            if ('' === $input) {
                update_option('crowdsec_trust_ip_forward_array', []);

                return $input;
            }
            $comparableIpBoundsList = convertInlineIpRangesToComparableIpBounds($input);
            update_option('crowdsec_trust_ip_forward_array', $comparableIpBoundsList);
            AdminNotice::displaySuccess('Ips with XFF to trust successfully saved.');
        } catch (WordpressCrowdSecBouncerException $e) {
            update_option('crowdsec_trust_ip_forward_array', []);
            add_settings_error('Trust these CDN IPs', 'crowdsec_error', 'Trust these CDN IPs: Invalid IP List format.');

            return '';
        }

        return $input;
    }, '<p>The <em><a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Forwarded-For" '.
    'target="_blank">X-forwarded-For</a></em> HTTP Header will be trust only when the client IP is in this list.'.
    '<br><strong>Comma (,)</strong> separated ips or ips ranges. Example: 1.2.3.4/24, 2.3.4.5, 3.4.5.6/27.<br><br>Some common CDN IP list: <a href="https://www.cloudflare.com/fr-fr/ips/" target="_blank">Cloudflare</a>, <a href="https://api.fastly.com/public-ip-list" target="_blank">Fastly</a>',
    'fill the IPs or IPs ranges here...', '');

    // Field "crowdsec_hide_mentions"
    addFieldCheckbox('crowdsec_hide_mentions', 'Hide CrowdSec mentions', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_remediations', function () {}, function () {}, '
    <p>Enable if you want to hide CrowdSec mentions on the Ban and Captcha pages</p>');

    /*******************************
     ** Section "Debug mode" **
     ******************************/

    add_settings_section('crowdsec_admin_advanced_debug', 'Debug mode', function () {
        echo 'Configure the debug mode.';
    }, 'crowdsec_advanced_settings');

    // Field "crowdsec_debug_mode"
    addFieldCheckbox('crowdsec_debug_mode', 'Enable debug mode', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_debug', function () {}, function () {}, '
    <p>Should not be used in production. When this mode is enabled, debug log will be written.</p>');

	/*******************************
	 ** Section "Display errors" **
	 ******************************/

	add_settings_section('crowdsec_admin_advanced_display_errors', 'Display errors', function () {
		echo 'Configure the errors display.';
	}, 'crowdsec_advanced_settings');

	// Field "crowdsec_display_errors"
	addFieldCheckbox('crowdsec_display_errors', 'Enable errors display', 'crowdsec_plugin_advanced_settings', 'crowdsec_advanced_settings', 'crowdsec_admin_advanced_display_errors', function () {}, function () {}, '
    <p>Do not use in production. When this mode is enabled, you will see every unexpected bouncing errors in the browser.</p>');
}
