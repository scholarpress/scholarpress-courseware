<?php

if ( !class_exists( 'Scholarpress_Courseware_Courseinfo' ) ) :

class Scholarpress_Courseware_Courseinfo {
    
    function scholarpress_courseware_courseinfo() {
        add_shortcode( 'spcourseinfo', array($this, 'shortcode') );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

    function shortcode($atts, $content=null, $code="") {
        extract(
            shortcode_atts( 
                array(
                    'field' => 'All'
                ), 
                $atts
            )
        );

        if($field != 'All') {
            return spcourseware_courseinfo_get_field($field);
        }

        return spcourseware_courseinfo_printfull();
    }
	
    function admin_menu() {
    	if ( function_exists( 'add_submenu_page' ) ) {
    	    $courseinfo_title = __( "Course Information", SPCOURSEWARE_TD ) . ' | ' . __( "ScholarPress Courseware", SPCOURSEWARE_TD );
            add_submenu_page('scholarpress-courseware', $courseinfo_title, __( "Course Information", SPCOURSEWARE_TD ), 'manage_options', 'scholarpress-courseware-courseinfo', array($this, 'admin_display'));
    	}
    }
    
    /**
     * Save course information
     */
    function save($courseInfo = array()) {
        // Makes sure course_timestart and course_timeend are times
        if (!empty($courseInfo['course_timestart'])) {
            $courseInfo['course_timeend'] = date('H:i:s', strtotime($courseInfo['course_timeend']));
        }

        if (!empty($courseInfo['course_timeend'])) {
            $courseInfo['course_timeend'] = date('H:i:s', strtotime($courseInfo['course_timeend']));
        }

        if (get_option('SpCoursewareCourseInfo') ) {
    	    update_option('SpCoursewareCourseInfo', $courseInfo);
        } else {
            $deprecated=' ';
            $autoload='no';
            add_option('SpCoursewareCourseInfo', $courseInfo, $deprecated, $autoload);
        }
        return '<div class="updated fade"><p>'. __( 'Course information saved successfully.', SPCOURSEWARE_TD ) .'</p></div>';
    }
    
    // Display admin form
    function admin_display() {
    	if ($_POST['save']) {
            unset($_POST['save'] );
    		$results = $this->save($_POST);
    		echo $results;
    	}

        $courseInfo = spcourseware_courseinfo_get_fields();
        $courseTimeStart = !empty($courseInfo['course_timestart']) ? date('g:ia', strtotime($courseInfo['course_timestart'])) : null;
        $courseTimeEnd = !empty($courseInfo['course_timeend']) ? date('g:ia', strtotime($courseInfo['course_timeend'])) : null;
        ?>
        <div class="wrap">
        <h2><?php echo __( 'Course Information', SPCOURSEWARE_TD ); ?> | <?php echo __( 'ScholarPress Courseware', SPCOURSEWARE_TD ); ?></h2>
        <br class="clear" />
        <form method="post">
            <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="course_title"><?php echo __( 'Course Title', SPCOURSEWARE_TD ); ?></label>
                </th>
                <td>
                    <input name="course_title" type="text" id="course_title" value="<?php echo $courseInfo['course_title']; ?>" class="regular-text" />
                    <p class="description"><?php echo __( 'The title of your course', SPCOURSEWARE_TD ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="course_number"><?php echo __( 'Course Number', SPCOURSEWARE_TD ); ?></label>
                </th>
                <td>
                    <input name="course_number" type="text" id="course_number" value="<?php echo $courseInfo['course_number']; ?>" class="regular-text" />
                    <p class="description"><?php echo __( "The registrar's number for your course (e.g. HIST 100)", SPCOURSEWARE_TD ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="course_section"><?php echo __( 'Course Section', SPCOURSEWARE_TD ); ?></label>
                </th>
                <td>
                    <input name="course_section" type="text" id="course_section" value="<?php echo $courseInfo['course_section']; ?>" class="regular-text" />
                    <p class="description"><?php echo __( "The registrar's section number for your course (e.g. 001)", SPCOURSEWARE_TD ); ?></p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="course_days"><?php echo __( 'Course Days', SPCOURSEWARE_TD ); ?></label></th>
                <td><input type="text" name="course_days" class="regular-text" value="<?php echo $courseInfo['course_days']; ?>" />
                    <p class="description"><?php echo __( 'The days your course regularly meets', SPCOURSEWARE_TD ); ?></p></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="course_timestart"><?php echo __( 'Course Time Start', SPCOURSEWARE_TD ); ?></label></th>
    		    <td><input type="text" name="course_timestart" class="regular-text" value="<?php echo $courseTimeStart; ?>" />
    		        <p class="description"><?php echo __('The time your course regularly starts (e.g. 1:00PM)', SPCOURSEWARE_TD ); ?></p></td>
            </tr>
             <tr valign="top">  
    		     <th scope="row"><label for="course_timeend"><?php echo __( 'Course Time End', SPCOURSEWARE_TD ); ?></label></th>
    		     <td><input type="text" name="course_timeend" class="regular-text" value="<?php echo $courseTimeEnd; ?>" />
     		        <p class="description"><?php echo __( 'The time your course regularly ends (e.g. 2:00PM)', SPCOURSEWARE_TD ); ?></p></td>
    		</tr>
            <tr valign="top">  
    		<th scope="row"><label for="course_location"><?php echo __( 'Course Location', SPCOURSEWARE_TD ); ?></label></th>
    		<td><input type="text" name="course_location" class="regular-text" value="<?php echo $courseInfo['course_location']; ?>" />
    	        <p class="description"><?php echo __( 'The place your course regularly meets (e.g. Building and room number)', SPCOURSEWARE_TD ); ?></p></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="course_description"><?php echo __( 'Course Description', SPCOURSEWARE_TD ); ?></label>
                </th>
                <td>
                    <textarea name="course_description"id="course_description" value="" rows="10" style="width:70%; min-width: 300px;"><?php echo $courseInfo['course_description']; ?></textarea>
                    <p class="description"><?php echo __( 'The description for your course.', SPCOURSEWARE_TD ); ?></p>
                </td>
            </tr>

            </table>
            <p class="submit">
            <input type="submit" name="save" class="button-primary" value="<?php echo __( 'Save Changes', SPCOURSEWARE_TD ); ?>" />
            </p>

        </form>
        </div>
        <br class="clear" />
        <?php
    }
    
}

endif; // class_exists

$scholarpress_courseware_courseinfo = new Scholarpress_Courseware_Courseinfo();