<?php

/**
 * Returns all the fields for course information as an array.
 * 
 * @since 1.2
 * @return array
 **/
function spcourseware_courseinfo_get_fields()
{
    $spcoursewareOptions = get_option('SpCoursewareCourseInfo');
	if (!empty($spcoursewareOptions)) {
		foreach ($spcoursewareOptions as $key => $value)
			$spcoursewareAdminOptions[$key] = $value;
		}
	return $spcoursewareAdminOptions;
}

/**
 * Returns the value of a specific field in the courseinfo array.
 *
 * @since 1.0
 * @uses spcourseware_courseinfo_get_fields()
 * @return string|null
 **/
function spcourseware_courseinfo_get_field($fieldName)
{
    $fieldName = str_replace(' ','_',strtolower($fieldName));
    $fields = spcourseware_courseinfo_get_fields();
    return $fields[$fieldName];
}

/**
 * Returns an HTML-formatted string for course information
 *
 * @since 1.0
 * @uses spcourseware_courseinfo_get_fields()
 * @return string
 **/
function spcourseware_courseinfo_printfull()
{
    $courseinfo = spcourseware_courseinfo_get_fields();
    $course_title = $courseinfo['course_title'];
    $course_description = $courseinfo['course_description'];
    $course_location = $courseinfo['course_location'];
    $course_number = $courseinfo['course_number'];
    $course_section = $courseinfo['course_section'];
    $course_time_start = $courseinfo['course_time_start'];
    $course_time_end = $courseinfo['course_time_end'];
    $course_days = $courseinfo['course_days'];
    $instructor_first_name = $courseinfo['instructor_first_name'];
    $instructor_last_name = $courseinfo['instructor_last_name'];
    $instructor_telephone = $courseinfo['instructor_telephone'];
    $instructor_office = $courseinfo['instructor_office'];
    $instructor_hours = $courseinfo['instructor_hours'];
    $instructor_email = $courseinfo['instructor_email'];
    
    // String to time that junk.
    $starttime = strtotime($course_time_start);
    $endtime = strtotime($course_time_end);

?>
<div class="courseinfo">
    <p>
    <?php if(!empty($course_number)): ?>
        <span class="course-number"><?php echo $course_number; ?></span>
    <?php endif; ?>
    <?php if(!empty($course_title)): ?>
        : <span class="course-title"><?php echo $course_title; ?></span>
    <?php endif; ?>
    </p>
    <?php if(!empty($course_location)): ?>
        <p class="location"><?php echo $course_location; ?></p>
    <?php endif; ?>

    <p class="timedays">
        <?php if(!empty($course_days)): ?>
            <span class="days"><?php echo $course_days; ?></span>
        <?php endif; ?>
        <?php if(!empty($starttime)): ?>
            <?php echo date('g:i',$starttime); ?><?php if(!empty($endtime)): ?>&ndash;<?php echo date('g:i',$endtime); ?>
        <?php endif; ?>
        <?php endif; ?>
    </p>
    <ul class="vcard instructor">
        <li><span class="fn n"><span class="given-name"><?php echo $instructor_first_name; ?></span> <span class="family-name"><?php echo $instructor_last_name; ?></span></span></li>
        <li><span class="office"><?php echo $instructor_office; ?></span></li>
        <li><a href="mailto:<?php echo $instructor_email; ?>" class="email"><?php echo $instructor_email; ?></a></li>
        <li><span class="tel"><?php echo $instructor_telephone; ?></span></li>
    </ul>
</div>

<?php
}

function spcourseware_get_assignment_entries($scheduleID=false, $type=false, $order='assignmentID')
{
    global $wpdb;
	$assignment_table_name = $wpdb->prefix . "assignments";
	
	$sql = "SELECT * FROM " . $assignment_table_name;
	
	if($scheduleID) {
	    $sql .= " WHERE assignments_scheduleID=".$scheduleID;
	    if($type) {
	        $sql .= " AND assignments_type = CONVERT( _utf8 '$type' USING latin1 )";
	    }
	} elseif ($type) {
        $sql .= " WHERE assignments_type = CONVERT( _utf8 '$type' USING latin1 )";
    }
	
	$sql .= " ORDER BY ".$order;
	
    // echo $sql; exit;
	$results = $wpdb->get_results($sql, OBJECT);
	return $results;
}

function spcourseware_get_assignment_types()
{
     array('reading','writing','presentation','groupwork','research','discussion', 'creative');
}

function spcourseware_get_assignment_entry_by_id($id) 
{
    global $wpdb;
	$assignments_table_name = $wpdb->prefix . "assignments";
	$sql = "SELECT * from " . $assignments_table_name . " where assignmentID=".$id;
	return $wpdb->get_row($sql, OBJECT);
}

function spcourseware_delete_assignment_entry($id)
{
    global $wpdb;
    if($id) {
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "assignments WHERE assignmentID = '" . $id . "'");
    }
}

function spcourseware_add_assignment_entry()
{
    global $wpdb;
    
    // print_r($_REQUEST); exit;
    $title = !empty($_REQUEST['assignment_title']) ? $_REQUEST['assignment_title'] : '';
    $scheduleID = !empty($_REQUEST['assignment_scheduleID']) ? $_REQUEST['assignment_scheduleID'] : '';
    $bibliographyID = !empty($_REQUEST['assignment_bibliographyID']) ? $_REQUEST['assignment_bibliographyID'] : '';
    $type = !empty($_REQUEST['assignment_type']) ? $_REQUEST['assignment_type'] : '';
    $pages = !empty($_REQUEST['assignment_pages']) ? $_REQUEST['assignment_pages'] : '';
    $description = !empty($_REQUEST['assignment_description']) ? $_REQUEST['assignment_description'] : '';

    $sql = "INSERT INTO " . $wpdb->prefix . "assignments SET assignments_title = '" . $title . "', assignments_scheduleID = '" . $scheduleID . "', assignments_bibliographyID = '" . $bibliographyID . "',  assignments_type = '" . $type . "', assignments_pages = '" . $pages . "', assignments_description = '" . $description . "'";
    $wpdb->query($sql);
    
    $sqlCheck = "SELECT assignmentID FROM " . $wpdb->prefix . "assignments WHERE assignments_title = '" . $title . "' and assignments_scheduleID = '" . $scheduleID . "' and assignments_type = '" . $type . "' and assignments_pages = '" . $pages . "' and assignments_description = '" . $description . "'";
    $check = $wpdb->get_results($sqlCheck);
    if ( !empty($check) || !empty($check[0]->assignmentID) ) {
        echo '<div class="updated"><p>'.sprintf( __( 'Assignment #%1$d updated successfully.', SPCOURSEWARE_TD), $check[0]->assignmentID ).'</p></div>';
    }
    
}

