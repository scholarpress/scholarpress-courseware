<?php
include_once 'spcourseware-public.php';
include_once 'spcourseware-bibliography.php';

define('SP_SCHEDULE_PAGE', '<spschedule />');

// Print small reading entry (eg title and link only, for use in sidebar)
function schedule_printsmall($scheduleID,$title="h3")
{ 
	global $wpdb;
	$table_name = $wpdb->prefix . "schedule";
	$assignments_table_name = $wpdb->prefix . "assignments";
	$bib_table_name = $wpdb->prefix . "bibliography";
	$sql = "select * from " . $table_name . " where scheduleID=".$scheduleID;
	$result = $wpdb->get_row($sql, OBJECT);
	$startTime = strtotime($result->schedule_date.' '.$result->schedule_timestart);
	$endTime = strtotime($result->schedule_date.' '.$result->schedule_timestop);
	?>
	<li class="vevent">
		<div class="date">
			<abbr class="dtstart" title="<?php echo date('Y-m-d\TH:i:s\Z', $startTime); ?>">
				<?php echo date('F d, Y, g:i a', $startTime); ?>
			</abbr>
			&ndash;
			<abbr class="dtend" title="<?php echo date('Y-m-d\T-H:i:s\Z', $endTime); ?>">
				<?php echo date('g:i a', $endTime); ?>
			</abbr>
		</div>
<<?php echo $title; ?> class="summary"><?php echo nl2br($result->schedule_title); ?></<?php echo $title; ?>>	


	</li> <?php
}

// Print full reading entry 
function schedule_printfull($scheduleID, $full='full',$date_first=true)
{ 
	global $wpdb;
	$table_name = $wpdb->prefix . "schedule";
	$assignments_table_name = $wpdb->prefix . "assignments";
	$bib_table_name = $wpdb->prefix . "bibliography";
	$sql = "select * from " . $table_name . " where scheduleID=".$scheduleID;
	$result = $wpdb->get_row($sql, OBJECT);
	$startTime = strtotime($result->schedule_date.' '.$result->schedule_timestart);
	$endTime = strtotime($result->schedule_date.' '.$result->schedule_timestop);
	?>
	<!-- vevent,summary,dtstart,dtend,description used for hCal microformat -->
	
	<div class="vevent">
		<?php if($date_first==true): ?>
		<div class="date">
			<abbr class="dtstart" title="<?php echo date('Y-m-d\TH:i:s\Z', $startTime); ?>">
				<?php echo date('F d, Y, g:i a', $startTime); ?>
			</abbr>
			&ndash;
			<abbr class="dtend" title="<?php echo date('Y-m-d\T-H:i:s\Z', $endTime); ?>">
				<?php echo date('g:i a', $endTime); ?>
			</abbr>
		</div>
		<?php endif; ?>
		<h3 class="summary"><?php echo $result->schedule_title; ?></h3>
		<?php if($date_first==false): ?>
		<div class="date">
			<abbr class="dtstart" title="<?php echo date('Y-m-d\TH:i:s\Z', $startTime); ?>">
				<?php echo date('F d, Y, g:i a', $startTime); ?>
			</abbr>
			&ndash;
			<abbr class="dtend" title="<?php echo date('Y-m-d\T-H:i:s\Z', $endTime); ?>">
				<?php echo date('g:i a', $endTime); ?>
			</abbr>
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
// Print specific schedule entry
function schedule_specific($id, $full="small")
{
	global $wpdb;
	$table_name = $wpdb->prefix . "schedule";
	
	if ($full=="full")
	{
		$sql = "select * from " . $table_name . " where scheduleID='{$id}'";
		$result = $wpdb->get_results($sql);
		if ( !empty($result) ) schedule_printfull($result[0]);
	} else {
		$sql = "select author_last, author_first, title from " . $table_name . " where scheduleID='{$id}'";
		$result = $wpdb->get_results($sql);
		if ( !empty($result) ) schedule_printsmall($result[0]);
	}	
}

function schedule_upcoming($num='4',$title='h4') {
	global $wpdb;
	$table_name = $wpdb->prefix . "schedule";
	$date = date("Y:m:d");
	$sql_schedule = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE schedule_date >= '$date' ORDER BY schedule_date, schedule_timestart LIMIT ".$num, ARRAY_A);
	if (count($sql_schedule) > 0) {
		foreach ($sql_schedule as $schedule) {
			schedule_printsmall($schedule['scheduleID'],$title);
		}
	}
	else {
		echo "<p>There are no upcoming schedule events.</p>";
	}
}

// Print all schedule entries onto a page, sorted by type
function schedule_page($data)
{
global $wpdb;
$table_name = $wpdb->prefix . "schedule";
$start = strpos($data, SP_SCHEDULE_PAGE);
if ( $start !==false )
	{
	ob_start();
	global $wpdb;
	$sql_schedule = $wpdb->get_results("SELECT scheduleID FROM " . $table_name . " ORDER BY schedule_date, schedule_timestart", ARRAY_A);
	

	
	if (count($sql_schedule) > 0)
		{
				echo '<p><a href="webcal://feeds.technorati.com/events/http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'">Subscribe to vCal file</a></p>';
			?><div id="classes" class="vcalendar"><?php
		
			foreach ( $sql_schedule as $scheduleID )
			{
				schedule_printfull($scheduleID['scheduleID']);
			}
			?></div><?php
		}
	else {
		echo '<p>You haven&#8217;t entered any schedule entries!</p>';
	}

	$contents = ob_get_contents();
	ob_end_clean();
	$data = substr_replace($data, $contents, $start, strlen(SP_SCHEDULE_PAGE));
	}
	return $data;	
} 

add_filter('the_content', 'schedule_page', 10);

?>