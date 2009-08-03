<?php
include_once 'spcourseware-public.php';
include_once 'spcourseware-bibliography.php';

define('SP_SCHEDULE_PAGE', '<spschedule />');

add_filter('the_content', 'schedule_page', 10);

/* == Queries == */
function sp_courseware_get_schedule_entries()
{
    global $wpdb;
    $schedule_table_name = $wpdb->prefix . "schedule";
	
	$results = $wpdb->get_results("SELECT scheduleID FROM " . $schedule_table_name . " ORDER BY schedule_date, schedule_timestart", OBJECT);
	return $results;
}

function sp_courseware_get_schedule_by_id($id) 
{
    global $wpdb;
	$schedule_table_name = $wpdb->prefix . "schedule";
	$sql = "select * from " . $schedule_table_name . " where scheduleID=".$id;
	return $wpdb->get_row($sql, OBJECT);
}

function sp_courseware_schedule_get_upcoming_entries($num=4)
{
    global $wpdb;
	$schedule_table_name = $wpdb->prefix . "schedule";
	$date = date("Y:m:d");
	$results = $wpdb->get_results("SELECT * FROM " . $schedule_table_name . " WHERE schedule_date >= '$date' ORDER BY schedule_date, schedule_timestart LIMIT ".$num, OBJECT);
	return $results;
}

/* == Formatting == */

// Print small reading entry (eg title and link only, for use in sidebar)
function schedule_printsmall($scheduleID,$title="h4")
{ 
    $result = sp_courseware_get_schedule_by_id($scheduleID);
	$startTime = strtotime($result->schedule_date.' '.$result->schedule_timestart);
	$endTime = strtotime($result->schedule_date.' '.$result->schedule_timestop);
	?>
	<li class="vevent">
		<div class="date">
			<span class="dtstart">
			    <abbr class="value" title="<?php echo date('Y-m-d', $startTime); ?>"><?php echo date('F d, Y', $startTime); ?></abbr>, <span class="value"><?php echo date('g:i a', $startTime); ?></span>
			</span>
			&ndash;
			<span class="dtend">
			    <span class="value-title" title="<?php echo date('Y-m-d', $endTime); ?>"></span><span class="value"><?php echo date('g:i a', $endTime); ?></span>
			</span>
		</div>
<<?php echo $title; ?> class="summary"><?php echo nl2br($result->schedule_title); ?></<?php echo $title; ?>>	


	</li> <?php
}

