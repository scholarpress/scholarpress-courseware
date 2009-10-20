<?php

// Add management pages to the administration panel; sink function for 'admin_menu' hook

function spcourseware_dashboard() {
	?>
	<div class="wrap">
	<h2>Dashbard | ScholarPress Courseware</h2>

	<div id="courseinfo">
		<h3>Course Information</h3>
		<?php echo spcourseware_courseinfo_printfull(); ?>
	<a href="?page=courseinfo">Edit Course Information</a>
	</div>
	</div>
	<br class="clear" />
	<?php
}
?>