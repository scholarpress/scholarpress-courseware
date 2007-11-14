<?php 
/*
Plugin Name: ScholarPress Courseware
Plugin URI: http://scholarpress.net/courseware/
Description: All-in-one course management for WordPress
Version: 1.0.1
Author: Jeremy Boggs, Josh Greenberg, and Dave Lester
Author URI: http://scholarpress.net/
*/

/*
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Misc functions
//Adapted from PHP.net: http://us.php.net/manual/en/function.nl2br.php#73479
function nls2p($str)
{
  return str_replace('<p></p>', '', '<p>'
        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
        . '</p>');
}

include_once 'spcourseware-public.php';
include_once 'spcourseware-bibliography.php';
include_once 'spcourseware-schedule.php';

// Page Delimiters 
define('SP_BIBLIOGRAPHY_PAGE', '<spbibliography />');
define('SP_SCHEDULE_PAGE', '<spschedule />');
define('SP_PROJECTS_PAGE', '<spprojects />');
define('SP_COURSEINFO_PAGE', '<spcourseinfo />');

function getAdminOptions() {
	$spcoursewareOptions = get_option('SpCoursewareAdminOptions');
	if (!empty($spcoursewareOptions)) {
		foreach ($spcoursewareOptions as $key => $option)
			$spcoursewareAdminOptions[$key] = $option;
		}
	return $spcoursewareAdminOptions;
}

function setAdminOptions($course_title, $course_number, $course_section, $course_timestart, $course_timeend, $course_location, $course_timedays, $instructor_firstname, $instructor_lastname, $instructor_email, $instructor_telephone, $instructor_office, $course_description, $instructor_hours) {
	$spcoursewareAdminOptions = array('course_title' => $course_title,
		'course_number' => $course_number,
		'course_section' => $course_section,
		'course_timestart' => $course_timestart,
		'course_timeend' => $course_timeend,
		'course_location' => $course_location,
		'course_timedays' => $course_timedays,
		'instructor_firstname' => $instructor_firstname,
		'instructor_lastname' => $instructor_lastname,
		'instructor_email' => $instructor_email,
		'instructor_telephone' => $instructor_telephone,
		'instructor_office' => $instructor_office,
		'course_description' => $course_description,
		'instructor_hours' => $instructor_hours);
		
	update_option('SpCoursewareAdminOptions', $spcoursewareAdminOptions);
}

// Install the courseware
function courseware_install () {

	global $table_prefix, $wpdb, $user_level;

	// Check user-level
	get_currentuserinfo();
	if ($user_level < 8) { return; }
	
	// table names
	$assignments_table_name = $table_prefix . "assignments";
	$bib_table_name = $table_prefix . "bibliography";
	$schedule_table_name = $table_prefix . "schedule";
	$projects_table_name = $table_prefix . "projects";
	$assignment2project_table_name = $table_prefix . "assignment2project";
	$units_table_name = $table_prefix . "units";
	$schedule2unit_table_name = $table_prefix . "schedule2unit";
	 
	// First-Run-Only parameters: Check if assignments table exists:
	if($wpdb->get_var("show tables like '$assignments_table_name'") != $assignments_table_name) 
	{
		// It doesn't exist, create the table
		$sql = "CREATE TABLE `" . $assignments_table_name . "` (
	     	 `assignmentID` INT(11) NOT NULL AUTO_INCREMENT,
			 `assignments_title` TEXT NOT NULL,
			 `assignments_scheduleID` INT NOT NULL,
			 `assignments_bibliographyID` INT NOT NULL,
			 `assignments_assignedScheduleID` INT NOT NULL,
			 `assignments_pages` VARCHAR(255) NOT NULL,
			 `assignments_description` TEXT NOT NULL,
			 `assignments_type` ENUM('reading','writing','presentation','groupwork','research','discussion', 'creative') NOT NULL,
			 PRIMARY KEY (`assignmentID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	    dbDelta($sql);
		
	}
	
	// First-Run-Only parameters: Check if bibliography table exists:
	if($wpdb->get_var("show tables like '$bib_table_name'") != $bib_table_name) 
	{
		 // It doesn't exist, create the table
		$sql = "CREATE TABLE `" . $bib_table_name . "` (
	     	 `entryID` INT(11) NOT NULL AUTO_INCREMENT,
	    	 `author_last` TEXT NOT NULL,
	     	 `author_first` TEXT NOT NULL,
			`author_two_last` TEXT NOT NULL,
			`author_two_first` TEXT NOT NULL,
			 `title` TEXT NOT NULL,
			`short_title` TEXT NOT NULL,
			 `journal` TEXT NOT NULL,
			 `volume_title` TEXT NOT NULL,
			 `volume_editors` TEXT NOT NULL,
			 `website_title` TEXT NOT NULL,	
			 `pub_location` TEXT NOT NULL,
			 `publisher` TEXT NOT NULL,
			 `date` TEXT NOT NULL,
			 `dateaccessed` TEXT NOT NULL,
			 `url` VARCHAR(255) NOT NULL,
			 `volume` VARCHAR(255) NOT NULL,
			 `issue` VARCHAR(255) NOT NULL,
			 `pages` VARCHAR(255) NOT NULL,
			 `description` TEXT NOT NULL,
			 `type` ENUM('monograph','textbook','article','volumechapter','unpublished','website','webpage','audio','video') NOT NULL,
			 PRIMARY KEY (`entryID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if schedule table exists:
	if($wpdb->get_var("show tables like '$schedule_table_name'") != $schedule_table_name) 
	{
		// It doesn't exist, create the table
		$sql = "CREATE TABLE `" . $schedule_table_name . "` (
	     	 `scheduleID` INT(11) NOT NULL AUTO_INCREMENT,
			 `schedule_title` tinytext NOT NULL,
			 `schedule_date` DATE NOT NULL,
			 `schedule_timestart` TIME NOT NULL,
			 `schedule_timestop` TIME NOT NULL,
			 `schedule_description` TEXT NOT NULL,
			 PRIMARY KEY (`scheduleID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if table exists:
	if($wpdb->get_var("show tables like '$projects_table_name'") != $projects_table_name) 
	{
		 // It doesn't exist, create the table
	   $sql = "CREATE TABLE `" . $projects_table_name . "` (
	     	 `projectID` INT(11) NOT NULL AUTO_INCREMENT,
			 `title` TEXT NOT NULL,
			`date` DATE NOT NULL,
			 `description` TEXT NOT NULL,
			 PRIMARY KEY (`projectID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if table exists:
	if($wpdb->get_var("show tables like '$assignment2project_table_name'") != $assignment2project_table_name) 
	{
		 // It doesn't exist, create the table
	   $sql = "CREATE TABLE `" . $assignment2project_table_name . "` (
	     	 `assignment2projectID` INT(11) NOT NULL AUTO_INCREMENT,
			 `assignmentID` INT NOT NULL,
			 `projectID` INT NOT NULL,
			 `modified` TIMESTAMP NOT NULL,
			 PRIMARY KEY (`assignment2projectID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if table exists:
	if($wpdb->get_var("show tables like '$units_table_name'") != $units_table_name) 
	{
		 // It doesn't exist, create the table
	   $sql = "CREATE TABLE `" . $units_table_name . "` (
	     	 `unitID` INT(11) NOT NULL AUTO_INCREMENT,
			 `title` TEXT NOT NULL,
			 `description` TEXT NOT NULL,
			 PRIMARY KEY (`unitID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if table exists:
	if($wpdb->get_var("show tables like '$schedule2unit_table_name'") != $schedule2unit_table_name) 
	{
		 // It doesn't exist, create the table
	   $sql = "CREATE TABLE `" . $schedule2unit_table_name . "` (
	     	 `schedule2unitID` INT(11) NOT NULL AUTO_INCREMENT,
			 `scheduleID` INT NOT NULL,
			 `unitID` INT NOT NULL,
			 `modified` TIMESTAMP NOT NULL,
			 PRIMARY KEY (`schedule2unitID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	    dbDelta($sql);
	}
	
	// Add course info stuff to the options table. You know, for course information.
	setAdminOptions(null, null, null, null, null, null, null, null, null, null, null, null, null, null);

	/// POPULATE DB WITH BIBLIOGRAPHY, SCHEDULE, PROJECTS, PAGES IF NOT ALREADY CREATED
	$now = time();
	$now_gmt = time();
	$parent_id = 1; // Uncategorized default
	$post_modified = $now;
	$post_modified_gmt = $now_gmt;
	
	if (!$wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_title='Bibliography'", OBJECT))
	{

		$bibliography_title = "Bibliography";
		$bibliography_content = "<div id=\"spbibliography\"><spbibliography /></div>";
		$bibliography_excerpt = "";
		$bibliography_status = "publish";
		$bibliography_name = "bibliography";

		//setAdminOptions(1, null, null, null, null, null, null, null, null, null, null, null, null, null);
		wp_insert_post(array(
		'post_author'		=> '1',
		'post_date'			=> $post_dt,
		'post_date_gmt'		=> $post_dt,
		'post_modified'		=> $post_modified_gmt,
		'post_modified_gmt'	=> $post_modified_gmt,
		'post_title'		=> $bibliography_title,
		'post_content'		=> $bibliography_content,
		'post_excerpt'		=> $bibliography_excerpt,
		'post_status'		=> $bibliography_status,
		'post_name'			=> $bibliography_name,
		'post_type' 		=> 'page')			
		);
	}
	
	if (!$wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_title='Schedule'", OBJECT)) 
	{
		$schedule_title = "Schedule";
		$schedule_content = "<div id=\"spschedule\"><spschedule /></div>";
		$schedule_excerpt = "";
		$schedule_status = "publish";
		$schedule_name = "schedule";
		wp_insert_post(array(
		'post_author'		=> '1',
		'post_date'		=> $post_dt,
		'post_date_gmt'		=> $post_dt,
		'post_modified'		=> $post_modified_gmt,
		'post_modified_gmt'	=> $post_modified_gmt,
		'post_title'		=> $schedule_title,
		'post_content'		=> $schedule_content,
		'post_excerpt'		=> $schedule_excerpt,
		'post_status'		=> $schedule_status,
		'post_name'		=> $schedule_name,
		'post_type' => 'page')			
		);
	}
}

// Add management pages to the administration panel; sink function for 'admin_menu' hook
function courseware_admin_menu()
{
	add_menu_page('spcourseware','SP Courseware',8,'spcourseware','spcourseware_manage');
	add_submenu_page('spcourseware','schedule', 'Schedule', 8, 'schedule', 'schedule_manage');
	add_submenu_page('spcourseware','bibliography', 'Bibliography', 8, 'bibliography', 'bibliography_manage');
	add_submenu_page('spcourseware','assignments', 'Assignments', 8, 'assignments', 'assignments_manage');
	add_submenu_page('spcourseware','courseinfo', 'Course Information', 8, 'courseinfo', 'courseinfo_manage');
}


/* ======== Backend management pages ========*/

