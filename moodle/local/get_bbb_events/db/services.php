<?php

// In service.php
// The following line defines the external functions and services that are available from this plugin.

$functions = array(
    'local_get_bbb_events' => array(
        'classname' => 'local_getbbb_external',
        'methodname' => 'get_data',
        'classpath' => 'local/get_bbb_events/externallib.php',
        'description' => 'This is a DCI custom pluggin to retrieve BBB events from a course.',
        'type' => 'read',
    ),
);

$services = array(
    // This is the name of the web service function that the client will call.  It can also be used to search for available functions.
    'get bbb events' => array(
        'functions' => array('local_get_bbb_events'),
        'enabled' => 1,
    ),
);
