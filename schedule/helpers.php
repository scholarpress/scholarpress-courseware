<?php

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
		?><div class="updated"><p>Schedule entry #<?php echo $check[0]->scheduleID;?> added successfully.</p></div><?php
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
    		?><div class="updated"><p>Schedule entry #<?php echo $id; ?> updated successfully.</p></div><?php
    	}
    }
	
}

function spcourseware_schedule_navigation()
{
    ?>
        <p><a href="admin.php?page=<?php echo $_GET['page']; ?>">View Schedule</a> | <a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=add_schedule">Add an Schedule Entry</a></p>
<?php
}


// Displays the add/edit form
function spcourseware_schedule_edit_form($mode='add_schedule', $id=false)
{
	$data = false;
	
	if ( $id !== false ) {
		if ( intval($id) != $id ){
			echo '<div class="error"><p>Schedule ID '. $id .' is not a valid integer.</p></div>';
			return;
		} else {
			$data = spcourseware_get_schedule_entry_by_id($id);
			if ( empty($data) ) {
				echo "<div class=\"error\"><p>I couldn't find a quote linked up with that identifier. Giving up...</p></div>";
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
						<th scope="row"><label for="schedule_title"><?php _e('Title'); ?></label></th>
						<td>
						<input type="text" id="title" name="schedule_title" class="input" size="45" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_title); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="schedule_description">Description</label></th>
					<td>
						<textarea name="schedule_description" id="description" cols="45" rows="7"><?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_description); ?></textarea>
						<p class="description">Enter in a description of what will go on in this class.</p>
				</tr>
            			<tr valign="top">
            			    <th scope="row"><span>Date &amp; Time</span></th>
            				<td>
            							<p><label><?php _e('Date: Input any date string, or use the date picker'); ?></label></p>
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

            							<p><label for="schedule_timestart"><?php _e('Class Starts'); ?> (e.g. 12:00pm)</label></p>
            							<?php ?><input type="text" name="schedule_timestart" class="date"  value="<?php if ( !empty($data) ) {echo date('g:ia',strtotime($data->schedule_timestart));} else {echo date('g:ia', strtotime($spcoursewareAdminOptions['course_time_start']));} ?>" /> <?php ?>

            							<p><label for="schedule_timestop"><?php _e('Class Ends'); ?> (e.g. 1:00pm)</label></p>
            							<input type="text" name="schedule_timestop" class="date"  value="<?php if ( !empty($data) ){ echo date('g:ia', strtotime($data->schedule_timestop));} else {echo date('g:ia', strtotime($spcoursewareAdminOptions['course_time_end']));} ?>" />													

            				</td>
            			</tr>
            		</table><!-- End side info column-->

    				<p class="submit">
    					<input type="submit" name="save" class="button-primary" value="Publish Schedule Entry &raquo;" />
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
        return $html;
    }
}
?>