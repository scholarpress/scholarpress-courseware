<?php
/*
The ScholarPress Magical Loader
this file includes the rest of the core, as well as other courseware 
components (bibliography, schedule) by including the contents of their folders
*/

require_once('helpers.php');

// Assignments includes
require_once(SPCOURSEWARE_PLUGIN_PATH.'assignments/helpers.php');
require_once(SPCOURSEWARE_PLUGIN_PATH.'assignments/index.php');

// Courseinfo includes
require_once(SPCOURSEWARE_PLUGIN_PATH.'courseinfo/helpers.php');
require_once(SPCOURSEWARE_PLUGIN_PATH.'courseinfo/index.php');
require_once(SPCOURSEWARE_PLUGIN_PATH.'courseinfo/shortcodes.php');

// Bibliography includes
require_once(SPCOURSEWARE_PLUGIN_PATH.'bibliography/helpers.php');
require_once(SPCOURSEWARE_PLUGIN_PATH.'bibliography/index.php');
require_once(SPCOURSEWARE_PLUGIN_PATH.'bibliography/shortcodes.php');

// Schedule includes
require_once(SPCOURSEWARE_PLUGIN_PATH.'schedule/helpers.php');
require_once(SPCOURSEWARE_PLUGIN_PATH.'schedule/index.php');
require_once(SPCOURSEWARE_PLUGIN_PATH.'schedule/shortcodes.php');

function spcourseware_install() 
{    

    global $wpdb, $user_level, $spcourseware_version;
    
    $courseware_option_name = 'spcourseware_version'; 
    
    if ( get_option($courseware_option_name) ) {
        update_option($courseware_option_name, SPCOURSEWARE_VERSION_NUMBER);
    } else {
        $deprecated=' ';
        $autoload='no';
        add_option($courseware_option_name, $spcourseware_version, $deprecated, $autoload);
    }
        
    // table names
    $assignments_table_name = $wpdb->prefix . "assignments";
    $bib_table_name = $wpdb->prefix . "bibliography";
    $schedule_table_name = $wpdb->prefix . "schedule";
     
    // First-Run-Only parameters: Check if assignments table exists:
    if($wpdb->get_var("SHOW TABLES LIKE '$assignments_table_name'") != $assignments_table_name) 
    {
        // It doesn't exist, create the table
        $sql = "CREATE TABLE " . $assignments_table_name . " (
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

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
    }
    
    // First-Run-Only parameters: Check if bibliography table exists:
    if($wpdb->get_var("SHOW TABLES LIKE '$bib_table_name'") != $bib_table_name) 
    {
         // It doesn't exist, create the table
        $sql = "CREATE TABLE " . $bib_table_name . " (
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

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    // First-Run-Only parameters: Check if schedule table exists:
    if($wpdb->get_var("SHOW TABLES LIKE '$schedule_table_name'") != $schedule_table_name) 
    {
        // It doesn't exist, create the table
        $sql = "CREATE TABLE " . $schedule_table_name . " (
             `scheduleID` INT(11) NOT NULL AUTO_INCREMENT,
             `schedule_title` tinytext NOT NULL,
             `schedule_date` DATE NOT NULL,
             `schedule_timestart` TIME NOT NULL,
             `schedule_timestop` TIME NOT NULL,
             `schedule_description` TEXT NOT NULL,
             PRIMARY KEY (`scheduleID`)
             )"; 

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    // Create Schedule and Bibliography pages, and include the shortcodes as content.
    	$now = time();
    	$now_gmt = time();
    	$parent_id = 1; // Uncategorized default
    	$post_modified = $now;
    	$post_modified_gmt = $now_gmt;

        $bibliography_title = __( "Bibliography", SPCOURSEWARE_TD);
    	if (!$wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_title='{$bibliography_title}'", OBJECT))
    	{
    		$bibliography_content = "[spbibliography]";
    		$bibliography_excerpt = "";
    		$bibliography_status = "publish";
    		$bibliography_name = "bibliography";

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

        $schedule_title = __( "Schedule", SPCOURSEWARE_TD );
    	if (!$wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_title='{$schedule_title}'", OBJECT)) 
    	{
    		$schedule_content = "[spschedule]";
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

if (isset($_GET['activate']) && $_GET['activate'] == 'true')
{
    add_action('init', 'spcourseware_install');
}

function spcourseware_admin_menu()
{
    $titles[] = __( "Dashboard", SPCOURSEWARE_TD );
    $titles[] = __( "Courseware", SPCOURSEWARE_TD );
    $titles[] = __( "ScholarPress Courseware", SPCOURSEWARE_TD );
    $titles[] = __( "Course Information", SPCOURSEWARE_TD );
    $titles[] = __( "Schedule", SPCOURSEWARE_TD );
    $titles[] = __( "Bibliography", SPCOURSEWARE_TD );
    $titles[] = __( "Assignments", SPCOURSEWARE_TD );
    add_menu_page( $titles[0], $titles[1] , 8, 'scholarpress-courseware', 'spcourseware_dashboard');
    add_submenu_page('scholarpress-courseware', $titles[0].' | '.$titles[2], $titles[1], 8, 'scholarpress-courseware','spcourseware_dashboard');
    add_submenu_page('scholarpress-courseware', $titles[3].' | '.$titles[2], $titles[3], 8, 'courseinfo', 'spcourseware_courseinfo_manage');
    add_submenu_page('scholarpress-courseware', $titles[4].' | '.$titles[2], $titles[4], 8, 'schedule', 'spcourseware_schedule_manage');
    add_submenu_page('scholarpress-courseware', $titles[5].' | '.$titles[2], $titles[5], 8, 'bibliography', 'spcourseware_bibliography_manage');
    add_submenu_page('scholarpress-courseware', $titles[6].' | '.$titles[2], $titles[6], 8, 'assignments', 'spcourseware_assignments_manage');    
}

// create nav in admin panel
add_action('admin_menu', 'spcourseware_admin_menu');

function spcourseware_admin_scripts() 
{
    if($_GET['page'] == 'schedule') {
    $url = WP_PLUGIN_URL;
	$datepicker_url = $url . '/scholarpress-courseware/core/includes/datepicker/';
	echo '<script src="'.$datepicker_url.'jquery.ui.all.js" type="text/javascript" charset="utf-8"></script>';
	echo '<link rel="stylesheet" type="text/css" href="'.$datepicker_url.'base.css" />';
	echo '<link rel="stylesheet" type="text/css" href="'.$datepicker_url.'datepicker.css" />';
    }
}

add_action('admin_head', 'spcourseware_admin_scripts');