<?php

require_once($CFG->libdir . '/externallib.php');
// In externallib.php
class local_mycustomws_external extends external_api {
    public static function get_data_parameters() {
        return new external_function_parameters(
            array(
                'course_id'=> new external_value(PARAM_INT, 'course_id'),
                'start_date'=>new external_value(PARAM_INT, 'start_date', VALUE_OPTIONAL),
                'end_date'=>new external_value(PARAM_INT, 'end_date', VALUE_OPTIONAL),
            )
        );
    }

    public static function get_data($course_id, $start_date=null, $end_date=null) {
        global $DB;
        
        // database query to retrieve data
        $query = "SELECT 
                    bbl.*, 
                    u.idnumber as std_idnumber,
                    CONCAT(u.firstname, ' ', lastname) AS std_fullname,
                    crs.idnumber as crs_idnumber, 
                    crs.fullname as crs_fullname 
                  FROM 
                    mdl_bigbluebuttonbn_logs as bbl 
                    LEFT JOIN mdl_user AS u ON bbl.userid = u.id 
                    LEFT JOIN mdl_course AS crs ON bbl.courseid = crs.id 
                  WHERE bbl.courseid = :course_id 
                ";

        $params = array(
            'course_id' => $course_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        );

        //CHECK IF start_date is provided
        if(!is_null($start_date)){
            $query .= ' AND bbl.timecreated >= :start_date';
            $params['start_date'] = $start_date;
        }
        //CHECK IF end_date is provided
        if(!is_null($end_date)){
            $query .= ' AND bbl.timecreated <= :end_date';
            $params['end_date'] = $end_date;
        }
        
        $data = $DB->get_records_sql($query, $params);
        
        // Process $data if needed 
        echo $params;
        return $data;
    }

    public static function get_data_returns() {
        return new external_multiple_structure(
          new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'id'),
                'courseid' => new external_value(PARAM_INT, 'courseid'),
                'bigbluebuttonbnid' => new external_value(PARAM_INT, 'bigbluebuttonbnid'),
                'userid' => new external_value(PARAM_INT, 'userid'),
                'std_idnumber' => new external_value(PARAM_TEXT, 'std_idnumber'),
                'std_fullname' => new external_value(PARAM_TEXT, 'std_fullname'),
                'crs_idnumber' => new external_value(PARAM_TEXT, 'crs_idnumber'),
                'crs_fullname' => new external_value(PARAM_TEXT, 'crs_fullname'),
                'timecreated' => new external_value(PARAM_INT, 'timecreated'),
                'meetingid' => new external_value(PARAM_TEXT, 'meetingid'),
                'log' => new external_value(PARAM_TEXT, 'log'),
                
                // Define other fields here
            )
        ));
    }
}


