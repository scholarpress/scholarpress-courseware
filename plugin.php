<?php 
/*
Plugin Name: ScholarPress Courseware
Plugin URI: http://scholarpress.net/courseware/
Description: All-in-one course management for WordPress
Version: 2.0
Author: Jeremy Boggs, Dave Lester, Zac Gordon, and Sean Takats
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
define(SPCOURSEWARE_VERSION_NUMBER, '1.2');

// define global variable for the plugins path
define(SPCOURSEWARE_PLUGIN_PATH, ABSPATH . PLUGINDIR . DIRECTORY_SEPARATOR . 'scholarpress-courseware/');

// i18n
function spcourseware_textdomain() {
	define( SPCOURSEWARE_TD, 'spcourseware' );
    load_plugin_textdomain( SPCOURSEWARE_TD, false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
}
add_action('init', 'spcourseware_textdomain');

// include the core's magical loader
require_once 'core/loader.php';
// and... that's a wrap!

?>