<?php

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
?>