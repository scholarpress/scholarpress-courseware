<?php get_header(); ?>

<div id="content" class="narrowcolumn">
	<h2><?php echo __( 'Course Information', SPCOURSEWARE_TD ); ?></h2>

    <?php spcourseware_courseinfo_printfull(); ?>

</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>