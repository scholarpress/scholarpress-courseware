<?php

add_filter('query_vars', 'spcourseware_queryvars' );

function spcourseware_queryvars( $qvars )
{
  $qvars[] = 'courseware';
  return $qvars;
}

add_filter('template_redirect','spcourseware_templates');

function spcourseware_templates() {
 global $wp_query;
 $courseware = $wp_query->query_vars['courseware']; 
 if(!empty($courseware)) {
     include(SPCOURSEWARE_PLUGIN_PATH.$courseware.'/template.php');
     exit;
 }
}

add_action('init', 'spcourseware_flush_rewrite_rules');

function spcourseware_flush_rewrite_rules() 
{
   global $wp_rewrite;
   $wp_rewrite->flush_rules();
}

add_action('generate_rewrite_rules', 'spcourseware_add_rewrite_rules');

function spcourseware_add_rewrite_rules( $wp_rewrite ) 
{
  $new_rules = array( 
     'bibliography' => 'index.php?courseware=bibliography',
     'schedule' => 'index.php?courseware=schedule',
     'courseinfo' => 'index.php?courseware=courseinfo'
     
     );

  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}