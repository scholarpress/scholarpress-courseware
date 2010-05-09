<?php get_header(); ?>

<div id="content" class="narrowcolumn">
	<h2><?php echo __( 'Bibliography', SPCOURSEWARE_TD ); ?></h2>

    <?php 
    
    $entries = spcourseware_get_bibliography_entries();
    if($entries) {
        foreach($entries as $entry) {
            spcourseware_bibliography_full($entry, true, true);
        }
    }    
    ?>

</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>