function spcourseware_update_assignment_entry($id)
{
    global $wpdb;
    
    $title = !empty($_REQUEST['assignment_title']) ? $_REQUEST['assignment_title'] : '';
    $scheduleID = !empty($_REQUEST['assignment_scheduleID']) ? $_REQUEST['assignment_scheduleID'] : '';
    $bibliographyID = !empty($_REQUEST['assignment_bibliographyID']) ? $_REQUEST['assignment_bibliographyID'] : '';
    $type = !empty($_REQUEST['assignment_type']) ? $_REQUEST['assignment_type'] : '';
    $pages = !empty($_REQUEST['assignment_pages']) ? $_REQUEST['assignment_pages'] : '';
    $description = !empty($_REQUEST['assignment_description']) ? $_REQUEST['assignment_description'] : '';

    if ( empty($id) ) {
        echo '<div class="error"><p>'. __( '<strong>Failure:</strong> No Assignment given.', SPCOURSEWARE_TD ) .'</p></div>';
    } else {
        $sql = "UPDATE " . $wpdb->prefix . "assignments SET assignments_title = '" . $title . "', assignments_scheduleID = '" . $scheduleID . "',  assignments_bibliographyID = '" . $bibliographyID . "', assignments_type = '" . $type . "', assignments_pages = '" . $pages . "', assignments_description = '" . $description . "' WHERE assignmentID = '" . $id . "'";
        
        $wpdb->query($sql);
        
        $sqlCheck = "SELECT assignmentID FROM " . $wpdb->prefix . "assignments WHERE assignments_title = '" . $title . "' and assignments_scheduleID = '" . $scheduleID . "' and assignments_type = '" . $type . "' and assignments_pages = '" . $pages . "' and assignments_description = '" . $description . "' LIMIT 1";
        
        $check = $wpdb->get_results($sqlCheck);
        if ( !empty($check) || !empty($check[0]->assignmentID) ) {
            echo '<div class="updated"><p>'. sprintf( __('Assignment #%1$d updated successfully.', SPCOURSEWARE_TD ), $id ) .'</p></div>';
        }
    }
}

function spcourseware_assignments_navigation()
{
    ?>
        <p><a href="admin.php?page=<?php echo $_GET['page']; ?>"><?php echo __( 'View Assignments', SPCOURSEWARE_TD ) ; ?></a> | <a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=add_assignment"><?php echo __( 'Add an Assignment', SPCOURSEWARE_TD ); ?></a></p>
<?php
}

function spcourseware_assignment_edit_form($mode='add_assignment', $id=false)
{
    $data = false;
	
	if($mode == 'add_assignment') {
	    echo '<h3>'. __( 'Add an Assignment Entry', SPCOURSEWARE_TD ) .'</h3>';
	}
	
	if ( $id !== false ) {
		if ( intval($id) != $id ){
			echo '<div class="error"><p>'. sprintf( __( 'Assignment entry #%1$d is not a valid integer.', SPCOURSEWARE_TD ), $id ) .'</p></div>';
			return;
		} else {
			$data = spcourseware_get_assignment_entry_by_id($id);
			echo '<h3>'. __( 'Update Assignment Entry #', SPCOURSEWARE_TD ) .$id. '</h3>';
			if ( empty($data) ) {
				echo '<div class="error"><p>'. __( 'I couldn\'t find Assignment #'. SPCOURSEWARE_TD ) .$id. '</p></div>';
				return;
			}
		}	
	}
    ?>
	<form name="readingform" id="readingform" class="wrap" method="post" action="">
		<input type="hidden" name="update_action" value="<?php echo $mode; ?>">
		<input type="hidden" name="assignment_id" value="<?php echo $id; ?>">
        <script type="text/javascript" charset="utf-8">
    	jQuery(function($){
            $('#assignment_title').hide();
            $('#assignment_bibliography_entry').show();
            
            $("#assignment_type option:selected").each(function(){
                if(this.value == 'reading') {
                    $('#assignment_title').hide();
                    $('#assignment_bibliography_entry').show();
                } else {
                    $('#assignment_title').show();
                    $('#assignment_bibliography_entry').hide();
                }
            });
            
            $("select#assignment_type").change(function () {
                if(this.value == 'reading') {
                    $('#assignment_title').hide();
                    $('#assignment_bibliography_entry').show();
                } else {
                    $('#assignment_title').show();
                    $('#assignment_bibliography_entry').hide();
                }
            });
            

    	});
    	</script>
        <table class="form-table">
			<tr valign="top">
			    <th scope="row"><label for="assignment_type"><?php echo __('Type of Assignment', SPCOURSEWARE_TD ); ?></label></th>

                <td>
                    <select name="assignment_type" id="assignment_type">
                    <option value="reading"<?php if ( empty($data) || $data->assignments_type=='reading' ) echo 'selected="selected"'; ?>><?php echo __( 'Reading', SPCOURSEWARE_TD ); ?></option>
                    <option value="writing"<?php if ( empty($data) || $data->assignments_type=='writing' ) echo ' selected="selected"'; ?>><?php echo __( 'Writing', SPCOURSEWARE_TD ); ?></option>
                    <option value="presentation" <?php if ( empty($data) || $data->assignments_type=='presentation' ) echo ' selected="selected"'; ?>><?php echo __( 'Presentation', SPCOURSEWARE_TD ); ?></option>
                    <option value="groupwork"<?php if ( empty($data) || $data->assignments_type=='groupwork' ) echo ' selected="selected"'; ?>><?php echo __( 'Group Work', SPCOURSEWARE_TD ); ?></option>
                    <option value="research"<?php if ( empty($data) || $data->assignments_type=='research' ) echo ' selected="selected"'; ?>><?php echo __( 'Research', SPCOURSEWARE_TD ); ?></option>
                    <option value="discussion"<?php if ( empty($data) || $data->assignments_type=='discussion' ) echo ' selected="selected"'; ?>><?php echo __( 'Discussion', SPCOURSEWARE_TD ); ?></option>	
                    <option value="creative"<?php if ( empty($data) || $data->assignments_type=='creative' ) echo ' selected="selected"'; ?>><?php echo __( 'Creative', SPCOURSEWARE_TD ); ?></option>		
                    </select>
                </td>
            </tr>
            
            <tr valign="top" id="assignment_title">
			    <th scope="row"><label for="assignment_title"><?php _e('Title'); ?></label></th>
				

				<td><input type="text" id="title" name="assignment_title" class="input" size="45" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_title); ?>" /></td>
			</tr>
            <tr valign="top" id="assignment_bibliography_entry">
				<th scope="row"><label for="assignment_bibliographyID"><?php echo __( 'Choose from your Bibliograhy', SPCOURSEWARE_TD ); ?></label></th>
				<td>
                <select name="assignment_bibliographyID">
						<option value=""><?php echo __( 'Select an entry', SPCOURSEWARE_TD ); ?></option>

						<?php 
						$bibs = spcourseware_get_bibliography_entries();
						if($bibs):
						foreach ($bibs as $bib): ?>
						<option value="<?php echo $bib->entryID; ?>"<?php if ($bib->entryID==$data->assignments_bibliographyID) echo " selected"; ?>><?php echo $bib->author_last; ?>: <?php echo $bib->title; ?></option>
						<?php endforeach; else: ?>
						    <option value=""><?php echo __( 'No Bibliography Entries!', SPCOURSEWARE_TD ); ?></option>
						    <?php endif;?>
				</select>
				<label for="assignment_pages"><?php echo __( 'Pages:', SPCOURSEWARE_TD ); ?></label>
				<input type="text" name="assignment_pages" size="10" class="input" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_pages); ?>" />	
				</td>
			</tr>
			<tr valign="top">
					<th><label for="assignment_scheduleID"><?php echo __( 'Date Due', SPCOURSEWARE_TD ); ?></label></th>
					<td>
					<select name="assignment_scheduleID" id="assignment_scheduleID">
						<option value=""></option>
						<?php 
                            $dates = spcourseware_get_schedule_entries();
                            if($dates):
                            foreach($dates as $date):
						?>
						<option value="<?php echo $date->scheduleID; ?>"<?php if ($date->scheduleID==$data->assignments_scheduleID) echo " selected"; ?>><?php echo date('F d, Y', strtotime($date->schedule_date)); ?>: <?php echo $date->schedule_title; ?></option>
						    <?php endforeach; ?>
						    <?php endif; ?>
					</select>
					</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="assignment_description"><?php echo __( 'Description', SPCOURSEWARE_TD ); ?></label></th>
						<td><textarea name="assignment_description" class="mceEditor input" rows="10" cols="60"><?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_description); ?></textarea>
				        </td>
				    </tr>
                </table>
		<p class="submit">
			<input type="submit" name="save" class="button-primary" value="<?php echo __( 'Save Assignment &raquo;', SPCOURSEWARE_TD ); ?>" />
		</p>
	</form>

