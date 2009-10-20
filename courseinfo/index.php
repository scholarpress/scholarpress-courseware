<?php
// Add management pages to the administration panel; sink function for 'admin_menu' hook

require_once('helpers.php');

function spcourseware_courseinfo_manage() {
    global $wpdb;
    	$data = false;

    	if ($_POST['save']) {

    	    $courseTimeStart = !empty($_REQUEST['course_timestart']) ? date('H:i:s', strtotime($_REQUEST['course_timestart'])) : '';
            $courseTimeEnd = !empty($_REQUEST['course_timeend']) ? date('H:i:s', strtotime($_REQUEST['course_timeend'])) : '';

    		spcourseware_courseinfo_set_fields($_REQUEST['course_title'], $_REQUEST['course_number'], $_REQUEST['course_section'], $courseTimeStart, $courseTimeEnd, $_REQUEST['course_location'], $_REQUEST['course_timedays'], $_REQUEST['course_description'], $_REQUEST['instructor_firstname'], $_REQUEST['instructor_lastname'], $_REQUEST['instructor_email'], $_REQUEST['instructor_telephone'], $_REQUEST['instructor_office'], $_REQUEST['instructor_hours']);

    		echo '<div class="updated"><p>Course information saved successfully.</p></div>';

    	}

    	$spcoursewareAdminOptions = spcourseware_courseinfo_get_fields();
        // print_r($spcoursewareAdminOptions); exit;
    
    ?>
    <div class="wrap">
    <h2>Course Information | ScholarPress Courseware</h2>
    <br class="clear" />
    <form method="post">
        		<input type="hidden" name="updateinfo" value="<?php echo $mode?>" />
        <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <label for="course_title">Course Title</label>
            </th>
            <td>
                <input name="course_title" type="text" id="course_title" value="<?php echo $spcoursewareAdminOptions['course_title']; ?>" class="regular-text" />
                <p class="description">The title of your course</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="course_number">Course Number</label>
            </th>
            <td>
                <input name="course_number" type="text" id="course_number" value="<?php echo $spcoursewareAdminOptions['course_number']; ?>" class="regular-text" />
                <p class="description">The registrar's number for your course (e.g. HIST 100)</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="course_section">Course Section</label>
            </th>
            <td>
                <input name="course_section" type="text" id="course_section" value="<?php echo $spcoursewareAdminOptions['course_section']; ?>" class="regular-text" />
                <p class="description">The registrar's section number for your course (e.g. 001)</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="course_days"><?php _e('Course Days'); ?></label></th>
            <td><input type="text" name="course_days" class="regular-text" value="<?php echo $spcoursewareAdminOptions['course_days']; ?>" />
                <p class="description">The days your course regularly meets</p></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="course_timestart"><?php _e('Course Time Start'); ?></label></th>
		    <td><input type="text" name="course_timestart" class="regular-text" value="<?php echo date('g:ia', strtotime($spcoursewareAdminOptions['course_time_start'])); ?>" />
		        <p class="description">The time your course regularly starts (e.g. 1:00PM)</p></td>
        </tr>
         <tr valign="top">  
		     <th scope="row"><label for="course_timeend"><?php _e('Course Time End'); ?></label></th>
		     <td><input type="text" name="course_timeend" class="regular-text" value="<?php echo date('g:ia', strtotime($spcoursewareAdminOptions['course_time_end'])); ?>" />
 		        <p class="description">The time your course regularly ends (e.g. 2:00PM)</p></td>
		</tr>
        <tr valign="top">  
		<th scope="row"><label for="course_location"><?php _e('Course Location'); ?></label></th>
		<td><input type="text" name="course_location" class="regular-text" value="<?php echo $spcoursewareAdminOptions['course_location']; ?>" />
	        <p class="description">The place your course regularly meets (e.g. Building and room number)</p></td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="course_description">Course Description</label>
            </th>
            <td>
                <textarea name="course_description"id="course_description" value="" rows="10" style="width:70%; min-width: 300px;"><?php echo $spcoursewareAdminOptions['course_description']; ?></textarea>
                <p class="description">The description for your course.</p>
            </td>
        </tr>
        
        </table>
        <p class="submit">
        <input type="submit" name="save" class="button-primary" value="Save Changes" />
        </p>
        
    </form>
    </div>
    <br class="clear" />
    <?php
}
?>