// Set up admin stylesheet

function courseware_admin_style() 
{
    $url = get_settings('siteurl');
    $url = $url . '/wp-content/plugins/spcourseware/spadmin.css';
	echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
}

function courseware_admin_scripts() 
{
    $url = get_settings('siteurl');
    $url = $url . '/wp-content/plugins/spcourseware/spcourseware.js';
	echo '<script type="text/javascript" src="' . $url . '"></script>';
}

add_action('admin_head', 'courseware_admin_style');
add_action('admin_head', 'courseware_admin_scripts');

// Set up the public stylesheet

function courseware_public_style() 
{
    $url = get_settings('siteurl');
    $url = $url . '/wp-content/plugins/spcourseware/spcourseware.css';
	echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
}

add_action('wp_head', 'courseware_public_style');

function spcourseware_manage() {
	?>
	<div class="wrap">
	<h2>SP Courseware</h2>
	
	<div id="schedule-assignments">
	<h3>Upcoming Schedule</h3>
	<ul>
	<?php schedule_upcoming(); ?>
	</ul>
	<a href="?page=schedule">Edit Schedule Entries</a>
	</div>

	<div id="courseinfo">
		<h3>Course Information</h3>
		<?php courseinfo_printfull(); ?>
	<a href="?page=courseinfo">Edit Course Information</a>
	</div>
	<br class="clear" />
	<?php
}