<?php
}

function spcourseware_get_bibliography_entries($type=null, $limit=null)
{
    global $wpdb;
	$bibliography_table_name = $wpdb->prefix . "bibliography";
	
	$sql = "SELECT * FROM " . $bibliography_table_name;
	
	if($type) {
	    $sql .= " WHERE type=".$type;
	}
	
	$sql .= " ORDER BY author_last, title ASC";
	
	if($limit != null) {
	    $sql .= " LIMIT ". $limit;
	}
	
	$results = $wpdb->get_results($sql, OBJECT);
	return $results;
}

function spcourseware_get_bibliography_entry_by_id($id) 
{
    global $wpdb;
	$bibliography_table_name = $wpdb->prefix . "bibliography";
	$sql = "SELECT * from " . $bibliography_table_name . " WHERE entryID=".$id;
	return $wpdb->get_row($sql, OBJECT);
}

function spcourseware_delete_bibliography_entry($id)
{
    global $wpdb;
    if($id) {
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "bibliography WHERE entryID = '" . $id . "'");
    }
}

function spcourseware_add_bibliography_entry()
{
    global $wpdb;
    
    // print_r($_REQUEST); exit;
    
    $author_last = !empty($_REQUEST['biblio_author_last']) ? $_REQUEST['biblio_author_last'] : '';
    $author_first = !empty($_REQUEST['biblio_author_first']) ? $_REQUEST['biblio_author_first'] : '';
    $author_two_last = !empty($_REQUEST['biblio_author_two_last']) ? $_REQUEST['biblio_author_two_last'] : '';
    $author_two_first = !empty($_REQUEST['biblio_author_two_first']) ? $_REQUEST['biblio_author_two_first'] : '';
    $title = !empty($_REQUEST['biblio_title']) ? $_REQUEST['biblio_title'] : '';
    $short_title = !empty($_REQUEST['biblio_short_title']) ? $_REQUEST['biblio_short_title'] : '';
    $journal = !empty($_REQUEST['biblio_journal']) ? $_REQUEST['biblio_journal'] : '';
    $volume_title = !empty($_REQUEST['biblio_volume_title']) ? $_REQUEST['biblio_volume_title'] : '';
    $volume_editors = !empty($_REQUEST['biblio_volume_editors']) ? $_REQUEST['biblio_volume_editors'] : '';
    $website_title = !empty($_REQUEST['biblio_website_title']) ? $_REQUEST['biblio_website_title'] : '';
    $pub_location = !empty($_REQUEST['biblio_pub_location']) ? $_REQUEST['biblio_pub_location'] : '';
    $publisher = !empty($_REQUEST['biblio_publisher']) ? $_REQUEST['biblio_publisher'] : '';
    $date = !empty($_REQUEST['biblio_date']) ? $_REQUEST['biblio_date'] : '';
    $dateaccessed = !empty($_REQUEST['biblio_dateaccessed']) ? $_REQUEST['biblio_dateaccessed'] : '';
    $url = !empty($_REQUEST['biblio_url']) ? $_REQUEST['biblio_url'] : '';
    $volume = !empty($_REQUEST['biblio_volume']) ? $_REQUEST['biblio_volume'] : '';
    $issue = !empty($_REQUEST['biblio_issue']) ? $_REQUEST['biblio_issue'] : '';
    $pages = !empty($_REQUEST['biblio_pages']) ? $_REQUEST['biblio_pages'] : '';
    $description = !empty($_REQUEST['biblio_description']) ? $_REQUEST['biblio_description'] : '';
    $type = !empty($_REQUEST['biblio_type']) ? $_REQUEST['biblio_type'] : '';
    
    // echo $author_last; exit;
    
	$sql = "INSERT INTO " . $wpdb->prefix . "bibliography SET author_last = '" . $author_last . "', author_first = '" . $author_first . "', author_two_last = '" . $author_two_last . "', author_two_first = '" . $author_two_first . "', title = '" . $title . "', short_title = '" . $short_title . "', journal = '" . $journal . "', volume_title = '" . $volume_title . "', volume_editors = '" . $volume_editors . "', website_title = '" . $website_title . "', pub_location = '" . $pub_location . "', publisher = '" . $publisher . "', date = '" . $date . "',  dateaccessed = '" . $dateaccessed . "',url = '" . $url . "', volume = '" . $volume ."', issue = '" . $issue . "', pages = '" . $pages . "', description = '" . $description . "', type = '" . $type . "'";
    
    $wpdb->query($sql);
    
    $sqlCheck = "SELECT entryID FROM " . $wpdb->prefix . "bibliography WHERE author_last = '" . $author_last . "' and author_first = '" . $author_first . "' and author_two_last = '" . $author_two_last . "' and author_two_first = '" . $author_two_first . "' and title = '" . $title . "' and short_title = '" . $short_title . "' and journal = '" . $journal . "' and volume_title = '" . $volume_title . "' and volume_editors = '" . $volume_editors . "' and website_title = '" . $website_title . "' and pub_location = '" . $pub_location . "' and publisher = '" . $publisher . "' and date = '" . $date . "' and dateaccessed = '" . $dateaccessed . "' and url = '" . $url . "' and volume = '" . $volume ."' and issue = '" . $issue . "' and pages = '" . $pages . "' and description = '" . $description . "' and type = '" . $type . "' LIMIT 1";
    
    $check = $wpdb->get_results($sqlCheck);
    if ( !empty($check) || !empty($check[0]->entryID) ) { 
        echo '<div class="updated"><p>'. sprintf( __( 'Bibliography %1$d updated successfully.', SPCOURSEWARE_TD ), $entryID ).'</p></div>';
    }
    
}

