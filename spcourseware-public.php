<?php 
// Page Delimiters 
define('SP_PROJECTS_PAGE', '<spprojects />');
define('SP_COURSEINFO_PAGE', '<spcourseinfo />');

/* ======== Begin template printing functions ========*/

// Print Full Project
function courseinfo_printfull() 
{
	global $wpdb;
	
	$courseinfo = spcourseware_get_admin_options();

	$course_title = $courseinfo['course_title'];
	$course_description = $courseinfo['course_description'];
	$course_location = $courseinfo['course_location'];
	$course_number = $courseinfo['course_number'];
	$course_section = $courseinfo['course_section'];
	$course_timestart = $courseinfo['course_timestart'];
	$course_timeend = $courseinfo['course_timeend'];
	$course_timedays = $courseinfo['course_timedays'];
	
	$instructor_firstname = $courseinfo['instructor_firstname'];
	$instructor_lastname = $courseinfo['instructor_lastname'];
	$instructor_telephone = $courseinfo['instructor_telephone'];
	$instructor_office = $courseinfo['instructor_office'];
	$instructor_hours = $courseinfo['instructor_hours'];
	$instructor_email = $courseinfo['instructor_email'];
	
	$starttime = strtotime($course_timestart);
	$endtime = strtotime($course_timeend);
	
?>
	<div class="courseinfo">
	    
		<p>
		<?php if(!empty($course_number)): ?><span class="course-number"><?php echo $course_number; ?></span><?php endif; ?><?php if(!empty($course_title)): ?>: 
		    <span class="course-title"><?php echo $course_title; ?></span>
		    <?php endif; ?>
		</p>
		<?php if(!empty($course_location)): ?>
		<p class="location"><?php echo $course_location; ?></p>
		<?php endif; ?>
		
		
		<p class="timedays">
		<?php if(!empty($course_timedays)): ?>
		    <span class="days"><?php echo $course_timedays; ?></span>
		<?php endif; ?>
		<?php if(!empty($starttime)): ?>, 
		    <?php echo date('g:i a',$starttime); ?>
	        <?php if(!empty($endtime)): ?>
	            &ndash;<?php echo date('g:i a',$endtime); ?>
	        <?php endif; ?>
		<?php endif; ?>
		</p>
		
		<ul class="vcard instructor">
			<li><span class="fn n"><span class="given-name"><?php echo $instructor_firstname; ?></span> <span class="family-name"><?php echo $instructor_lastname; ?></span></span></li>
			<li><span class="office"><?php echo $instructor_office; ?></span></li>
			
			<li><a href="mailto:<?php echo $instructor_email; ?>" class="email"><?php echo $instructor_email; ?></a></li>
			<li><span class="tel"><?php echo $instructor_telephone; ?></span></li>
		</ul>

	</div>
<?php 
}

// Print full assignment
function assign_printsmall($assignmentsmall, $full="small")
{ 
	echo '<div class="assignment">';
		//print_r($assignmentfull);
		if ($assignmentsmall->assignments_type=='reading') {
			?><?php bib_specific($assignmentfull->assignments_bibliographyID, $full); ?><?php if ( !empty($assignmentsmall->assignments_pages)): ?>, <span class="pages"><?php echo $assignmentsmall->assignments_pages; ?></span>.<?php endif; ?>
	<?php } else { ?>
			<div class="assignment-title"><?php echo $assignmentsmall->assignments_title; ?></div>
	<?php } ?>
	
	</div>
 <?php 
}

// Print full assignment
function assign_printfull($assignmentfull, $full="small")
{ 
	echo '<div class="assignment">';
		//print_r($assignmentfull);
		if ($assignmentfull->assignments_type=='reading') {
			?><?php bib_specific($assignmentfull->assignments_bibliographyID, $full); ?><?php if ( !empty($assignmentfull->assignments_pages)): ?>, <span class="pages"><?php echo $assignmentfull->assignments_pages; ?></span>.<?php endif; ?>
			
	<?php } else { ?>
			<div class="assignment-title"><?php echo $assignmentfull->assignments_title; ?></div>
			<div class="assignment-description"><?php echo $assignmentfull->assignments_description; ?></div>
	<?php } ?>
	
	<?php if (!empty($assignmentfull->assignments_description)){ ?><div class="description"><?php echo $assignmentfull->assignments_description; ?></div>
	<?php } ?>
	</div>
 <?php 
}

function assign_schedulefull($assignmentfull)
{ 
		//print_r($assignmentfull);
		
		if ($assignmentfull['assignments_type']=='reading') {
			?><?php bib_specific($assignmentfull['assignments_bibliographyID'], $full, $assignment=true); ?><?php if ( !empty($assignmentfull['assignments_pages'])): ?> <span class="pages"><?php echo $assignmentfull['assignments_pages']; ?></span>.<?php endif;
			if ( !empty($assignmentfull['assignments_description'])): ?><div class="assignment-description"><?php echo $assignmentfull['assignments_description']; ?></div>
			<?php endif; ?>
	<?php }
		else { ?>
			<div class="assignment-title"><?php echo $assignmentfull['assignments_title']; ?></div>
			<div class="assignment-description"><?php echo spcourseware_nls2p($assignmentfull['assignments_description']); ?></div>
		<?php } 
}

// Print specific assignments entry
function assign_specific($id, $full="small")
{
	global $wpdb;
	$table_name = $wpdb->prefix . "assignments";
	
	if ($full=="full")
	{
		$sql = "select * from " . $table_name . " where assignmentID='{$id}'";
		$result = $wpdb->get_results($sql);
		if ( !empty($result) ) assign_printfull($result[0]);
	} else {
		$sql = "select * from " . $table_name . " where assignmentID='{$id}'";
		$result = $wpdb->get_results($sql);
		if ( !empty($result) ) assign_printsmall($result[0]);
	}	
}

// Print all schedule entries onto a page, sorted by type

// Print Full Project
function project_printfull($projectID) 
{
	global $wpdb;
	$table_name = $wpdb->prefix . "projects";	
	$sql = "select * from " . $table_name . " where projectID=".$projectID;
	$result = $wpdb->get_row($sql, OBJECT);
?>
	<div class="project" id="<?php echo($projectID); ?>">
		<?php if(!empty($result->title)): ?><h3><?php echo $result->title; ?></h3><?php endif;?>
	<!--	<?php if(!empty($result->date)): ?><p class="date"><?php echo $result->date; ?></p><?php endif;?> -->
		<?php if(!empty($result->description)): ?><div class="description"><?php echo $result->description; ?></div><?php endif; ?>
	</div>
<?php 
}

// Print all project entries onto a page, sorted by type
function project_page($data)
{
global $wpdb;
$table_name = $wpdb->prefix . "projects";
$start = strpos($data, SP_PROJECTS_PAGE);
if ( $start !==false )
	{
	ob_start();
	global $wpdb;
	$sql_projects = $wpdb->get_results("SELECT projectID FROM " . $table_name . " ORDER BY projectID", ARRAY_A);
	if (count($sql_projects) > 0)
		{
			?><div id="projects"><?php
		
			foreach ( $sql_projects as $projectID )
			{
				project_printfull($projectID['projectID']);
			}
			?></div><?php
		}

	$contents = ob_get_contents();
	ob_end_clean();
	$data = substr_replace($data, $contents, $start, strlen(SP_PROJECTS_PAGE));
	}
	return $data;	
}
/* ======== End template printing functions ========*/

?>