// Print full reading entry 
function schedule_printfull($scheduleID, $title='h3', $full='full',$date_first=true)
{ 
	global $wpdb;
	$table_name = $wpdb->prefix . "schedule";
	$assignments_table_name = $wpdb->prefix . "assignments";
	$bib_table_name = $wpdb->prefix . "bibliography";    $result = sp_courseware_get_schedule_by_id($scheduleID);
	$startTime = strtotime($result->schedule_date.' '.$result->schedule_timestart);
	$endTime = strtotime($result->schedule_date.' '.$result->schedule_timestop);
	?>
	<!-- vevent,summary,dtstart,dtend,description used for hCal microformat -->
	
	<div class="vevent">
		<?php if($date_first==true): ?>
    		<div class="date">
    			<span class="dtstart">
    			    <abbr class="value" title="<?php echo date('Y-m-d', $startTime); ?>"><?php echo date('F d, Y', $startTime); ?></abbr>, <span class="value"><?php echo date('g:i a', $startTime); ?></span>
    			</span>
    			&ndash;
    			<span class="dtend">
    			    <span class="value-title" title="<?php echo date('Y-m-d', $endTime); ?>"></span><span class="value"><?php echo date('g:i a', $endTime); ?></span>
    			</span>
    		</div>
		<?php endif; ?>
		<h3 class="summary"><?php echo $result->schedule_title; ?></h3>
		<?php if($date_first==false): ?>
			<div class="date">
    			<span class="dtstart">
    			    <abbr class="value" title="<?php echo date('Y-m-d', $startTime); ?>"><?php echo date('F d, Y', $startTime); ?></abbr>, <span class="value"><?php echo date('g:i', $startTime); ?></span><?php echo date('a', $startTime); ?>
    			</span>
    			&ndash;
    			<span class="dtend">
    			    <span class="value-title" title="<?php echo date('Y-m-d', $endTime); ?>"></span><span class="value"><?php echo date('g:i', $endTime); ?></span><?php echo date('a', $endTime); ?>
    			</span>
    		</div>
		<?php endif; ?>
				<div class="description"> 
				<?php if ($result->schedule_description): ?>
				<p><?php echo nl2br($result->schedule_description); ?></p>
				<?php endif; 
				
				// Show all assignments due that day
					// Get assignment types
					$sql = 'SHOW COLUMNS FROM '.$assignments_table_name.' WHERE field="assignments_type"';
					$types=$wpdb->get_row($sql, ARRAY_N);
					foreach(explode("','",substr($types[1],6,-2)) as $type)
					{
						// Get assignments of that type from db
						$sql = "select * from ".$assignments_table_name." LEFT JOIN ".$bib_table_name." ON ".$assignments_table_name.".assignments_bibliographyID = ".$bib_table_name.".entryID where assignments_scheduleID=".$scheduleID." AND assignments_type='".$type."'";
						$assignments = $wpdb->get_results($sql, ARRAY_A);

						// Echo assignment header
						if ($assignments) 
						{
							echo '<div class="assignment '.$type.'"><h4>'.ucfirst($type).'</h4>';
							echo '<ul>';
						
							foreach ( $assignments as $assignment ) {
								echo '<li>';
								assign_schedulefull($assignment, $full);
								echo '</li>';
							}
							echo '</ul>';
							echo '</div>';
						}
					}	
					
					// Show all assignments *assigned* that day
						// Get assignment types
						$sql = 'SHOW COLUMNS FROM '.$assignments_table_name.' WHERE field="assignments_type"';
						$types=$wpdb->get_row($sql, ARRAY_N);
						foreach(explode("','",substr($types[1],6,-2)) as $type)
						{
							// Get assignments of that type from db
							$sql = "select * from ".$assignments_table_name." LEFT JOIN ".$bib_table_name." ON ".$assignments_table_name.".assignments_bibliographyID = ".$bib_table_name.".entryID LEFT JOIN ".$table_name." ON ".$assignments_table_name.".assignments_scheduleID = ".$table_name.".scheduleID where assignments_assignedScheduleID=".$scheduleID." AND assignments_type='".$type."'";
							$assignments = $wpdb->get_results($sql, ARRAY_A);

							// Echo assignment header
							if ($assignments) 
							{
								echo '<div class="'.$type.'-assigned"><h4>'.ucfirst($type).'</h4>';


								foreach ( $assignments as $assignment ) {
									assign_schedulefull($assignment, $full);
									?><span class="dueDate">due <?php echo date('F d, Y', strtotime($assignment['schedule_date'].' '.$assignment['schedule_timestart'])); ?></span><?php
								}
								
								echo '</div>';
							}
						}?>
					</div>
				</div>
<?php 
}

function schedule_upcoming($num='4',$title='h3') 
{
    $upcoming = sp_courseware_schedule_get_upcoming_entries($num);
	if (count($upcoming) > 0) {
		foreach ($upcoming as $schedule) {
			schedule_printfull($schedule->scheduleID,$title);
		}
	}
	else {
		echo "<p>There are no upcoming schedule events.</p>";
	}
}

// Print all schedule entries onto a page, sorted by type
function schedule_page($data)
{
$start = strpos($data, SP_SCHEDULE_PAGE);
if ( $start !== false )
	{
	ob_start();
    $entries = sp_courseware_get_schedule_entries();
	if (count($entries) > 0)
		{
				echo '<p><a href="webcal://feeds.technorati.com/events/http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'">Subscribe to vCal file</a></p>';
			?><div id="classes" class="vcalendar"><?php
		
			foreach ( $entries as $entry )
			{
				schedule_printfull($entry->scheduleID);
			}
			?></div><?php
		}
	else {
		echo '<p>No upcoming scheduled events have been set.</p>';
	}
	$contents = ob_get_contents();
	ob_end_clean();
	$data = substr_replace($data, $contents, $start, strlen(SP_SCHEDULE_PAGE));
	}
	return $data;	
}