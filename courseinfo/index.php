<?php
// Add management pages to the administration panel; sink function for 'admin_menu' hook

require_once('helpers.php');

function spcourseware_courseinfo_manage() {
    global $wpdb;
    	$data = false;

    	if ($_POST['save']) {

    	    $courseTimeStart = !empty($_REQUEST['course_timestart']) ? date('H:i:s', strtotime($_REQUEST['course_timestart'])) : '';
            $courseTimeEnd = !empty($_REQUEST['course_timeend']) ? date('H:i:s', strtotime($_REQUEST['course_timeend'])) : '';

    		spcourseware_courseinfo_set_fields($_REQUEST['course_title'], $_REQUEST['course_number'], $_REQUEST['course_section'], $courseTimeStart, $courseTimeEnd, $_REQUEST['course_location'], $_REQUEST['course_days'], $_REQUEST['course_description'], $_REQUEST['instructor_firstname'], $_REQUEST['instructor_lastname'], $_REQUEST['instructor_email'], $_REQUEST['instructor_telephone'], $_REQUEST['instructor_office'], $_REQUEST['instructor_hours']);

    		echo '<div class="updated"><p>'. __( 'Course information saved successfully.', SPCOURSEWARE_TD ) .'</p></div>';

    	}

    	$spcoursewareAdminOptions = spcourseware_courseinfo_get_fields();
        // print_r($spcoursewareAdminOptions); exit;
    
    ?>
    <div class="wrap">
    <h2><?php echo __( 'Course Information', SPCOURSEWARE_TD ); ?> | <?php echo __( 'ScholarPress Courseware', SPCOURSEWARE_TD ); ?></h2>
    <br class="clear" />
    <form method="post">
        		<input type="hidden" name="updateinfo" value="<?php echo $mode?>" />
        <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <label for="course_title"><?php echo __( 'Course Title', SPCOURSEWARE_TD ); ?></label>
            </th>
            <td>
                <input name="course_title" type="text" id="course_title" value="<?php echo $spcoursewareAdminOptions['course_title']; ?>" class="regular-text" />
                <p class="description"><?php echo __( 'The title of your course', SPCOURSEWARE_TD ); ?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="course_number"><?php echo __( 'Course Number', SPCOURSEWARE_TD ); ?></label>
            </th>
            <td>
                <input name="course_number" type="text" id="course_number" value="<?php echo $spcoursewareAdminOptions['course_number']; ?>" class="regular-text" />
                <p class="description"><?php echo __( "The registrar's number for your course (e.g. HIST 100)", SPCOURSEWARE_TD ); ?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="course_section"><?php echo __( 'Course Section', SPCOURSEWARE_TD ); ?></label>
            </th>
            <td>
                <input name="course_section" type="text" id="course_section" value="<?php echo $spcoursewareAdminOptions['course_section']; ?>" class="regular-text" />
                <p class="description"><?php echo __( "The registrar's section number for your course (e.g. 001)", SPCOURSEWARE_TD ); ?></p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="course_days"><?php echo __( 'Course Days', SPCOURSEWARE_TD ); ?></label></th>
            <td><input type="text" name="course_days" class="regular-text" value="<?php echo $spcoursewareAdminOptions['course_days']; ?>" />
                <p class="description"><?php echo __( 'The days your course regularly meets', SPCOURSEWARE_TD ); ?></p></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label for="course_timestart"><?php echo __( 'Course Time Start', SPCOURSEWARE_TD ); ?></label></th>
		    <td><input type="text" name="course_timestart" class="regular-text" value="<?php echo date('g:ia', strtotime($spcoursewareAdminOptions['course_time_start'])); ?>" />
		        <p class="description"><?php echo __('The time your course regularly starts (e.g. 1:00PM)', SPCOURSEWARE_TD ); ?></p></td>
        </tr>
         <tr valign="top">  
		     <th scope="row"><label for="course_timeend"><?php echo __( 'Course Time End', SPCOURSEWARE_TD ); ?></label></th>
		     <td><input type="text" name="course_timeend" class="regular-text" value="<?php echo date('g:ia', strtotime($spcoursewareAdminOptions['course_time_end'])); ?>" />
 		        <p class="description"><?php echo __( 'The time your course regularly ends (e.g. 2:00PM)', SPCOURSEWARE_TD ); ?></p></td>
		</tr>
        <tr valign="top">  
		<th scope="row"><label for="course_location"><?php echo __( 'Course Location', SPCOURSEWARE_TD ); ?></label></th>
		<td><input type="text" name="course_location" class="regular-text" value="<?php echo $spcoursewareAdminOptions['course_location']; ?>" />
	        <p class="description"><?php echo __( 'The place your course regularly meets (e.g. Building and room number)', SPCOURSEWARE_TD ); ?></p></td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label for="course_description"><?php echo __( 'Course Description', SPCOURSEWARE_TD ); ?></label>
            </th>
            <td>
                <textarea name="course_description"id="course_description" value="" rows="10" style="width:70%; min-width: 300px;"><?php echo $spcoursewareAdminOptions['course_description']; ?></textarea>
                <p class="description"><?php echo __( 'The description for your course.', SPCOURSEWARE_TD ); ?></p>
            </td>
        </tr>
        
        </table>
        <p class="submit">
        <input type="submit" name="save" class="button-primary" value="<?php echo __( 'Save Changes', SPCOURSEWARE_TD ); ?>" />
        </p>
        
    </form>
    </div>
    <br class="clear" />
    <?php
}
?>