// Handles the assignments management page
function assignments_manage()
{
	global $table_prefix, $wpdb;

	$updateaction = !empty($_REQUEST['updateaction']) ? $_REQUEST['updateaction'] : '';
	$assignmentID = !empty($_REQUEST['assignmentID']) ? $_REQUEST['assignmentID'] : '';
	
	if (isset($_REQUEST['action']) ):
		if ($_REQUEST['action'] == 'delete_assignment') 
		{
			$assignmentID = intval($_GET['assignmentID']);
			if (empty($assignmentID))
			{
				?><div class="error"><p><strong>Failure:</strong> No assignments ID given. I guess I deleted nothing successfully.</p></div><?php
			}
			else
			{
				$wpdb->query("DELETE FROM " . $table_prefix . "assignments WHERE assignmentID = '" . $assignmentID . "'");
				$sql = "SELECT assignmentID FROM " . $table_prefix . "assignments WHERE assignmentID = '" . $assignmentID . "'";
				$check = $wpdb->get_results($sql);
				if ( empty($check) || empty($check[0]->assignmentID) )
				{
					?><div class="updated"><p>Reading Entry <?php echo $assignmentID; ?> deleted successfully.</p></div><?php
				}
				else
				{
					?><div class="error"><p><strong>Failure:</strong></p></div><?php
				}
			}
		} // end delete_assignment block
	endif;
	
	if ( $updateaction == 'update_assignment' )
	{
		$title = !empty($_REQUEST['assignment_title']) ? $_REQUEST['assignment_title'] : '';
		$scheduleID = !empty($_REQUEST['assignment_scheduleID']) ? $_REQUEST['assignment_scheduleID'] : '';
		$bibliographyID = !empty($_REQUEST['assignment_bibliographyID']) ? $_REQUEST['assignment_bibliographyID'] : '';
		$type = !empty($_REQUEST['assignment_type']) ? $_REQUEST['assignment_type'] : '';
		$pages = !empty($_REQUEST['assignment_pages']) ? $_REQUEST['assignment_pages'] : '';
		$description = !empty($_REQUEST['assignment_description']) ? $_REQUEST['assignment_description'] : '';
		
		if ( empty($assignmentID) )
		{
			?><div class="error"><p><strong>Failure:</strong> No reading-id given. Can't save nothing. Giving up...</p></div><?php
		}
		else
		{
			$sql = "UPDATE " . $table_prefix . "assignments SET assignments_title = '" . $title . "', assignments_scheduleID = '" . $scheduleID . "',  assignments_bibliographyID = '" . $bibliographyID . "', assignments_type = '" . $type . "', assignments_pages = '" . $pages . "', assignments_description = '" . $description . "' WHERE assignmentID = '" . $assignmentID . "'";
			$wpdb->get_results($sql);
			$sql = "SELECT assignmentID FROM " . $table_prefix . "assignments WHERE assignments_title = '" . $title . "' and assignments_scheduleID = '" . $scheduleID . "' and assignments_type = '" . $type . "' and assignments_pages = '" . $pages . "' and assignments_description = '" . $description . "' LIMIT 1";
			$check = $wpdb->get_results($sql);
			if ( empty($check) || empty($check[0]->assignmentID) )
			{
				?><div class="error"><p><strong>Failure:</strong> I couldn't update your entry. Try again?</p></div><?php
			}
			else
			{
				?><div class="updated"><p>Assignment <?php echo $assignmentID; ?> updated successfully.</p></div><?php
			}
		}
	} // end update_assignment block
	elseif ( $updateaction == 'add_assignment' )
	{
		$title = !empty($_REQUEST['assignment_title']) ? $_REQUEST['assignment_title'] : '';
		$scheduleID = !empty($_REQUEST['assignment_scheduleID']) ? $_REQUEST['assignment_scheduleID'] : '';
		$bibliographyID = !empty($_REQUEST['assignment_bibliographyID']) ? $_REQUEST['assignment_bibliographyID'] : '';
		$type = !empty($_REQUEST['assignment_type']) ? $_REQUEST['assignment_type'] : '';
		$pages = !empty($_REQUEST['assignment_pages']) ? $_REQUEST['assignment_pages'] : '';
		$description = !empty($_REQUEST['assignment_description']) ? $_REQUEST['assignment_description'] : '';
		
		$sql = "INSERT INTO " . $table_prefix . "assignments SET assignments_title = '" . $title . "', assignments_scheduleID = '" . $scheduleID . "', assignments_bibliographyID = '" . $bibliographyID . "',  assignments_type = '" . $type . "', assignments_pages = '" . $pages . "', assignments_description = '" . $description . "'";
		$wpdb->get_results($sql);
		$sql = "SELECT assignmentID FROM " . $table_prefix . "assignments WHERE assignments_title = '" . $title . "' and assignments_scheduleID = '" . $scheduleID . "' and assignments_type = '" . $type . "' and assignments_pages = '" . $pages . "' and assignments_description = '" . $description . "'";
		$check = $wpdb->get_results($sql);
		if ( empty($check) || empty($check[0]->assignmentID) )
		{
			?><div class="error"><p><strong>Failure:</strong> Try again? </p></div><?php
		}
		else
		{
			?><div class="updated"><p>Yeah! Assignment <?php echo $check[0]->assignmentID;?> added successfully.</p></div><?php
		}
	} // end add_assignment block
	?>

	<div class="wrap">
	<?php
	if ( $_REQUEST['action'] == 'edit_assignment' )
	{
		?>
		<h2><?php _e('Edit reading'); ?></h2>
		<?php
		if ( empty($assignmentID) )
		{
			echo "<div class=\"error\"><p>I didn't get an entry identifier from the query string. Giving up...</p></div>";
		}
		else
		{
			assignments_editform('update_assignment', $assignmentID);
		}	
	}
	else
	{
		?>
		<h2><?php _e('Add Entry'); ?></h2>
		<?php assignments_editform(); ?>
	
		<h2><?php _e('Manage Assignments'); ?></h2>
		<?php
			assignments_displaylist();
	}
	?>
	</div><?php
}

