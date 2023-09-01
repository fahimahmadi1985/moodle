<?php
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class update_course_content extends external_api {
    
    public static function update_data_parameters() {
        return new external_function_parameters(
            array('course_id' => new external_value(PARAM_INT, 'ID of the course', VALUE_REQUIRED, null))
        );
    }

    public static function update_data($course_id) {
    
        global $DB;
        global $CFG;
        
        /* ---------------------------------- start --------------------------------- */
        
        // Read the request data from the body
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        // $course_id = required_param('course_id', null, PARAM_INT);
        $sequenceList = [];
        
        //start transaction
        $transaction = $DB->start_delegated_transaction();
        
        try{
            $isCourseExist = $DB->record_exists('course', ['id'=> $course_id]);
            if($course_id === null || !$isCourseExist){
                throw new Exception('Invalid course_id.');
            }
            if($data === null || empty($data)){
                throw new Exception('Invalid JSON data: Missing Data in the request.');
            }

            /* ----------------------------- DELETE OLD DATA ---------------------------- */
            $DB->delete_records('assign', ["course" => $course_id]); //delete assignments
            $DB->delete_records('label', ["course" => $course_id]); //delete labels
            $DB->delete_records('course_modules', ["course" => $course_id]); //delete course_modules
            $DB->delete_records('course_format_options', ["courseid"=> $course_id]); //delete course_format_options
            $DB->delete_records_select('course_sections', 'course = ? AND section > 0', array($course_id)); //delete course_sections for given course except first row
        
            /* ------------------------------------ . ----------------------------------- */
            // Initialize course modules
            foreach ($data as $courseData) {
                if(!property_exists($courseData, 'parent_section')){
                    throw new Exception('Invalid JSON data: Missing required fields (Parent_section).');
                }

                if(!is_string($courseData->parent_section)){
                    throw new Exception('Invalid JSON data: Parent section should be string.');
                }

                // $parentSection is the main title of a course module
                $parentSection = $courseData->parent_section;
                $sectionNumber = $DB->count_records("course_sections");
                
                //1. Insert main title of course module as section in table course_sections
                $parentSectionId = $DB->insert_record("course_sections", [
                    "course"=> $course_id,
                    "section"=>$sectionNumber,
                    "name" => $parentSection,
                    "summary" => "",
                    "summaryformat" => 1,
                    "sequence" => "",
                    "visible" => 0,
                    "availability" => NULL,
                    "timemodified" => time()
                ]);

                
            
                // Define main title as top level section
                $ps_formatOptions = [
                        ["courseid"=>$course_id, "format"=>"flexsections", "sectionid" => $parentSectionId, "name" => "collapsed", "value" => 0],
                        ["courseid"=>$course_id, "format"=>"flexsections", "sectionid" => $parentSectionId, "name" => "parent", "value" => 0],
                        ["courseid"=>$course_id, "format"=>"flexsections", "sectionid" => $parentSectionId, "name" => "visibleold", "value" => 1],
                    ];

                foreach ($ps_formatOptions as $option) {
                    $DB->insert_record("course_format_options", $option);
                }
                /* ------------------------------------ . ----------------------------------- */

                // Loop through subSections (Days) of main-title
                foreach ($courseData->sub_sections as $subSectionData) {
                    // Validate Sub Section
                    if(!property_exists($subSectionData, 'title')){
                    throw new Exception('Invalid JSON data: Missing required fields (Sub_section).');
                    }
                    if(!is_string($subSectionData->title)){
                        throw new Exception('Invalid JSON data: Subsections title should be string.');
                    }
                    
                    $subSectionTitle = $subSectionData->title;
                    $subSectionNumber = $DB->count_records("course_sections");
                    
                    //Insert subSection in table course_sections
                    $subSectionId = $DB->insert_record("course_sections", [
                        "course"=> $course_id,
                        "section"=>$subSectionNumber,
                        "name" => $subSectionTitle,
                        "summary" => "",
                        "summaryformat" => 1,
                        "sequence" => "",
                        "visible" => 0,
                        "availability" => NULL,
                        "timemodified" => time()
                    ]);

                    // Define subSection as second-level title
                    $formatOptions = [
                        ["courseid"=>$course_id, "format"=>"flexsections", "sectionid" => $subSectionId, "name" => "collapsed", "value" => 0],
                        ["courseid"=>$course_id, "format"=>"flexsections", "sectionid" => $subSectionId, "name" => "parent", "value" => $sectionNumber],
                        ["courseid"=>$course_id, "format"=>"flexsections", "sectionid" => $subSectionId, "name" => "visibleold", "value" => 1],
                    ];
                    foreach ($formatOptions as $option) {
                        $DB->insert_record("course_format_options", $option);
                    }
                    /* ------------------------------------ . ----------------------------------- */
            
                    // Extract module description as Label and insert it to table Label
                    foreach ($subSectionData->labels as $label) {
                        // Use first line of label as Name for it
                        $lable_lines = explode("\n", $label);
                        $first_line = $lable_lines[0];

                        $labelId = $DB->insert_record("label", [
                            "course" => $course_id,
                            "name" => $first_line,
                            "intro" => $label,
                            "introformat" => 4,
                            "timemodified" => time()
                            ]);
                        
                        // Populate an array of inserted Label_ids
                        array_push($sequenceList,
                            //Insert Label
                            $DB->insert_record("course_modules", [
                            "course" => $course_id,
                            "module" => 13,
                            "instance" => $labelId,
                            "section" => $subSectionId,
                            "idnumber" => "", 
                            "added" => time(),
                            "score" => 0,
                            "indent" => 0,
                            "visible" => 0,
                            "visibleoncoursepage" => 1,
                            "visibleold" => 1,
                            "groupmode" => 0,
                            "groupingid" => 0,
                            "completion" => 1,
                            "completiongradeitemnumber" => NULL,
                            "completionview" => 0,
                            "completionexpected" => 0,
                            "completionpassgrade" => 0,
                            "showdescription" => 1,
                            "availability" => NULL,
                            "deletioninprogress" => 0,
                            "downloadcontent" => 1,
                            "lang"=>""

                            ]));
                    }
                    /* ------------------------------------ . ----------------------------------- */
                    
                    // Extract Assignments and insert it to table Assign
                    foreach ($subSectionData->assigns as $assign) {
                        // Extract Assignment's name out of URL
                        $assignArray = explode('/', $assign);
                        $assignName = end($assignArray);
                        
                        $assignId = $DB->insert_record("assign", [
                            "course" => $course_id,
                            "name" => $assignName, 
                            "intro" => $assign,
                            "introformat" => 4,
                            "alwaysshowdescription" => 0,
                            "nosubmissions" => 0,
                            "submissiondrafts" => 0,
                            "sendnotifications" => 0,
                            "sendlatenotifications" => 0,
                            "duedate" => 0,
                            "allowsubmissionsfromdate" => 0,
                            "grade" => 100,
                            "timemodified" => time(),
                            "requiresubmissionstatement" => 0,
                            "completionsubmit" => 1,
                            "cutoffdate" => 0,
                            "gradingduedate" => 0,
                            "teamsubmission" => 0,
                            "requireallteammemberssubmit" => 0,
                            "teamsubmissiongroupingid" => 0,
                            "blindmarking" => 0,
                            "hidegrader" => 0,
                            "revealidentities" => 0,
                            "attemptreopenmethod" => 'none',
                            "maxattempts" => -1,
                            "markingworkflow" => 0,
                            "markingallocation" => 0,
                            "sendstudentnotifications" => 1,
                            "preventsubmissionnotingroup" => 0,
                            "activityformat" => 1,
                            "timelimit" => 0,
                            "submissionattachments" => 0
                        ]);

                        // Add assignments IDs to the current list of labels
                        // Because Labels and Assigns are called Instance
                        array_push($sequenceList,
                            //Insert Assignment into table assign 
                            $DB->insert_record("course_modules", [
                            "course" => $course_id,
                            "module" => 1,
                            "instance" => $assignId,
                            "section" => $subSectionId,
                            "idnumber" => "", 
                            "added" => time(),
                            "score" => 0,
                            "indent" => 0,
                            "visible" => 0,
                            "visibleoncoursepage" => 1,
                            "visibleold" => 1,
                            "groupmode" => 0,
                            "groupingid" => 0,
                            "completion" => 1,
                            "completiongradeitemnumber" => NULL,
                            "completionview" => 0,
                            "completionexpected" => 0,
                            "completionpassgrade" => 0,
                            "showdescription" => 0,
                            "availability" => NULL,
                            "deletioninprogress" => 0,
                            "downloadcontent" => 1,
                            "lang" => ""
                            ]));
                    }
                    /* ------------------------------------ . ----------------------------------- */

                    //To make link between Labels/Assigns and Subsections
                    // should populate a sequence list of labels and assigns IDs
                    $dataToUpdate = new stdClass();
                    $dataToUpdate->id = $subSectionId;
                    $dataToUpdate->sequence = implode(",", $sequenceList);

                    // Update the column sequence in table course_sections
                    $DB->update_record("course_sections", $dataToUpdate);
                    $sequenceList = [];
                }

            }
            // Commit the transaction if everything is successful
            $transaction->allow_commit();

        }catch(Exception $e){
            // Rollback the transaction on error
            $transaction->rollback($e);

            // Handle the exception
            return array(
                'message'=>$e->getMessage(),
                'status' => 400
            );
        }

    /* ----------------------------------- end ---------------------------------- */

            // If the code execution reaches here, the transaction was committed successfully
            $result = array(
                'message' => 'Data inserted successfully',
                'status' => 201,
            );
            return $result;

    }

    public static function update_data_returns() {
        return new external_single_structure(
            array(
                'message' => new external_value(PARAM_TEXT, 'A success message'),
                'status' => new external_value(PARAM_INT, 'Status code of the response'),
            )
        );
    }
}

?>