<?php

require_once($CFG->libdir . '/externallib.php');

class local_getbbb_external extends external_api
{
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_data_parameters()
    {
        return new external_function_parameters(
            array(
                'course_id' => new external_value(PARAM_INT, 'ID of the course', VALUE_OPTIONAL, null),
                'start_date' => new external_value(PARAM_INT, 'Start date for filtering', VALUE_OPTIONAL, null),
                'end_date' => new external_value(PARAM_INT, 'End date for filtering', VALUE_OPTIONAL, null)
            )
        );
    }

    /**
     * The function itself
     * @param int $course_id
     * @param int $start_date
     * @param int $end_date
     * @return array
     */
    public static function get_data()
    {
        global $DB;

        $course_id = optional_param('course_id', null, PARAM_INT);
        $start_date = optional_param('start_date', null, PARAM_INT);
        $end_date = optional_param('end_date', null, PARAM_INT);

        $query = "SELECT 
            logs.id, logs.eventname, bbb.log AS bbb_log, logs.action, logs.crud,
            bbb.meetingid AS bbb_meetingid, 
            logs.userid, CONCAT(u.firstname, ' ', u.lastname) AS user_fullname,
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
            logs.component = 'mod_bigbluebuttonbn'
        AND 
            logs.target = 'meeting'";

        $conditions = [];
        $params = [];

        //CHECK IF course_id is provided
        if (!is_null($course_id) && $course_id > 0) {
            $conditions[] = 'logs.courseid = :course_id';
            $params['course_id'] = $course_id;
        }

        //CHECK IF start_date is provided
        if (!is_null($start_date) && $start_date > 0) {
            $conditions[] = 'logs.timecreated >= :start_date';
            $params['start_date'] = $start_date;
        }

        //CHECK IF end_date is provided
        if (!is_null($end_date) && $end_date > 0) {
            $conditions[] = 'logs.timecreated <= :end_date';
            $params['end_date'] = $end_date;
        }

        // Add conditions to the main query
        if (count($conditions) > 0) {
            $query .= ' AND ' . implode(' AND ', $conditions);
        }


        $data = $DB->get_records_sql($query, $params);

        return $data;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
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
                    'ip' => new external_value(PARAM_TEXT, 'ip')
                )
            )
        );
    }
}