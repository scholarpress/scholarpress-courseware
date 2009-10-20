<?php

/**
 * Returns all the fields for course information as an array.
 * 
 * @since 1.2
 * @return array
 **/
function spcourseware_courseinfo_get_fields()
{
    $spcoursewareOptions = get_option('SpCoursewareCourseInfo');
	if (!empty($spcoursewareOptions)) {
		foreach ($spcoursewareOptions as $key => $value)
			$spcoursewareAdminOptions[$key] = $value;
		}
	return $spcoursewareAdminOptions;
}

/**
 * Returns the value of a specific field in the courseinfo array.
 *
 * @since 1.0
 * @uses spcourseware_courseinfo_get_fields()
 * @return string|null
 **/
function spcourseware_courseinfo_get_field($fieldName)
{
    $fieldName = str_replace(' ','_',strtolower($fieldName));
    $fields = spcourseware_courseinfo_get_fields();
    return $fields[$fieldName];
}

/**
 * Returns an HTML-formatted string for course information
 *
 * @since 1.0
 * @uses spcourseware_courseinfo_get_fields()
 * @return string
 **/
function spcourseware_courseinfo_printfull()
{
    $courseinfo = spcourseware_courseinfo_get_fields();
    $course_title = $courseinfo['course_title'];
    $course_description = $courseinfo['course_description'];
    $course_location = $courseinfo['course_location'];
    $course_number = $courseinfo['course_number'];
    $course_section = $courseinfo['course_section'];
    $course_time_start = $courseinfo['course_time_start'];
    $course_time_end = $courseinfo['course_time_end'];
    $course_days = $courseinfo['course_days'];
    $instructor_first_name = $courseinfo['instructor_first_name'];
    $instructor_last_name = $courseinfo['instructor_last_name'];
    $instructor_telephone = $courseinfo['instructor_telephone'];
    $instructor_office = $courseinfo['instructor_office'];
    $instructor_hours = $courseinfo['instructor_hours'];
    $instructor_email = $courseinfo['instructor_email'];
    
    // String to time that junk.
    $starttime = strtotime($course_time_start);
    $endtime = strtotime($course_time_end);

?>
<div class="courseinfo">
    <p>
    <?php if(!empty($course_number)): ?>
        <span class="course-number"><?php echo $course_number; ?></span>
    <?php endif; ?>
    <?php if(!empty($course_title)): ?>
        : <span class="course-title"><?php echo $course_title; ?></span>
    <?php endif; ?>
    </p>
    <?php if(!empty($course_location)): ?>
        <p class="location"><?php echo $course_location; ?></p>
    <?php endif; ?>

    <p class="timedays">
        <?php if(!empty($course_days)): ?>
            <span class="days"><?php echo $course_days; ?></span>
        <?php endif; ?>
        <?php if(!empty($starttime)): ?>
            <?php echo date('g:i',$starttime); ?><?php if(!empty($endtime)): ?>&ndash;<?php echo date('g:i',$endtime); ?>
        <?php endif; ?>
        <?php endif; ?>
    </p>
    <ul class="vcard instructor">
        <li><span class="fn n"><span class="given-name"><?php echo $instructor_first_name; ?></span> <span class="family-name"><?php echo $instructor_last_name; ?></span></span></li>
        <li><span class="office"><?php echo $instructor_office; ?></span></li>
        <li><a href="mailto:<?php echo $instructor_email; ?>" class="email"><?php echo $instructor_email; ?></a></li>
        <li><span class="tel"><?php echo $instructor_telephone; ?></span></li>
    </ul>
</div>

<?php
}

/**
 * Sets the values for course information.
 * 
 * @since 1.2
 * @uses get_option()
 * @uses set_option()
 * @param string $courseTitle
 * @param string $courseNumber
 * @param string $courseSection
 * @param string $courseTimeStart
 * @param string $courseTimeEnd
 * @param string $courseLocation
 * @param string $courseDays
 * @param string $courseDescription
 * @param string $instructorFirstName
 * @param string $instructorLastName
 * @param string $instructorEmail
 * @param string $instructorTelephone
 * @param string $instructorOffice
 * @param string $instructorHours
 * @param array $options
 * @param Item|null Check for this specific item record (current item if null).
 * @return string|array|null
 **/
function spcourseware_courseinfo_set_fields($courseTitle, $courseNumber, $courseSection, $courseTimeStart, $courseTimeEnd, $courseLocation, $courseDays, $courseDescription, $instructorFirstName, $instructorLastName, $instructorEmail, $instructorTelephone, $instructorOffice, $instructorHours) 
{
		$spcoursewareCourseInfo = array(
		    'course_title' => $courseTitle,
		    'course_number' => $courseNumber,
		    'course_section' => $courseSection,
		    'course_time_start' => $courseTimeStart,
		    'course_time_end' => $courseTimeEnd,
		    'course_location' => $courseLocation,
		    'course_days' => $courseDays,
		    'instructor_first_name' => $instructorFirstName,
		    'instructor_last_name' => $instructorLastName,
		    'instructor_email' => $instructorEmail,
		    'instructor_telephone' => $instructorTelephone,
		    'instructor_office' => $instructorOffice,
		    'course_description' => $courseDescription,
		    'instructor_hours' => $instructorHours
		);
    
    // $spcoursewareCourseInfoOldOptionsName = 'SpCoursewareAdminOptions';
    
    $spcoursewareCourseInfoOptionsName = 'SpCoursewareCourseInfo';
    
    // if($oldOptions = get_option($spcoursewareCourseInfoOldOptionsName)) {
    //     add_option($spcoursewareCourseInfoOptionsName, $oldOptions);
    //     delete_option($spcoursewareCourseInfoOldOptionsName);
    // } else {
	    if (get_option($spcoursewareCourseInfoOptionsName) ) {
    	    update_option($spcoursewareCourseInfoOptionsName, $spcoursewareCourseInfo);
        } else {
            $deprecated=' ';
            $autoload='no';
            add_option($spcoursewareCourseInfoOptionsName, $spcoursewareCourseInfo, $deprecated, $autoload);
        }
    // }
}


?>