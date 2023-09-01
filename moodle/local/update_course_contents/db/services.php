<?php

// In service.php
// The following line defines the external functions and services that are available from this plugin.

$functions = array(
    'update_course_content_update_data' => array(
        'classname' => 'update_course_content',
        'methodname' => 'update_data',
        'classpath' => 'local/update_course_contents/externallib.php',
        'description' => 'This is a DCI custom pluggin to update course curiculum and content',
        'type' => 'write',
    ),
);

$services = array(
    // This is the name of the web service function that the client will call.  It can also be used to search for available functions.
    'update_course_content' => array(
        'functions' => array('update_course_content_update_data'),
        'enabled' => 1,
    ),
);

?>