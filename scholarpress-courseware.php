<?php 
/*
Plugin Name: ScholarPress Courseware
Plugin URI: http://scholarpress.net/courseware/
Description: All-in-one course management for WordPress
Version: 2.0
Author: Jeremy Boggs, Stas Sușkov
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

// define ScholarPress Courseware Version #
if (!defined('SPCOURSEWARE_VERSION_NUMBER' ))
	define('SPCOURSEWARE_VERSION_NUMBER', '2.0');

if ( !defined( 'SPCOURSEWARE_TD' ) )
    define('SPCOURSEWARE_TD', 'spcourseware');

if ( !class_exists( 'Scholarpress_Courseware_Loader' ) ) :

class Scholarpress_Courseware_Loader {

    /**
     * ScholarPress Courseware Loader
     *
     * @since 2.0
     * @uses add_action()
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     */
	function scholarpress_courseware_loader() {
		add_action( 'init', array ( $this, 'init' ) );
		add_action( 'admin_init', array ( $this, 'admin_init' ) );
		add_action( 'plugins_loaded', array( $this, 'loaded' ) );
		
		// Admin menu stuff
		add_action('admin_menu', array( $this, 'admin_menu' ));
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order_function' ) );
		add_filter( 'menu_order', array( $this, 'menu_order_my_function' ) );
		
		// Activation sequence
		register_activation_hook( __FILE__, array( $this, 'activation' ) );

		// Deactivation sequence
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		
		// When Courseware is loaded, get includes.
		add_action( 'scholarpress_courseware_loaded', array( $this, 'includes' ) );
		
		// When Courseware is initialized, add localization files.
		add_action( 'scholarpress_courseware_init', array( $this, 'textdomain' ) );
		
		// When Courseware is loaded, add our custom post types.
		add_action( 'scholarpress_courseware_init', array( $this, 'register_post_types') );
	}

    /**
     * Adds a plugin initialization action.
     */
	function init() {
		do_action( 'scholarpress_courseware_init' );
	}
	
	/**
     * Adds a plugin admin initialization action.
     */
	function admin_init() {
	    do_action( 'scholarpress_courseware_admin_init');
	}
	
	/**
     * Adds a plugin loaded action.
     */
	function loaded() {
		do_action( 'scholarpress_courseware_loaded' );
	}
    
    /**
     * Activation script. Runs when the plugin is activated. Checks if the WP 
     * install is using multisite, and if the activation is networkwide.
     *
     * @uses switch_to_blog()
     * @uses is_multisite()
     */
    function activation() {
        global $wpdb;
        // If we've got a multisite instance of WP
    	if (function_exists('is_multisite') && is_multisite()) {
    		// check if it is a network activation - if so, run needed activation scripts for each blog id
    		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
    	        $old_blog = $wpdb->blogid;
    			// Get all blog ids
    			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
    			foreach ($blogids as $blog_id) {
    			    // Switch to the next blog, and install our plugin tables
    				switch_to_blog($blog_id);
    				$this->register_post_types();
    			}
    			switch_to_blog($old_blog);
    			return;
    		}	
    	} 
    	$this->register_post_types();
    }
    
    /**
     * Deactivation script. Runs when plugin is deactivated.
     */
    function deactivation() {}
    
    /**
     * Upgrade script. Runs when plugin needs upgrading.
     */
    function upgrade() {} 
    
    /**
     * Includes other necessary plugin files.
     */
	function includes() {
	    require(dirname( __FILE__ ).'/scholarpress-courseware-helpers.php');
        require(dirname( __FILE__ ).'/modules/courseinfo.php');
        require(dirname( __FILE__ ).'/modules/assignments.php');
        require(dirname( __FILE__ ).'/modules/bibliography.php');
        require(dirname( __FILE__ ).'/modules/schedule.php');
	}

    /**
     * Handles localization files. Added on scholarpress_courseware_init. 
     * Plugin core localization files are in the 'languages' directory. Users
     * can also add custom localization files in 
     * 'wp-content/scholarpress-courseware-files/languages' if desired.
     *
     * @uses load_textdomain()
     * @uses get_locale()
     */
	function textdomain() {
		$locale = get_locale();

		// First look in wp-content/wordhub-files/languages, where custom language files will not be overwritten by Courseware upgrades. Then check the packaged language file directory.
		$mofile_custom = WP_CONTENT_DIR . "/scholarpress-courseware-files/languages/spcourseware-$locale.mo";
		$mofile_packaged = WP_PLUGIN_DIR . "/scholarpress-courseware/languages/spcourseware-$locale.mo";

    	if ( file_exists( $mofile_custom ) ) {
      		load_textdomain( 'spcourseware', $mofile_custom );
      		return;
      	} else if ( file_exists( $mofile_packaged ) ) {
      		load_textdomain( 'spcourseware', $mofile_packaged );
      		return;
      	}
	}
    
    /**
     * Installs plugin tables for newly created blogs on WP network.
     *
     * @param int The new blog ID.
     */
    function new_blog($newBlogId) {
    	global $wpdb;
       // Makes sure the plugin is defined before adding tables.
    	if (is_plugin_active_for_network('scholarpress-courseware/plugin.php')) {
    		$oldBlogId = $wpdb->blogid;
    		switch_to_blog($newBlogId);
    		$this->_create_tables();
    		switch_to_blog($oldBlogId);
	    }
    }
    
    /**
     * Creates plugin tables: spcourseware_assignments, spcourseware_schedule,
     * and spcourseware_bibliography.
     */
    function _create_tables() {
        global $wpdb, $user_level;

        // table names
        $assignments_table_name = $wpdb->prefix . "spcourseware_assignments";
        $bib_table_name = $wpdb->prefix . "spcourseware_bibliography";
        $schedule_table_name = $wpdb->prefix . "spcourseware_schedule";

        // First-Run-Only parameters: Check if assignments table exists:
        if($wpdb->get_var("SHOW TABLES LIKE '$assignments_table_name'") != $assignments_table_name) 
        {
            // It doesn't exist, create the table
            $sql = "CREATE TABLE " . $assignments_table_name . " (
                 `id` INT(11) NOT NULL AUTO_INCREMENT,
                 `title` TEXT NOT NULL,
                 `schedule_id` INT NOT NULL,
                 `bibliography_id` INT NOT NULL,
                 `assigned_schedule_id` INT NOT NULL,
                 `pages` VARCHAR(255) NOT NULL,
                 `description` TEXT NOT NULL,
                 `type` ENUM('reading','writing','presentation','groupwork','research','discussion', 'creative') NOT NULL,
                 PRIMARY KEY (`id`)
                 )"; 

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

        }

        // First-Run-Only parameters: Check if bibliography table exists:
        if($wpdb->get_var("SHOW TABLES LIKE '$bib_table_name'") != $bib_table_name) 
        {
             // It doesn't exist, create the table
            $sql = "CREATE TABLE " . $bib_table_name . " (
                 `id` INT(11) NOT NULL AUTO_INCREMENT,
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
                 PRIMARY KEY (`id`)
                 )"; 

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        // First-Run-Only parameters: Check if schedule table exists:
        if($wpdb->get_var("SHOW TABLES LIKE '$schedule_table_name'") != $schedule_table_name) 
        {
            // It doesn't exist, create the table
            $sql = "CREATE TABLE " . $schedule_table_name . " (
                 `id` INT(11) NOT NULL AUTO_INCREMENT,
                 `title` tinytext NOT NULL,
                 `date` DATE NOT NULL,
                 `timestart` TIME NOT NULL,
                 `timestop` TIME NOT NULL,
                 `description` TEXT NOT NULL,
                 PRIMARY KEY (`id`)
                 )";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    /**
     * Creates post types for schedule, bibliography, and assignments.
     */
    function register_post_types() {
        
        $assignment_post_def = array(
            'label'                 => __( 'Assignments', 'spcourseware' ),
            'singular_label'        => __( 'Assignment', 'spcourseware' ),
            'description'           => __( 'ScholarPress Courseware Assignments', 'spcourseware' ),
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => true, // need to hide this.
            'capability_type'       => 'sp_assignment',
            'hierarchical'          => true,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' )
        );
        
        register_post_type( 'sp_assignment', $assignment_post_def );
        
         $bibliography_labels = array(
			'name' => _x('Bibliography', 'bibliography general name'),
			'singular_name' => _x('Bibliography Entry', 'single bibliography entry'),
			'add_new' => _x('Add New', 'book'),
			'add_new_item' => __('Add New Entry'),
			'edit_item' => __('Edit Entry'),
			'new_item' => __('New Entry'),
			'view_item' => __('View Entry'),
			'search_items' => __('Search Bibliography'),
			'not_found' =>  __('No entries found'),
			'not_found_in_trash' => __('No entries found in Trash'),
			'parent_item_colon' => ''
		  );
		
		$bibliography_post_def = array(
			'label'                 => __( 'Bibliography', SPCOURSEWARE_TD ),
			'labels'                => $bibliography_labels,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'_builtin'              => false,
			'show_ui'               => true, // todo: hide
			'capability_type'       => 'bibliography',
			'hierarchical'          => true,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' ),
			'rewrite'               => array("slug" => "part") // Permalinks format
		);
		
		register_post_type( 'sp_bibliography', $bibliography_post_def );
		
		 $schedule_labels = array(
			'name' => _x('Schedule', 'schedule general name'),
			'singular_name' => _x('Schedule Entry', 'single schedule entry'),
			'add_new' => _x('Add New', 'schedule'),
			'add_new_item' => __('Add New Schedule Entry'),
			'edit_item' => __('Edit Schedule Entry'),
			'new_item' => __('New Schedule Entry'),
			'view_item' => __('View Schedule Entry'),
			'search_items' => __('Search Schedule'),
			'not_found' =>  __('No entries found'),
			'not_found_in_trash' => __('No entries found in Trash'),
			'parent_item_colon' => ''
		  );
		
		$schedule_post_def = array(
			'label'                 => __( 'Schedule', SPCOURSEWARE_TD ),
			'labels'                => $schedule_labels,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'_builtin'              => false,
			'show_ui'               => true, // todo: hide
			'capability_type'       => 'schedule',
			'hierarchical'          => true,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' ),
			'rewrite'               => array("slug" => "schedule") // Permalinks format
	    );
		
		register_post_type( 'sp_schedule', $schedule_post_def );
    }

    /**
     * The next two functions are hacks to make WordPress hide the menu items. 
     * Borrowed from Anthologize...thanks Boone!
     */
	function custom_menu_order_function(){
		return true;
	}

	function menu_order_my_function( $menu_order ){
		global $menu;
		foreach ( $menu as $mkey => $m ) {
			$key1 = array_search( 'edit.php?post_type=sp_assignment', $m, true );
			$key2 = array_search( 'edit.php?post_type=sp_bibliography', $m, true );
            $key3 = array_search( 'edit.php?post_type=sp_schedule', $m, true );
			if ( $key1 || $key2 || $key3 ) unset( $menu[$mkey] );
		}
		return $menu_order;
	}
	
    function admin_menu() {
        if ( function_exists( 'add_menu_page' ) ) {
            $dashboard_title = __( "Dashboard", SPCOURSEWARE_TD ) . ' | ' . __( "ScholarPress Courseware", SPCOURSEWARE_TD );
            $spcoursewarepage = add_menu_page( __( "Dashboard", SPCOURSEWARE_TD ), __( "Courseware", SPCOURSEWARE_TD ) , 'manage_options', 'scholarpress-courseware', array($this, 'admin_display'));
            add_submenu_page('scholarpress-courseware', $dashboard_title, __( "Dashboard", SPCOURSEWARE_TD ), 'manage_options', 'scholarpress-courseware', array($this, 'admin_display'));
		}
    }

    function admin_display() {
    ?>
    <div class="wrap">
    <h2><?php _e( 'Dashboard', SPCOURSEWARE_TD ); ?> | <?php _e( 'ScholarPress Courseware', SPCOURSEWARE_TD ); ?></h2>
    <br class="clear" />
    </div>
    <?php
    }
}

endif; // class exists

$scholarpress_courseware_loader = new Scholarpress_Courseware_Loader();