// Displays the list of assignments entries
function assignments_displaylist() 
{
	global $wpdb, $table_prefix;
	
	$assignments = $wpdb->get_results("SELECT * FROM ".$table_prefix."assignments LEFT JOIN ".$table_prefix."bibliography ON ".$table_prefix."assignments.assignments_bibliographyID = ".$table_prefix."bibliography.entryID LEFT JOIN ".$table_prefix."schedule ON ".$table_prefix."assignments.assignments_scheduleID = ".$table_prefix."schedule.scheduleID ORDER BY assignmentID DESC");
	
	if ( !empty($assignments) )
	{
		?>
			<table width="100%" cellpadding="3" cellspacing="3">
			<tr>
				<th scope="col"><?php _e('ID') ?></th>
				<th scope="col"><?php _e('Title') ?></th>
				<th scope="col"><?php _e('Description') ?></th>
				<th scope="col"><?php _e('Date') ?></th>
				<th scope="col"><?php _e('Type') ?></th>
				<th scope="col"><?php _e('Edit') ?></th>
				<th scope="col"><?php _e('Delete') ?></th>
			</tr>
		<?php
		$class = '';
		foreach ( $assignments as $assignment )
		{
			$class = ($class == 'alternate') ? '' : 'alternate';
			?>
			<tr class="<?php echo $class; ?>">
				<th scope="row"><?php echo $assignment->assignmentID; ?></th>
				<td><?php if ($assignment->assignments_type=='reading') echo $assignment->title; else echo $assignment->assignments_title; ?></td>
				<td><?php echo $assignment->assignments_description; ?></td>
				<td><?php echo $assignment->schedule_date; ?></td>
				<td><?php echo $assignment->assignments_type; ?></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=edit_assignment&amp;assignmentID=<?php echo $assignment->assignmentID;?>" class="edit"><?php echo __('Edit'); ?></a></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_assignment&amp;assignmentID=<?php echo $assignment->assignmentID;?>" class="delete" onclick="return confirm('Are you sure you want to delete this entry?')"><?php echo __('Delete'); ?></a></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		?>
		<p><?php _e("You haven't entered any reading entries yet.") ?></p>
		<?php	
	}
}


// Displays the add/edit form
function assignments_editform($mode='add_assignment', $assignmentID=false)
{
	global $wpdb, $table_prefix;
	$data = false;
	
	if ( $assignmentID !== false )
	{
		// this next line makes me about 200 times cooler than you.
		if ( intval($assignmentID) != $assignmentID )
		{
			echo "<div class=\"error\"><p>Bad Monkey! No banana!</p></div>";
			return;
		}
		else
		{
			$data = $wpdb->get_results("SELECT * FROM " . $table_prefix . "assignments WHERE assignmentID = '" . $assignmentID . " LIMIT 1'");
			if ( empty($data) )
			{
				echo "<div class=\"error\"><p>I couldn't find a quote linked up with that identifier. Giving up...</p></div>";
				return;
			}
			$data = $data[0];
		}	
	}
	
	?>
	<form name="readingform" id="readingform" class="wrap" method="post" action="">
		<input type="hidden" name="updateaction" value="<?php echo $mode?>">
		<input type="hidden" name="assignmentID" value="<?php echo $assignmentID?>">
	
		<div id="item_manager">
			<div style="float: left; width: 98%; clear: both;" class="top">

			<div style="float: right; " class="top">
			</div>
		
		<!-- List URL -->
					
				<fieldset class="small"><legend><?php _e('Date Due'); ?></legend>
						<select name="assignment_scheduleID">
							<option value=""></option>
							<?php 
								// Get schedule events from DB
								$SQL = 'SELECT * from '.$table_prefix.'schedule ORDER BY schedule_date, schedule_timestart';
								$dates = $wpdb->get_results($SQL, OBJECT);
								foreach ($dates as $date) {
							?>
							<option value="<?php echo $date->scheduleID; ?>"<?php if ($date->scheduleID==$data->assignments_scheduleID) echo " selected"; ?>><?php echo date('F d, Y', strtotime($date->schedule_date)); ?>: <?php echo $date->schedule_title; ?></option>
							<?php } ?>
						</select>
				</fieldset>
				<fieldset class="small"><legend><?php _e('Date Assigned (optional)'); ?></legend>
						<select name="assignment_assignedScheduleID">
							<option value=""></option>
							<?php 
								// Get schedule events from DB
								$SQL = 'SELECT * from '.$table_prefix.'schedule ORDER BY schedule_date, schedule_timestart';
								$dates = $wpdb->get_results($SQL, OBJECT);
								foreach ($dates as $date) {
							?>
							<option value="<?php echo $date->scheduleID; ?>"<?php if ($date->scheduleID==$data->assignments_assignedScheduleID) echo " selected"; ?>><?php echo date('F d, Y', strtotime($date->schedule_date)); ?>: <?php echo $date->schedule_title; ?></option>
							<?php } ?>
						</select>
				</fieldset>
				<fieldset class="small"><legend><?php _e('Type'); ?></legend>
					<input type="radio" onClick="toggleAssignmentType()" name="assignment_type" class="input" value="reading" 
					<?php if ( empty($data) || $data->assignments_type=='reading' ) echo "checked" ?>/>
					<label for="assignment_type">Reading</label>

					<input type="radio" onClick="toggleAssignmentType()" name="assignment_type" name="assignment_type" class="input" value="writing" 
					<?php if ( !empty($data) && $data->assignments_type=='writing' ) echo "checked" ?>/>  
					<label for="assignment_type">Writing</label>
					
					<input type="radio" onClick="toggleAssignmentType()" name="assignment_type" class="input" value="presentation" 
					<?php if ( !empty($data) && $data->assignments_type=='presentation' ) echo "checked" ?>/>  
					<label for="assignment_type">Presentation</label>

					<input type="radio" onClick="toggleAssignmentType()" name="assignment_type" class="input" value="groupwork" 
					<?php if ( !empty($data) && $data->assignments_type=='groupwork' ) echo "checked" ?>/> 
					<label for="assignment_type">Group Work </label>
					
					<input type="radio" onClick="toggleAssignmentType()" name="assignment_type" class="input" value="research" 
					<?php if ( !empty($data) && $data->assignments_type=='research' ) echo "checked" ?>/>  
					<label for="assignment_type">Research</label>

					<input type="radio" onClick="toggleAssignmentType()" name="assignment_type" class="input" value="discussion" 
					<?php if ( !empty($data) && $data->assignments_type=='discussion' ) echo "checked" ?>/> 
					<label for="assignment_type">Discussion</label>
					
					<input type="radio" onClick="toggleAssignmentType()" name="assignment_type" class="input" value="creative" 
					<?php if ( !empty($data) && $data->assignments_type=='creative' ) echo "checked" ?>/>  
					<label for="assignment_type">Creative</label>
					
				</fieldset>
				
				<fieldset class="small" id="bibfield"<?php if (@$data->type!='reading' && @$data) echo ' style="display:none;"';?>><legend><?php _e('Bibliography'); ?></legend>
					<select name="assignment_bibliographyID">
						<option value=""></option>
						<?php 
							// Get bibliography events from DB
							$SQL = 'SELECT * from '.$table_prefix.'bibliography ORDER BY author_last, title';
							$bibs = $wpdb->get_results($SQL, OBJECT);
							foreach ($bibs as $bib) {
						?>
						<option value="<?php echo $bib->entryID; ?>"<?php if ($bib->entryID==$data->assignments_bibliographyID) echo " selected"; ?>><?php echo $bib->author_last; ?>: <?php echo $bib->title; ?></option>
						<?php } ?>
					</select>
				</fieldset>				

				<fieldset class="small" id="titlefield"<?php if (@$data->type=='reading' || !@$data) echo 's style="display:none;"';?>><legend><?php _e('Title'); ?></legend>
					<input type="text" name="assignment_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_title); ?>" />
				</fieldset>

				<fieldset class="small" id="pagesfield"><legend><?php _e('Pages'); ?></legend>
					<input type="text" name="assignment_pages" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_pages); ?>" />
				</fieldset>

				<fieldset class="small"><legend><?php _e('Description'); ?></legend>
					<textarea name="assignment_description" class="input" cols=45 rows=7><?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_description); ?></textarea>
				</fieldset>
				<input type="submit" name="save" class="button bold" value="Save &raquo;" />
			</div>
			<div style="clear:both; height:1px;">&nbsp;</div>
		</div>
	</form>
	<?php
}

