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
    $html = '';
    
    if($id != null) {
        $entry = spcourseware_get_bibliography_entry_by_id($id);
        $html = spcourseware_bibliography_full($entry);
    } else {
        $entries =  spcourseware_get_bibliography_entries();
        if($entries) {
            foreach($entries as $entry) {
                $html .= spcourseware_bibliography_full($entry);
            }
        } else {
            $html .= '<p>'. __( 'You have no bibliography entries!', SPCOURSEWARE_TD ) .'</p>';
        }
    }
    return $html;
}