function spcourseware_update_bibliography_entry($id)
{
    global $wpdb;
        
    // print_r($_REQUEST); exit;
    $author_last = !empty($_REQUEST['biblio_author_last']) ? $_REQUEST['biblio_author_last'] : '';
    $author_first = !empty($_REQUEST['biblio_author_first']) ? $_REQUEST['biblio_author_first'] : '';
    $author_two_last = !empty($_REQUEST['biblio_author_two_last']) ? $_REQUEST['biblio_author_two_last'] : '';
    $author_two_first = !empty($_REQUEST['biblio_author_two_first']) ? $_REQUEST['biblio_author_two_first'] : '';
    $title = !empty($_REQUEST['biblio_title']) ? $_REQUEST['biblio_title'] : '';
    $short_title = !empty($_REQUEST['biblio_short_title']) ? $_REQUEST['biblio_short_title'] : '';
    $journal = !empty($_REQUEST['biblio_journal']) ? $_REQUEST['biblio_journal'] : '';
    $volume_title = !empty($_REQUEST['biblio_volume_title']) ? $_REQUEST['biblio_volume_title'] : '';
    $volume_editors = !empty($_REQUEST['biblio_volume_editors']) ? $_REQUEST['biblio_volume_editors'] : '';
    $website_title = !empty($_REQUEST['biblio_website_title']) ? $_REQUEST['biblio_website_title'] : '';
    $pub_location = !empty($_REQUEST['biblio_pub_location']) ? $_REQUEST['biblio_pub_location'] : '';
    $publisher = !empty($_REQUEST['biblio_publisher']) ? $_REQUEST['biblio_publisher'] : '';
    $date = !empty($_REQUEST['biblio_date']) ? $_REQUEST['biblio_date'] : '';
    $dateaccessed = !empty($_REQUEST['biblio_dateaccessed']) ? $_REQUEST['biblio_dateaccessed'] : '';
    $url = !empty($_REQUEST['biblio_url']) ? $_REQUEST['biblio_url'] : '';
    $volume = !empty($_REQUEST['biblio_volume']) ? $_REQUEST['biblio_volume'] : '';
    $issue = !empty($_REQUEST['biblio_issue']) ? $_REQUEST['biblio_issue'] : '';
    $pages = !empty($_REQUEST['biblio_pages']) ? $_REQUEST['biblio_pages'] : '';
    $description = !empty($_REQUEST['biblio_description']) ? $_REQUEST['biblio_description'] : '';
    $type = !empty($_REQUEST['biblio_type']) ? $_REQUEST['biblio_type'] : '';

    if ( empty($id) ) {
    	echo '<div class="error"><p>'. __( '<strong>Failure:</strong> No bibliography ID given.', SPCOURSEWARE_TD ).'</p></div>';
    } else {
        $sql = "UPDATE " . $wpdb->prefix . "bibliography SET author_last = '" . $author_last . "', author_first = '" . $author_first . "', author_two_last = '" . $author_two_last . "', author_two_first = '" . $author_two_first . "', title = '" . $title . "', short_title = '" . $short_title . "', journal = '" . $journal . "', volume_title = '" . $volume_title . "', volume_editors = '" . $volume_editors . "', website_title = '" . $website_title . "', pub_location = '" . $pub_location . "', publisher = '" . $publisher . "', date = '" . $date . "', dateaccessed = '" . $dateaccessed . "', url = '" . $url . "', volume = '" . $volume ."', issue = '" . $issue . "', pages = '" . $pages . "', description = '" . $description . "', type = '" . $type . "' WHERE entryID = '" . $id . "'";
        $wpdb->query($sql);
        
        $sqlCheck = "SELECT entryID FROM " . $wpdb->prefix . "bibliography WHERE author_last = '" . $author_last . "' and author_first = '" . $author_first . "' and author_two_last = '" . $author_two_last . "' and author_two_first = '" . $author_two_first . "' and title = '" . $title . "' and short_title = '" . $short_title . "' and journal = '" . $journal . "' and volume_title = '" . $volume_title . "' and volume_editors = '" . $volume_editors . "' and website_title = '" . $website_title . "' and pub_location = '" . $pub_location . "' and publisher = '" . $publisher . "' and date = '" . $date . "' and dateaccessed = '" . $dateaccessed . "' and url = '" . $url . "' and volume = '" . $volume ."' and issue = '" . $issue . "' and pages = '" . $pages . "' and description = '" . $description . "' and type = '" . $type . "' LIMIT 1";
        $check = $wpdb->get_results($sqlCheck);
        if ( !empty($check) || !empty($check[0]->id) ) { 
            echo '<div class="updated"><p>'. sprintf( __( 'Bibliography %1$d updated successfully.', SPCOURSEWARE_TD ), $id ) .'</p></div>';
        }
    }
}

function spcourseware_bibliography_navigation()
{
    ?>
        <p>
			<a href="admin.php?page=<?php echo $_GET['page']; ?>"><?php echo __( 'View Bibliography', SPCOURSEWARE_TD ); ?></a> |
			<a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=add_biblio"><?php echo __( 'Add an Bibliography Entry', SPCOURSEWARE_TD ); ?></a>
		</p>
<?php
}