// Handles the schedule management page
function schedule_manage()
{
	global $table_prefix, $wpdb;

	$updateaction = !empty($_REQUEST['updateaction']) ? $_REQUEST['updateaction'] : '';
	$scheduleID = !empty($_REQUEST['scheduleID']) ? $_REQUEST['scheduleID'] : '';
	
	if (isset($_REQUEST['action']) ):
		if ($_REQUEST['action'] == 'delete_schedule') 
		{
			$scheduleID = intval($_GET['scheduleID']);
			if (empty($scheduleID))
			{
				?><div class="error"><p><strong>Failure:</strong> No schedule ID given. I guess I deleted nothing successfully.</p></div><?php
			}
			else
			{
				$wpdb->query("DELETE FROM " . $table_prefix . "schedule WHERE scheduleID = '" . $scheduleID . "'");
				$sql = "SELECT scheduleID FROM " . $table_prefix . "schedule WHERE scheduleID = '" . $scheduleID . "'";
				$check = $wpdb->get_results($sql);
				if ( empty($check) || empty($check[0]->scheduleID) )
				{
					?><div class="updated"><p>Schedule Entry <?php echo $scheduleID; ?> deleted successfully.</p></div><?php
				}
				else
				{
					?><div class="error"><p><strong>Failure:</strong></p></div><?php
				}
			}
		} // end delete_schedule block
	endif;
	
	if ( $updateaction == 'update_schedule' )
	{
		$title = !empty($_REQUEST['schedule_title']) ? $_REQUEST['schedule_title'] : '';
		$date = !empty($_REQUEST['schedule_date']) ? $_REQUEST['schedule_date'] : '';
		$description = !empty($_REQUEST['schedule_description']) ? $_REQUEST['schedule_description'] : '';
		$timestart = !empty($_REQUEST['schedule_timestart']) ? $_REQUEST['schedule_timestart'] : '';
		$timestop = !empty($_REQUEST['schedule_timestop']) ? $_REQUEST['schedule_timestop'] : '';
		
		if ( empty($scheduleID) )
		{
			?><div class="error"><p><strong>Failure:</strong> No schedule ID given. Can't save nothing. Giving up...</p></div><?php
		}
		else
		{
			$sql = "UPDATE " . $table_prefix . "schedule SET schedule_title = '" . $title . "', schedule_date = '" . $date . "', schedule_timestart = '" . $timestart . "', schedule_timestop = '" . $timestop . "', schedule_description = '" . $description . "'  WHERE scheduleID = '" . $scheduleID . "'";
			$wpdb->get_results($sql);
			$sql = "SELECT scheduleID FROM " . $table_prefix . "schedule WHERE schedule_title = '" . $title . "' and schedule_date = '" . $date . "' and schedule_description = '" . $description . "'  LIMIT 1";
			$check = $wpdb->get_results($sql);
			if ( empty($check) || empty($check[0]->scheduleID) )
			{
				?><div class="error"><p><strong>Failure:</strong> The Evil Monkey Overlord wouldn't let me update your entry. Try again?</p></div><?php
			}
			else
			{
				?><div class="updated"><p>schedule <?php echo $scheduleID; ?> updated successfully.</p></div><?php
			}
		}
	} // end update_schedule block
	elseif ( $updateaction == 'add_schedule' )
	{
		$title = !empty($_REQUEST['schedule_title']) ? $_REQUEST['schedule_title'] : '';
		$date = !empty($_REQUEST['schedule_date']) ? $_REQUEST['schedule_date'] : '';
		$description = !empty($_REQUEST['schedule_description']) ? $_REQUEST['schedule_description'] : '';
		$timestart = !empty($_REQUEST['schedule_timestart']) ? $_REQUEST['schedule_timestart'] : '';
		$timestop = !empty($_REQUEST['schedule_timestop']) ? $_REQUEST['schedule_timestop'] : '';
		
		$sql = "INSERT INTO " . $table_prefix . "schedule SET schedule_title = '" . $title . "', schedule_date = '" . $date . "', schedule_timestart = '" . $timestart . "', schedule_timestop = '" . $timestop . "', schedule_description = '" . $description . "'";
		$wpdb->get_results($sql);
		$sqlres = "SELECT scheduleID FROM " . $table_prefix . "schedule WHERE schedule_title = '" . $title . "' and schedule_date = '" . $date . "' and schedule_description = '" . $description . "'";
		$check = $wpdb->get_results($sqlres);

		if ( empty($check) || empty($check[0]->scheduleID) )
		{
			?><div class="error"><p><strong>Failure:</strong> Try again? <?php echo $sqlres; ?></p></div><?php
		}
		else
		{
			?><div class="updated"><p>Writing up a storm! Schedule id <?php echo $check[0]->scheduleID;?> added successfully.</p></div><?php
		}
	} // end add_schedule block
	?>

	<div class=wrap>
	<?php
	if ( $_REQUEST['action'] == 'edit_schedule' )
	{
		?>
		<h2><?php _e('Edit schedule entry'); ?></h2>
		<?php
		if ( empty($scheduleID) )
		{
			echo "<div class=\"error\"><p>I didn't get an entry identifier from the query string. Giving up...</p></div>";
		}
		else
		{
			schedule_editform('update_schedule', $scheduleID);
		}	
	}
	else
	{
		?>
		<h2><?php _e('Add Schedule Entry'); ?></h2>
		<?php schedule_editform(); ?>
	
		<h2><?php _e('Manage schedule'); ?></h2>
		<?php
			schedule_displaylist();
	}
	?>
	</div><?php
}

