<?php

require_once($CFG->libdir . '/externallib.php');
// In externallib.php
class local_getbbb_external extends external_api
{
    public static function get_data_parameters()
    {
        return new external_function_parameters(
            array(
                'course_id' => new external_value(PARAM_INT, 'course_id'),
                'start_date' => new external_value(PARAM_INT, 'start_date', VALUE_OPTIONAL),
                'end_date' => new external_value(PARAM_INT, 'end_date', VALUE_OPTIONAL),
            )
        );
    }

    public static function get_data($course_id, $start_date = null, $end_date = null)
    {
        global $DB;

        // database query to retrieve data
        // $query = "SELECT
        //             bbl.*,
        //             u.idnumber as std_idnumber,
        //             CONCAT(u.firstname, ' ', lastname) AS std_fullname,
        //             crs.idnumber as crs_idnumber,
        //             crs.fullname as crs_fullname
        //           FROM
        //             mdl_bigbluebuttonbn_logs as bbl
        //             LEFT JOIN mdl_user AS u ON bbl.userid = u.id
        //             LEFT JOIN mdl_course AS crs ON bbl.courseid = crs.id
        //           WHERE bbl.courseid = :course_id
        //         ";
        $query = "SELECT 
                    logs.id, logs.eventname, bbb.log AS bbb_log, logs.action, logs.crud,
                    bbb.meetingid AS bbb_meetingid, 
                    logs.userid, CONCAT(u.firstname, ' ',u.lastname) AS user_fullname,
                    u.idnumber AS user_idnumber,
                    logs.courseid, crs.fullname AS crs_fullname,
                    crs.idnumber AS crs_idnumber,
                    logs.timecreated, logs.ip

                    FROM 
                    mdl_logstore_standard_log AS logs
                    LEFT JOIN mdl_user AS u ON logs.userid = u.id
                    LEFT JOIN mdl_course AS crs ON logs.courseid = crs.id
                    LEFT JOIN mdl_bigbluebuttonbn_logs AS bbb ON logs.courseid = bbb.courseid

                    WHERE
                    logs.courseid = :course_id ";

        $params = array(
            'course_id' => $course_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        );

        //CHECK IF start_date is provided
        if (!is_null($start_date)) {
            $query .= ' AND logs.timecreated >= :start_date';
            $params['start_date'] = $start_date;
        }
        //CHECK IF end_date is provided
        if (!is_null($end_date)) {
            $query .= ' AND logs.timecreated <= :end_date';
            $params['end_date'] = $end_date;
        }

        $data = $DB->get_records_sql($query, $params);

        // Process $data if needed
        // echo $params;
        return $data;
    }

    public static function get_data_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'id'),
                    'eventname' => new external_value(PARAM_TEXT, 'eventname'),
                    'bbb_meetingid' => new external_value(PARAM_TEXT, 'bbb_meetingid'),
                    'bbb_log' => new external_value(PARAM_TEXT, 'bbb_log'),
                    'action' => new external_value(PARAM_TEXT, 'action'),
                    'crud' => new external_value(PARAM_TEXT, 'crud'),
                    'userid' => new external_value(PARAM_INT, 'userid'),
                    'user_fullname' => new external_value(PARAM_TEXT, 'user_fullname'),
                    'user_idnumber' => new external_value(PARAM_TEXT, 'user_idnumber'),
                    'courseid' => new external_value(PARAM_INT, 'courseid'),
                    'crs_fullname' => new external_value(PARAM_TEXT, 'crs_fullname'),
                    'crs_idnumber' => new external_value(PARAM_TEXT, 'crs_idnumber'),
                    'timecreated' => new external_value(PARAM_INT, 'timecreated'),
                    'ip' => new external_value(PARAM_TEXT, 'ip'),
                    // 'meetingid' => new external_value(PARAM_TEXT, 'meetingid'),
                    // 'log' => new external_value(PARAM_TEXT, 'log'),

                    // Define other fields here
                )
            )
        );
    }
}
