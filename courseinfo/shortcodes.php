<?php 
require_once('helpers.php');

add_shortcode('spcourseinfo', 'spcourseware_courseinfo_shortcode');

function spcourseware_courseinfo_shortcode($atts, $content=null, $code="")
{
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