function spcourseware_bibliography_edit_form($mode='add_biblio', $id=false)
{
	$data = false;
	
	if($mode == 'add_biblio') {
	    echo '<h3>'. __( 'Add an Bibliography Entry', SPCOURSEWARE_TD ) .'</h3>';
	}
	
	if ( $id !== false ) {
		if ( intval($id) != $id ){
			echo '<div class="error"><p>'. sprintf( __( 'Bibliography ID %s1d is not a valid integer.', SPCOURSEWARE_TD), $id ) .'</p></div>';
			return;
		} else {
			$data = spcourseware_get_bibliography_entry_by_id($id);
			echo '<h3>'. __( 'Update Bibliography Entry #', SPCOURSEWARE_TD) .$id. '</h3>';
			if ( empty($data) ) {
				echo "<div class=\"error\"><p>". __( "I couldn't find a quote linked up with that identifier. Giving up...", SPCOURSEWARE_TD ) ."</p></div>";
				return;
			}
		}	
	}
	
    ?>
    
    <form name="biblioform" id="biblioform" class="wrap" method="post" action="">
    		<input type="hidden" name="update_action" value="<?php echo $mode; ?>">
    		<input type="hidden" name="entry_id" value="<?php echo $id; ?>">
            <script type="text/javascript" charset="utf-8">
        	jQuery(function($){

                $('#assignment_title').hide();
                $('#assignment_bibliography_entry').show();

                $("#biblio_type option:selected").each(function(){
                    
                    var val = this.value;
                    
                    if ((val == 'monograph') || (val == 'textbook')) {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').show();
                        $('#biblio_publisher_field').show();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    } else if (val == 'article') {      
                        $('#biblio_journal_field').show();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').show();
                        $('#biblio_issue_field').show();
                        $('#biblio_pages_field').show();
                    } else if (val == 'volumechapter') {        
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').show();
                        $('#biblio_publisher_field').show();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').show();
                        $('#biblio_website_title_field').hide();
                        $('#biblio_dateaccessed_field').hide();
                        $('#biblio_date_field').show(); 
                    } else if (val == 'unpublished') {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_date_field').hide();

                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    } else if (val == 'website') {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_date_field').show();
                        $('#biblio_dateaccessed_field').show();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    }
                });

                $("select#biblio_type").change(function () {
                    var val = this.value;
                    
                    if ((val == 'monograph') || (val == 'textbook')) {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').show();
                        $('#biblio_publisher_field').show();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    } else if (val == 'article') {      
                        $('#biblio_journal_field').show();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').show();
                        $('#biblio_issue_field').show();
                        $('#biblio_pages_field').show();
                    } else if (val == 'volumechapter') {        
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').show();
                        $('#biblio_publisher_field').show();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').show();
                        $('#biblio_website_title_field').hide();
                        $('#biblio_dateaccessed_field').hide();
                        $('#biblio_date_field').show(); 
                    } else if (val == 'unpublished') {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_date_field').hide();

                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    } else if (val == 'website') {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_date_field').show();
                        $('#biblio_dateaccessed_field').show();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    }
                });

        	});
        	</script>
    					<table class="form-table">
    						
    						<tr valign="top">
    						<th>
                            <label for="biblio_type"><?php echo __( 'Type of Bibliography Item' ); ?></labgel></th>
                            <td>
                                <select name="biblio_type" id="biblio_type">
                                    <option><?php echo __( 'Choose a type', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_book" value="monograph"<?php if ( empty($data) || $data->type=='monograph' ) echo ' selected="selected"'; ?>><?php echo __( 'Book', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_article" value="article"<?php if ( !empty($data) && $data->type=='article' ) echo ' selected="selected"'; ?>><?php echo __( 'Article', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_volume" value="volumechapter"<?php if ( !empty($data) && $data->type=='volumechapter' ) echo ' selected="selected"'; ?>><?php echo __( 'Volume Chapter', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_unpublished" value="unpublished"<?php if ( !empty($data) && $data->type=='unpublished' ) echo ' selected="selected"' ?>><?php echo __( 'Unpublished', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_website" value="website"<?php if ( !empty($data) && $data->type=='website' ) echo ' selected="selected"'; ?>><?php echo __( 'Website', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_webpage" value="webpage"<?php if ( !empty($data) && $data->type=='webpage' ) echo ' selected="selected"'; ?>><?php echo __( 'Webpage', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_video" value="video"<?php if ( !empty($data) && $data->type=='video' ) echo ' selected="selected"'; ?>><?php echo __( 'Video', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_audio" value="audio"<?php if ( !empty($data) && $data->type=='audio' ) echo ' selected="selected"'; ?>><?php echo __( 'Audio', SPCOURSEWARE_TD ); ?></option>
                            </select>
                            </td>
                            </tr>
                            
                            <tr valign="top">

    						<th scope="row"><?php echo __( 'Author(s)', SPCOURSEWARE_TD ); ?></th>
    						<td class="inside withlabels">
    							<p><label for="biblio_author_last"><?php echo __( 'Author Last Name', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_author_last" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_last); ?>" />
    							<p><label for="biblio_author_first"><?php echo __( 'Author First Name', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_author_first" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_first); ?>" />
    							<p><label for="biblio_author_two_last"><?php echo __( 'Author Two Last Name', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_author_two_last" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_two_last); ?>" />
    							<p><label for="biblio_author_two_first"><?php echo __( 'Author Two First Name', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_author_two_first" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_two_first); ?>" />
    						</td>
</tr>
<tr valign="top">
    						<th><span><?php echo __( 'Publish Information', SPCOURSEWARE_TD ); ?></span></th>
    						<td>
    						<fieldset class="small" id="biblio_title_field">
    							<p><label for="biblio_title"><?php echo __( 'Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->title); ?>" />
    						</fieldset>
    						<fieldset class="small" id="biblio_short_title_field">
    							<p><label for="biblio_short_title"><?php echo __( 'Short Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_short_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->short_title); ?>" />
    						</fieldset>
    						<fieldset class="small" id="biblio_journal_field">
    							<p><label for="biblio_journal"><?php echo __( 'Journal Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_journal" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->journal); ?>" />
    						</fieldset>

    						<fieldset class="small" id="biblio_volume_title_field">
    							<p><label for="biblio_volume_title"><?php echo __( 'Volume Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_volume_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume_title); ?>" />
    						</fieldset>

    						<fieldset class="small" id="biblio_volume_editors_field">
    							<p><label for="biblio_volume_editors"><?php echo __( 'Volume Editor(s)', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_volume_editors" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume_editors); ?>" />
    						</fieldset>

    						<fieldset class="small" id="biblio_pub_location_field">
    							<p><label for="biblio_pub_location"><?php echo __( 'Place of Publication', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_pub_location" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->pub_location); ?>" />
    						</fieldset>
    						<fieldset class="small" id="biblio_publisher_field">
    							<p><label for="biblio_publisher"><?php echo __( 'Publisher', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_publisher" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->publisher); ?>" />
    						</fieldset>
    						<fieldset class="small" id="biblio_website_title_field">
    							<p><label><?php echo __( 'Website Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_website_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->website_title); ?>" />
    						</fieldset>							
    						</td>
    						</tr>
                            <tr valign="top">

    						<th class='hndle'><span><?php echo __( 'Additional Information', SPCOURSEWARE_TD ); ?></span></th>
    						<td class="inside withlabels">
    							<fieldset class="small" id="biblio_date_field">
    								<p><label for="biblio_date"><?php echo __( 'Date Published', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_date" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->date); ?>" />
    							</fieldset>				
    							<fieldset class="small" id="biblio_dateaccessed_field">
    								<p><label for="biblio_dateaccessed"><?php echo __( 'Date Accessed', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_dateaccessed" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->dateaccessed); ?>" />
    							</fieldset>				
    							<fieldset class="small" id="biblio_url_field">
    								<p><label for="biblio_url"><?php echo __( 'URL', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_url" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->url); ?>" />
    							</fieldset>			
    							<fieldset class="small" id="biblio_volume_field">
    								<p><label for="biblio_volume"><?php echo __( 'Volume', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_volume" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume); ?>" />
    							</fieldset>
    							<fieldset class="small" id="biblio_issue_field">
    								<p><label for="biblio_issue"><?php echo __( 'Issue', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_issue" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->issue); ?>" />
    							</fieldset>
    							<fieldset class="small" id="biblio_pages_field">
    								<p><label for="biblio_pages"><?php echo __( 'Pages', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_pages" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->pages); ?>" />
    							</fieldset>
    							<fieldset class="small" id="biblio_description_field">
    								<p><label for="biblio_description"><?php echo __( 'Description', SPCOURSEWARE_TD ); ?></label></p>
    								<textarea name="biblio_description" class="input" cols=45 rows=7><?php if ( !empty($data) ) echo htmlspecialchars($data->description); ?></textarea>
    							</fieldset>
    						</td>
    					</tr>
    					</table>
		<p class="submit clear"><input type="submit" name="save" class="button-primary" value="<?php echo __( 'Save Entry &raquo;', SPCOURSEWARE_TD ); ?>" /></p>
    	</form>
    	<div class="clear"></div>
    
    <?php
}

// Print small biblio entry (eg title and link only, for use in sidebar)
function spcourseware_bibliography_short($entry, $wrapper='p') 
{ ?>
    <<?php echo $wrapper; ?> class="hcite <?php echo $entry->type; ?>">
    <?php if(!empty($entry->author_last)) { ?><span class="creator fn"><span class="family-name"><?php echo ($entry->author_last); ?></span></span><?php if( !empty($entry->author_two_last)) { ?> and <span class="creator fn"><span class="family-name"><?php echo ($entry->author_two_last); ?></span></span><?php } ?>, <? } if(!empty($entry->url)) { ?><a href="<?php echo $entry->url; ?>"><?php } ?><?php if(!empty($entry->short_title)){ ?> <span class="title"><?php if($entry->type != 'monograph' && $entry->type !='website') { ?>&#8220;<?php } ?><?php echo ($entry->short_title); ?><?php if($entry->type != 'monograph' && $entry->type !='website') { ?></span>.&#8221;<?php } else{ ?>.<?php } ?><?php } elseif(!empty($entry->title)){ ?> <span class="title"><?php if($entry->type != 'monograph' && $entry->type !='website') { ?>&#8220;<?php } ?><?php echo ($entry->title); ?><?php if($entry->type != 'monograph' && $entry->type !='website') { ?></span>.&#8221;<?php } else { ?>.<?php } } if(!empty($entry->url)){?></a><?php } ?>
    </<?php echo $wrapper; ?>>
    <?php 
}

// Print full biblio entry 
function spcourseware_bibliography_full($entry,$bibid=false,$description=false) 
{ ?>
    <div class="hcite <?php echo $entry->type; ?>"<?php if($bibid==true){?> id="bib-entry-<?php echo $entry->entryID; ?>"<?php } ?>>
    <p><?php if( !empty($entry->author_last)): ?><span class="creator fn"><span class="family-name"><?php echo ($entry->author_last); ?></span>, <span class="given-name"><?php echo ($entry->author_first); ?></span><?php if( !empty($entry->author_two_last)): ?> and <span class="given-name"><?php echo ($entry->author_two_first); ?></span> <span class="family-name"><?php echo ($entry->author_two_last); ?></span><?php endif; ?>. <?php endif; ?><?php if($entry->type != 'monograph' && $entry->type !='website'){ ?>&#8220;<?php } if(!empty($entry->url)){ ?><a href="<?php echo($entry->url); ?>"><?php } ?><span class="title"><?php echo nl2br($entry->title); ?></span><?php if ( !empty($entry->url)){ ?></a><?php } ?>.<?php if($entry->type != 'monograph' && $entry->type !='website') { ?>&#8221;<?php } ?>
	
    <?php if ($entry->type == 'monograph'): ?>
    <?php if ( !empty($entry->pub_location)) { ?><span class="location"><?php echo nl2br($entry->pub_location); ?></span>:<?php } ?><?php if (!empty($entry->publisher)) { ?> <span><?php echo nl2br($entry->publisher); ?></span>,<?php } ?> <?php if( !empty($entry->date)) { ?><span class="date"><?php echo ($entry->date); ?></span>.<?php } ?>

    <?php elseif ($entry->type == 'volumechapter'): ?>
    <?php if(!empty($entry->volume_title)) { ?><span class="volume-title"><?php echo nl2br($entry->volume_title); ?></span>. <?php } if(!empty($entry->volume_editors)) { ?><span class="volume-editors"><?php echo $entry->volume_editors; ?>, ed.</span> <?php } if ( !empty($entry->pub_location)) { ?><span class="location"><?php echo nl2br($entry->pub_location); ?></span>:<?php } ?><?php if (!empty($entry->publisher)) { ?> <span><?php echo nl2br($entry->publisher); ?></span>,<?php } ?> <?php if( !empty($entry->date)) { ?><span class="date"><?php echo ($entry->date); ?></span><?php } ?><?php if ( !empty($entry->pages)) { ?>: <span class="pages"><?php echo ($entry->pages); ?></span><?php } ?>.

    <?php elseif ($entry->type == 'article'): ?>
    <span class="journal"><?php echo nl2br($entry->journal); ?></span> <?php if ( !empty($entry->volume) || !empty($entry->issue)) { ?><span class="volume"><?php echo nl2br($entry->volume); ?></span>, no. <span class="issue"><?php echo nl2br($entry->issue); ?></span><?php } ?> <?php if( !empty($entry->date)) { ?>(<span class="date"><?php echo ($entry->date); ?></span>)<?php } ?><?php if ( !empty($entry->pages)) { ?>: <span class="pages"><?php echo ($entry->pages); ?></span><?php } ?>. 

    <?php elseif ($entry->type == 'website'): ?>
    <?php if( !empty($entry->date)) { ?><span class="date-published"><?php echo ($entry->date); ?></span><?php } ?><?php if( !empty($entry->dateaccessed)) { ?>. (Accessed <span class="date-accesed"><?php echo ($entry->dateaccessed); ?></span>)<?php } if(!empty($entry->date) || !empty($entry->dateaccessed)) { ?>.<?php } ?> 

    <?php elseif ($entry->type == 'webpage'): ?>
    <?php if(!empty($entry->website_title)) { ?><span class="website-title"><?php echo $entry->website_title; ?></span>. <?php } ?><?php if( !empty($entry->date)) { ?><span class="date-published"><?php echo ($entry->date); ?></span><?php } ?><?php if( !empty($entry->dateaccessed)) { ?>. (Accessed <span class="date-accesed"><?php echo ($entry->dateaccessed); ?></span>)<?php } if(!empty($entry->date) || !empty($entry->dateaccessed)) { ?>.<?php } ?>

    <?php elseif($entry->type == 'unpublished'): ?>
    <?php if( !empty($entry->date)) { ?><span class="date-published"><?php echo ($entry->date); ?>.</span><?php } ?> 

    <?php else: ?>
    <?php if( !empty($entry->date)) { ?><span class="date-published"><?php echo ($entry->date); ?></span>.<?php } ?>
    <?php endif;  ?></p>
    <?php if ( $description==true && !empty($entry->description) ) { ?>
    	<p class="description"><?php echo nl2br($entry->description);?></p>
    <?php } ?>
    </div>
    <?php
}

// Add management pages to the administration panel; sink function for 'admin_menu' hook

function spcourseware_dashboard() {
	?>
	<div class="wrap">
	<h2><?php echo __( "Dashboard", SPCOURSEWARE_TD ); ?> | <?php echo __( "ScholarPress Courseware", SPCOURSEWARE_TD ); ?></h2>

	<div id="courseinfo">
		<h3><?php echo __( "Course Information", SPCOURSEWARE_TD ); ?></h3>
		<?php echo spcourseware_courseinfo_printfull(); ?>
	<a href="?page=courseinfo"><?php echo __( "Edit Course Information", SPCOURSEWARE_TD ); ?></a>
	</div>
	</div>
	<br class="clear" />
	<?php
}

function spcourseware_get_schedule_entries($limit=null, $recent=false)
{
    global $wpdb;
    $schedule_table_name = $wpdb->prefix . "schedule";
	
	$sql = "SELECT * FROM " . $schedule_table_name;
	
	if($recent == true) {
	    $date = date("Y-m-d");
	    $sql .= " WHERE schedule_date > '$date'";
	}
	$sql .= " ORDER BY schedule_date, schedule_timestart";
	if($limit != null) {
	    $sql .= " LIMIT ". $limit;
	}
	
	$results = $wpdb->get_results($sql, OBJECT);
	return $results;
}

function spcourseware_get_schedule_entries_by_date($date)
{    
    global $wpdb;
    $schedule_table_name = $wpdb->prefix . "schedule";
	
	$date = date('Y-m-d', strtotime($date));
    
	$sql = "SELECT * FROM " . $schedule_table_name . " WHERE schedule_date = '$date' ORDER BY schedule_date, schedule_timestart";
	
	$results = $wpdb->get_results($sql, OBJECT);
	return $results;
}

function spcourseware_get_schedule_entry_by_id($id) 
{
    global $wpdb;
	$schedule_table_name = $wpdb->prefix . "schedule";
	$sql = "SELECT * from " . $schedule_table_name . " where scheduleID=".$id;
	return $wpdb->get_row($sql, OBJECT);
}

function spcourseware_delete_schedule_entry($id)
{
    global $wpdb;
    if($id) {
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "schedule WHERE scheduleID = '" . $id . "'");
    }
}

function spcourseware_add_schedule_entry()
{
    global $wpdb;
    
    $spcoursewareAdminOptions = spcourseware_courseinfo_get_fields();

    $defaultStart = !empty($spcoursewareAdminOptions['course_timestart']) ? $spcoursewareAdminOptions['course_timestart'] : date('H:i:s');
    $defaultStop = !empty($spcoursewareAdminOptions['course_timeend']) ? $spcoursewareAdminOptions['course_timeend'] : date('H:i:s');

    $title = !empty($_REQUEST['schedule_title']) ? $_REQUEST['schedule_title'] : '';
    $date = !empty($_REQUEST['schedule_date']) ? date('Y-m-d', strtotime($_REQUEST['schedule_date'])) : date('Y-m-d');
    $description = !empty($_REQUEST['schedule_description']) ? $_REQUEST['schedule_description'] : '';	
    $timestart = !empty($_REQUEST['schedule_timestart']) ? date('H:i:s', strtotime($_REQUEST['schedule_timestart'])) : $defaultStart;
    $timestop = !empty($_REQUEST['schedule_timestop']) ? date('H:i:s', strtotime($_REQUEST['schedule_timestop'])) : $defaultStop;	
    
    $sql = "INSERT INTO " . $wpdb->prefix . "schedule SET schedule_title = '" . $title . "', schedule_date = '" . $date . "', schedule_timestart = '" . $timestart . "', schedule_timestop = '" . $timestop . "', schedule_description = '" . $description . "'";
    $wpdb->query($sql);
    
    $sqlCheck = "SELECT scheduleID FROM " . $wpdb->prefix . "schedule WHERE schedule_title = '" . $title . "' and schedule_date = '" . $date . "' and schedule_description = '" . $description . "'";
	$check = $wpdb->get_results($sqlCheck);

	if ( !empty($check) || !empty($check[0]->scheduleID) )
	{
		?><div class="updated"><p><?php echo sprintf( __( 'Schedule entry #%1$d added successfully.', SPCOURSEWARE_TD), $check[0]->scheduleID ); ?></p></div><?php
	}
	
}

function spcourseware_update_schedule_entry($id)
{
    global $wpdb;
    
    $spcoursewareAdminOptions = spcourseware_courseinfo_get_fields();

    $defaultStart = !empty($spcoursewareAdminOptions['course_timestart']) ? $spcoursewareAdminOptions['course_timestart'] : date('H:i:s');
    $defaultStop = !empty($spcoursewareAdminOptions['course_timeend']) ? $spcoursewareAdminOptions['course_timeend'] : date('H:i:s');

    $title = !empty($_REQUEST['schedule_title']) ? $_REQUEST['schedule_title'] : '';
    $date = !empty($_REQUEST['schedule_date']) ? date('Y-m-d', strtotime($_REQUEST['schedule_date'])) : date('Y-m-d');
    $description = !empty($_REQUEST['schedule_description']) ? $_REQUEST['schedule_description'] : '';	
    $timestart = !empty($_REQUEST['schedule_timestart']) ? date('H:i:s', strtotime($_REQUEST['schedule_timestart'])) : $defaultStart;
    $timestop = !empty($_REQUEST['schedule_timestop']) ? date('H:i:s', strtotime($_REQUEST['schedule_timestop'])) : $defaultStop;	

    if($id) {
        $wpdb->query("UPDATE " . $wpdb->prefix . "schedule SET schedule_title = '" . $title . "', schedule_date = '" . $date . "', schedule_timestart = '" . $timestart . "', schedule_timestop = '" . $timestop . "', schedule_description = '" . $description . "'  WHERE scheduleID = '" . $id . "'");
        
        $sql = "SELECT scheduleID FROM " . $wpdb->prefix . "schedule WHERE schedule_title = '" . $title . "' and schedule_date = '" . $date . "' and schedule_description = '" . $description . "'  LIMIT 1";
    	$check = $wpdb->get_results($sql);
    	if ( !empty($check) || !empty($check[0]->scheduleID) )
    	{
    		?><div class="updated"><p><?php echo sprintf( __( 'Schedule entry #%1$d updated successfully.', SPCOURSEWARE_TD), $id ); ?></p></div><?php
    	}
    }
	
}

function spcourseware_schedule_navigation()
{
    ?>
        <p><a href="admin.php?page=<?php echo $_GET['page']; ?>"><?php echo __( 'View Schedule', SPCOURSEWARE_TD ); ?></a> | <a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=add_schedule"><?php echo __( 'Add an Schedule Entry', SPCOURSEWARE_TD ); ?></a></p>
<?php
}


// Displays the add/edit form
function spcourseware_schedule_edit_form($mode='add_schedule', $id=false)
{
	$data = false;
	
	if ( $id !== false ) {
		if ( intval($id) != $id ){
			echo '<div class="error"><p>'. sprintf( __( 'Schedule ID %1$d is not a valid integer.', SPCOURSEWARE_TD), $id ) .'</p></div>';
			return;
		} else {
			$data = spcourseware_get_schedule_entry_by_id($id);
			if ( empty($data) ) {
				echo "<div class=\"error\"><p>". __( "I couldn't find a quote linked up with that identifier. Giving up...", SPCOURSEWARE_TD ) ."</p></div>";
				return;
			}
		}	
	}    
	$spcoursewareAdminOptions = spcourseware_courseinfo_get_fields();
	
	$action = 'admin.php?page='. $_GET['page'];
	if($_REQUEST['update_action'] == 'edit_biblio') {
	    $action .= '&amp;view=form&amp;entry_id='.$_GET['entry_id'];
	}
	
?>
<?php $url = WP_PLUGIN_URL."/scholarpress-courseware/datepicker/images/calendar.gif"; ?>		
							<script type="text/javascript" charset="utf-8">
							jQuery("#schedule_date").datepicker({ 
							    dateFormat: jQuery.datepicker.W3C, 
								showOn: "button", //change to button once button works 
							    buttonImage: "<?php echo $url;?>",
							    buttonImageOnly: true 
							});
							</script>
    <!-- Beginning of Add Schedule Entry -->
	<form name="scheduleform" id="scheduleform" class="wrap" method="post" action="<?php echo $action; ?>">


		<input type="hidden" name="update_action" value="<?php echo $mode; ?>">
		<input type="hidden" name="schedule_id" value="<?php echo $id; ?>">

			<table class="form-table">
				<tr>
						<th scope="row"><label for="schedule_title"><?php echo __('Title', SPCOURSEWARE_TD); ?></label></th>
						<td>
						<input type="text" id="title" name="schedule_title" class="input" size="45" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_title); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="schedule_description"><?php echo __( 'Description', SPCOURSEWARE_TD ); ?></label></th>
					<td>
						<textarea name="schedule_description" id="description" cols="45" rows="7"><?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_description); ?></textarea>
						<p class="description"><?php echo __( 'Enter in a description of what will go on in this class.', SPCOURSEWARE_TD ); ?></p>
				</tr>
            			<tr valign="top">
            			    <th scope="row"><span><?php echo __( 'Date &amp; Time', SPCOURSEWARE_TD ); ?></span></th>
            				<td>
            							<p><label><?php echo __('Date: Input any date string, or use the date picker', SPCOURSEWARE_TD ); ?></label></p>
            							<input type="text" name="schedule_date" id="schedule_date" class="format-y-m-d divider-dash split-date date" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_date); ?>" />			
            							<?php $url = WP_PLUGIN_URL."/scholarpress-courseware/core/includes/datepicker/images/calendar.gif"; ?>		
            							<script type="text/javascript" charset="utf-8">
            							jQuery("#schedule_date").datepicker({ 
            							    dateFormat: jQuery.datepicker.W3C, 
            								showOn: "button", //change to button once button works 
            							    buttonImage: "<?php echo $url;?>",
            							    buttonImageOnly: true 
            							});
            							</script>							

            							<p><label for="schedule_timestart"><?php echo __( 'Class Starts', SPCOURSEWARE_TD ); ?> (ex. 12:00pm)</label></p>
            							<?php ?><input type="text" name="schedule_timestart" class="date"  value="<?php if ( !empty($data) ) {echo date('g:ia',strtotime($data->schedule_timestart));} else {echo date('g:ia', strtotime($spcoursewareAdminOptions['course_time_start']));} ?>" /> <?php ?>

            							<p><label for="schedule_timestop"><?php echo __( 'Class Ends', SPCOURSEWARE_TD ); ?> (ex. 1:00pm)</label></p>
            							<input type="text" name="schedule_timestop" class="date"  value="<?php if ( !empty($data) ){ echo date('g:ia', strtotime($data->schedule_timestop));} else {echo date('g:ia', strtotime($spcoursewareAdminOptions['course_time_end']));} ?>" />													

            				</td>
            			</tr>
            		</table><!-- End side info column-->

    				<p class="submit">
    					<input type="submit" name="save" class="button-primary" value="<?php echo __( 'Publish Schedule Entry &raquo;', SPCOURSEWARE_TD ); ?>" />
    				</p>
	</form>
	<div class="clear"></div>
	
<?php
}

// Print small reading entry (eg title and link only, for use in sidebar)
function spcourseware_schedule_short($entry,$wrapper="li",$title="h4", $description=false)
{ 
	$startTime = strtotime($entry->schedule_date.' '.$entry->schedule_timestart);
	$endTime = strtotime($entry->schedule_date.' '.$entry->schedule_timestop);
	?>
	<<?php echo $wrapper; ?> class="vevent">
		<div class="date">
			<span class="dtstart">
			    <abbr class="value" title="<?php echo date('Y-m-d', $startTime); ?>"><?php echo date('F d, Y', $startTime); ?></abbr>, <span class="value"><?php echo date('g:i a', $startTime); ?></span>
			</span>
			&ndash;
			<span class="dtend">
			    <span class="value-title" title="<?php echo date('Y-m-d', $endTime); ?>"></span><span class="value"><?php echo date('g:i a', $endTime); ?></span>
			</span>
		</div>
<<?php echo $title; ?> class="summary"><?php echo nl2br($entry->schedule_title); ?></<?php echo $title; ?>>	

    <?php if($description == true && $scheduleDescription = $entry->schedule_description) echo $scheduleDescription; ?>

	</<?php echo $wrapper; ?>> <?php
}


function spcourseware_schedule_printfull()
{
    $scheduleEntries = spcourseware_get_schedule_entries();
    if($scheduleEntries) {
        $html = '<ul>';
        
        foreach($scheduleEntries as $entry) {
            $html .= spcourseware_schedule_short($entry);
        }
        $html .= '</ul>';
    } else {
        $html = '<p>'. __( 'You have no schedule entries!', SPCOURSEWARE_TD ) .'</p>';
    }
    
    return $html;
}