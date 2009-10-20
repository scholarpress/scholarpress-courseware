<?php get_header(); ?>

<div id="content" class="narrowcolumn">
	<h2>Bibliography</h2>

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