// Displays the list of schedule entries
function schedule_displaylist() 
{
	global $wpdb, $table_prefix;
	
	$schedule = $wpdb->get_results("SELECT * FROM " . $table_prefix . "schedule ORDER BY scheduleID DESC");
	
	if ( !empty($schedule) )
	{
		?>
			<table width="100%" cellpadding="3" cellspacing="3">
			<tr>
				<th scope="col"><?php _e('ID') ?></th>
				<th scope="col"><?php _e('Title') ?></th>
				<th scope="col"><?php _e('Description') ?></th>
				<th scope="col"><?php _e('Date') ?></th>
				<th scope="col"><?php _e('Edit') ?></th>
				<th scope="col"><?php _e('Delete') ?></th>
			</tr>
		<?php
		$class = '';
		foreach ( $schedule as $schedule )
		{
			$class = ($class == 'alternate') ? '' : 'alternate';
			?>
			<tr class="<?php echo $class; ?>">
				<th scope="row"><?php echo $schedule->scheduleID; ?></th>
				<td><?php echo $schedule->schedule_title ?></td>
				<td><?php echo $schedule->schedule_description ?></td>
				<td><?php echo $schedule->schedule_date ?></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=edit_schedule&amp;scheduleID=<?php echo $schedule->scheduleID;?>" class="edit"><?php echo __('Edit'); ?></a></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_schedule&amp;scheduleID=<?php echo $schedule->scheduleID;?>" class="delete" onclick="return confirm('Are you sure you want to delete this entry?')"><?php echo __('Delete'); ?></a></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		?>
		<p><?php _e("You haven't entered any schedule entries yet.") ?></p>
		<?php	
	}
}


// Displays the add/edit form
function schedule_editform($mode='add_schedule', $scheduleID=false)
{
	global $wpdb, $table_prefix;
	$data = false;
	
	if ( $scheduleID !== false )
	{
		// this next line makes me about 200 times cooler than you.
		if ( intval($scheduleID) != $scheduleID )
		{
			echo "<div class=\"error\"><p>Bad Monkey! No banana!</p></div>";
			return;
		}
		else
		{
			$data = $wpdb->get_results("SELECT * FROM " . $table_prefix . "schedule WHERE scheduleID = '" . $scheduleID . " LIMIT 1'");
			if ( empty($data) )
			{
				echo "<div class=\"error\"><p>I couldn't find a quote linked up with that identifier. Giving up...</p></div>";
				return;
			}
			$data = $data[0];
		}	
	}
	
	getAdminOptions();

	?>
	<form name="scheduleform" id="scheduleform" class="wrap" method="post" action="">
		<input type="hidden" name="updateaction" value="<?php echo $mode?>">
		<input type="hidden" name="scheduleID" value="<?php echo $scheduleID?>">
	
		<div id="item_manager">
			<div style="float: left; width: 68%; clear: left;" class="top">

				<!-- List URL -->
				<fieldset class="small"><legend><?php _e('Title'); ?></legend>
					<input type="text" name="schedule_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_title); ?>" />
				</fieldset>


				<fieldset class="small"><legend><?php _e('Date'); ?> (YYYY-MM-DD)</legend>
					<input type="text" name="schedule_date" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_date); ?>" />
				</fieldset>

				<fieldset class="small"><legend><?php _e('TimeStart'); ?> (24:00:00)</legend>
					<input type="text" name="schedule_timestart" class="input" size=45 value="<?php if ( !empty($data) ) {echo htmlspecialchars($data->schedule_timestart);} else {echo $spcoursewareAdminOptions['course_timestart'];} ?>" />
				</fieldset>

				<fieldset class="small"><legend><?php _e('TimeStop'); ?> (24:00:00)</legend>
					<input type="text" name="schedule_timestop" class="input" size=45 value="<?php if ( !empty($data) ){ echo htmlspecialchars($data->schedule_timestop); } else {echo $spcoursewareAdminOptions['course_timeend'];} ?>" />
				</fieldset>

				<fieldset class="small"><legend><?php _e('Description'); ?></legend>
					<textarea name="schedule_description" class="input" cols=45 rows=7><?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_description); ?></textarea>
				</fieldset>
