<?php 
require_once('helpers.php');

add_shortcode('spbibliography', 'spcourseware_bibliography_shortcode');

function spcourseware_bibliography_shortcode($atts, $content=null, $code="")
{
    extract(
        shortcode_atts( 
            array(
                'id' => null
            ), 
            $atts
        )
    );
    
    if($id != null) {
        $entry = spcourseware_get_bibliography_entry_by_id($id);
        return spcourseware_bibliography_full($entry);
    }
    $html .= '';
    $entries =  spcourseware_get_bibliography_entries();
    foreach($entries as $entry) {
        $html .= spcourseware_bibliography_full($entry);
    }
    return $html;
}