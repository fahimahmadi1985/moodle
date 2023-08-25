<?php

// In service.php
$functions = array(
    'local_mycustomws_get_data' => array(
        'classname' => 'local_mycustomws_external',
        'methodname' => 'get_data',
        'classpath' => 'local/mycustomws_get_data/externallib.php',
        'description' => 'Retrieve data from your custom table',
        'type' => 'read',
    ),
);

$services = array(
    'customws get data' => array(
        'functions' => array('local_mycustomws_get_data'),
        'enabled'=>1,
    ),
);