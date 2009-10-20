<?php 
require_once('helpers.php');

add_shortcode('spschedule', 'spcourseware_schedule_shortcode');

function spcourseware_schedule_shortcode($atts, $content=null, $code="")
{
    extract(
        shortcode_atts( 
            array(
                'date' => null
            ), 
            $atts
        )
    );
    
    if($date != null) {
        $entry = spcourseware_get_schedule_by_date($date);
        return spcourseware_schedule_short($entry->id);
    }
    
    return spcourseware_courseinfo_printfull();
}