<?php

define('CROWDSEC_REFRESH_BLOCKLIST_CRON_HOOK', 'crowdsec_refresh_blocklist_cron_hook');
define('CROWDSEC_REFRESH_BLOCKLIST_CRON_INTERVAL', 'crowdsec_refresh_blocklist_cron_interval');

// Create a WP custom cron interval (ovewrite previous if any).
add_filter('cron_schedules', function ($schedules) {
    $refreshFrequency = (int) get_option('crowdsec_stream_mode_refresh_frequency');
    if ($refreshFrequency > 0) {
        $schedules[CROWDSEC_REFRESH_BLOCKLIST_CRON_INTERVAL] = [
            'interval' => $refreshFrequency,
            'display' => esc_html__('Every '.$refreshFrequency.' second(s)'),
        ];
    }

    return $schedules;
});

function crowdSecRefreshBlocklist()
{
    try {
        $bouncer = getBouncerInstance();
        $bouncer->refreshBlocklistCache();
    } catch (WordpressCrowdSecBouncerException $e) {
        getCrowdSecLoggerInstance()->error('', [
            'type' => 'WP_EXCEPTION_WHILE_REFRESHING_CACHE',
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
}

// Create the hook that the schedule will call
add_action(CROWDSEC_REFRESH_BLOCKLIST_CRON_HOOK, 'crowdSecRefreshBlocklist');

function unscheduleBlocklistRefresh()
{
    $timestamp = wp_next_scheduled(CROWDSEC_REFRESH_BLOCKLIST_CRON_HOOK);
    wp_unschedule_event($timestamp, CROWDSEC_REFRESH_BLOCKLIST_CRON_HOOK);
}

function scheduleBlocklistRefresh()
{
    // Remove existing schedule if any.
    unscheduleBlocklistRefresh();

    // Schedule "blocklist cache refresh" each <refresh interval>, the first execution starting now.
    wp_schedule_event(time(), CROWDSEC_REFRESH_BLOCKLIST_CRON_INTERVAL, CROWDSEC_REFRESH_BLOCKLIST_CRON_HOOK);
}
