<?php

	$schedule_table_name = $table_prefix . "schedule";

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

?>