<?php

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
    return array('reading','writing','presentation','groupwork','research','discussion', 'creative');
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
        echo '<div class="updated"><p>Assignment #'. $check[0]->assignmentID .'updated successfully.</p></div>';
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
        echo '<div class="error"><p><strong>Failure:</strong> No Assignment given.</p></div>';
    } else {
        $sql = "UPDATE " . $wpdb->prefix . "assignments SET assignments_title = '" . $title . "', assignments_scheduleID = '" . $scheduleID . "',  assignments_bibliographyID = '" . $bibliographyID . "', assignments_type = '" . $type . "', assignments_pages = '" . $pages . "', assignments_description = '" . $description . "' WHERE assignmentID = '" . $id . "'";
        
        $wpdb->query($sql);
        
        $sqlCheck = "SELECT assignmentID FROM " . $wpdb->prefix . "assignments WHERE assignments_title = '" . $title . "' and assignments_scheduleID = '" . $scheduleID . "' and assignments_type = '" . $type . "' and assignments_pages = '" . $pages . "' and assignments_description = '" . $description . "' LIMIT 1";
        
        $check = $wpdb->get_results($sqlCheck);
        if ( !empty($check) || !empty($check[0]->assignmentID) ) {
            echo '<div class="updated"><p>Assignment #'.$id .' updated successfully.</p></div>';
        }
    }
}

function spcourseware_assignments_navigation()
{
    ?>
        <p><a href="admin.php?page=<?php echo $_GET['page']; ?>">View Assignments</a> | <a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=add_assignment">Add an Assignment</a></p>
<?php
}

function spcourseware_assignment_edit_form($mode='add_assignment', $id=false)
{
    $data = false;
	
	if($mode == 'add_assignment') {
	    echo '<h3>Add an Assignment Entry</h3>';
	}
	
	if ( $id !== false ) {
		if ( intval($id) != $id ){
			echo '<div class="error"><p>Assignment entry #'. $id .' is not a valid integer.</p></div>';
			return;
		} else {
			$data = spcourseware_get_assignment_entry_by_id($id);
			echo '<h3>Update Assignment Entry #'.$id.'</h3>';
			if ( empty($data) ) {
				echo "<div class=\"error\"><p>I couldn't find Assignment #'.$id.'</p></div>";
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
			    <th scope="row"><label for="assignment_type">Type of Assignment</label></th>

                <td>
                    <select name="assignment_type" id="assignment_type">
                    <option value="reading"<?php if ( empty($data) || $data->assignments_type=='reading' ) echo 'selected="selected"'; ?>>Reading</option>
                    <option value="writing"<?php if ( empty($data) || $data->assignments_type=='writing' ) echo ' selected="selected"'; ?>>Writing</option>
                    <option value="presentation" <?php if ( empty($data) || $data->assignments_type=='presentation' ) echo ' selected="selected"'; ?>>Presentation</option>
                    <option value="groupwork"<?php if ( empty($data) || $data->assignments_type=='groupwork' ) echo ' selected="selected"'; ?>>Group Work</option>
                    <option value="research"<?php if ( empty($data) || $data->assignments_type=='research' ) echo ' selected="selected"'; ?>>Research</option>
                    <option value="discussion"<?php if ( empty($data) || $data->assignments_type=='discussion' ) echo ' selected="selected"'; ?>>Discussion</option>	
                    <option value="creative"<?php if ( empty($data) || $data->assignments_type=='creative' ) echo ' selected="selected"'; ?>>Creative</option>		
                    </select>
                </td>
            </tr>
            
            <tr valign="top" id="assignment_title">
			    <th scope="row"><label for="assignment_title"><?php _e('Title'); ?></label></th>
				

				<td><input type="text" id="title" name="assignment_title" class="input" size="45" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_title); ?>" /></td>
			</tr>
            <tr valign="top" id="assignment_bibliography_entry">
				<th scope="row"><label for="assignment_bibliographyID">Choose from your Bibliograhy</label></th>
				<td>
                <select name="assignment_bibliographyID">
						<option value="">Select an entry</option>

						<?php 
						$bibs = spcourseware_get_bibliography_entries();
						if($bibs):
						foreach ($bibs as $bib): ?>
						<option value="<?php echo $bib->entryID; ?>"<?php if ($bib->entryID==$data->assignments_bibliographyID) echo " selected"; ?>><?php echo $bib->author_last; ?>: <?php echo $bib->title; ?></option>
						<?php endforeach; else: ?>
						    <option value="">No Biblography Entries!</option>
						    <?php endif;?>
				</select>
				<label for="assignment_pages">Pages:</label>
				<input type="text" name="assignment_pages" size="10" class="input" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_pages); ?>" />	
				</td>
			</tr>
			<tr valign="top">
					<th><label for="assignment_scheduleID"><?php _e('Date Due'); ?></label></th>
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
						<th scope="row"><label for="assignment_description">Description</label></th>
						<td><textarea name="assignment_description" class="mceEditor input" rows="10" cols="60"><?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_description); ?></textarea>
				        </td>
				    </tr>
                </table>
		<p class="submit">
			<input type="submit" name="save" class="button-primary" value="Save Assignment &raquo;" />
		</p>
	</form>

<?php
}