<!-- 
				<fieldset class="small"><legend><?php _e('Associated Assignments'); ?></legend>
				
				<p class="description">Below are classroom assignments associated with this schedule.</p>
				<?php assignments_displaylist(); ?>
 -->
				<input type="submit" name="save" class="button bold" value="Save &raquo;" />


			</div>
			<div style="clear:both; height:1px;">&nbsp;</div>
		</div>
	</form>
	<?php
}

// Displays the course info form
function courseinfo_manage()
{
	global $wpdb;
	$data = false;

	if ($_POST['saveInfo']) {
		setAdminOptions($_REQUEST['course_title'], $_REQUEST['course_number'], $_REQUEST['course_section'], $_REQUEST['course_timestart'], $_REQUEST['course_timeend'], $_REQUEST['course_location'], $_REQUEST['course_timedays'], $_REQUEST['instructor_firstname'], $_REQUEST['instructor_lastname'], $_REQUEST['instructor_email'], $_REQUEST['instructor_telephone'], $_REQUEST['instructor_office'],  $_REQUEST['course_description'], $_REQUEST['instructor_hours']);
	
		echo '<div class="updated"><p>Course information saved successfully.</p></div>';
	
	}

	$spcoursewareAdminOptions = getAdminOptions();

	?>
	<form name="courseinfoform" id="courseinfoform" class="wrap" method="post" action="">
		<input type="hidden" name="updateinfo" value="<?php echo $mode?>">
	
		<div id="item_manager">
			<div style="float: left; width: 98%; clear: both;" class="top">
				<!-- List URL -->
				<h2>Course Information Management</h2>
				<h3>Course Information</h3>
				<fieldset class="small"><legend><?php _e('Course Title'); ?></legend>
					<input type="text" name="course_title" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['course_title']; ?>" />
				</fieldset>
				<fieldset class="small"><legend><?php _e('Course Number'); ?></legend>
					<input type="text" name="course_number" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['course_number']; ?>" />
				</fieldset>
				<fieldset class="small"><legend><?php _e('Course Section'); ?></legend>
					<input type="text" name="course_section" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['course_section']; ?>" />
				</fieldset>
				<fieldset class="small"><legend><?php _e('Course Days'); ?></legend>
					<input type="text" name="course_timedays" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['course_timedays']; ?>" />
				</fieldset>
				<fieldset class="small"><legend><?php _e('Course Time Start'); ?></legend>
					<input type="text" name="course_timestart" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['course_timestart']; ?>" />
				</fieldset>
				<fieldset class="small"><legend><?php _e('Course Time End'); ?></legend>
					<input type="text" name="course_timeend" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['course_timeend']; ?>" />
				</fieldset>
				
				<fieldset class="small"><legend><?php _e('Course Location'); ?></legend>
					<input type="text" name="course_location" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['course_location']; ?>" />
				</fieldset>
				
				<fieldset class="small"><legend><?php _e('Course Description'); ?></legend>
					<textarea name="course_description" class="input" cols=45 rows=7><?php echo $spcoursewareAdminOptions['course_description']; ?></textarea>
				</fieldset>
				
				<h3>Instructor Information</h3>
				<fieldset class="small"><legend><?php _e('Instructor First Name'); ?></legend>
					<input type="text" name="instructor_firstname" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['instructor_firstname']; ?>" />
				</fieldset>
				
				<fieldset class="small"><legend><?php _e('Instructor Last Name'); ?></legend>
					<input type="text" name="instructor_lastname" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['instructor_lastname']; ?>" />
				</fieldset>
				
				<fieldset class="small"><legend><?php _e('Instructor Email'); ?></legend>
					<input type="text" name="instructor_email" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['instructor_email']; ?>" />
				</fieldset>
				
				<fieldset class="small"><legend><?php _e('Instructor Telephone'); ?></legend>
					<input type="text" name="instructor_telephone" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['instructor_telephone']; ?>" />
				</fieldset>
				
				<fieldset class="small"><legend><?php _e('Instructor Office Location'); ?></legend>
					<input type="text" name="instructor_office" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['instructor_office']; ?>" />
				</fieldset>
				
				<fieldset class="small"><legend><?php _e('Instructor Office Hours'); ?></legend>
					<input type="text" name="instructor_hours" class="input" size=45 value="<?php echo $spcoursewareAdminOptions['instructor_hours']; ?>" />
				</fieldset>
				<input type="submit" name="saveInfo" class="button bold" value="Save &raquo;" />


			</div>
			<div style="clear:both; height:1px;">&nbsp;</div>
		</div>
	</form>
	<?php
}

if (isset($_GET['activate']) && $_GET['activate'] == 'true')
{
	add_action('init', 'courseware_install');
}

// Insert sinks into the plugin hook list for 'admin_menu'
add_action('admin_menu', 'courseware_admin_menu');

/* ======== End hook stuff up ========*/
/* ======== Hook stuff up ========*/

add_filter('the_content', 'bib_page', 10);
add_filter('the_content', 'project_page',10);

?>