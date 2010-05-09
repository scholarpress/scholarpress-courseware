<?php get_header(); ?>

<div id="content" class="narrowcolumn">
	<h2><?php echo __('Schedule', SPCOURSEWARE_TD ); ?></h2>
    <?php 
    $entries = spcourseware_get_schedule_entries(); 
    
    if($entries):?>
    
    <p><a href="webcal://feeds.technorati.com/events/http://<?php echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>"><?php echo __( 'Subscribe to vCal file', SPCOURSEWARE_TD ); ?></a></p>
    <div id="classes" class="vcalendar">
    <?php
    foreach($entries as $entry):
    
    $startTime = strtotime($entry->schedule_date.' '.$entry->schedule_timestart);
	$endTime = strtotime($entry->schedule_date.' '.$entry->schedule_timestop);
	
    ?>
    
    <div class="vevent">
		
		<div class="date">
			<strong><span class="dtstart"><span class="value-title" title="<?php echo date('Y-m-d', $startTime); ?>"><?php echo date('F d, Y', $startTime); ?></span>, <span class="value"><?php echo date('g:i a', $startTime); ?></span></span>&ndash;<span class="dtend"><span class="value-title" title="<?php echo date('Y-m-d', $endTime); ?>"></span><span class="value"><?php echo date('g:i a', $endTime); ?></span>
			</span></strong>
		</div>
		<h3 class="summary"><?php echo $entry->schedule_title; ?></h3>
        
		<div class="description"> 
		<?php if ($entry->schedule_description): ?>
		<p><?php echo $entry->schedule_description; ?></p>
		<?php endif; ?>
		
		<?php
		$assignmentTypes = spcourseware_get_assignment_types();
		
		foreach($assignmentTypes as $assignmentType):
		    $assignments = spcourseware_get_assignment_entries($entry->scheduleID,$assignmentType);
		    if($assignments): ?>
		    <h4><?php echo ucwords($assignmentType); ?></h4>
		    <ul class="assignments">
		        <?php foreach($assignments as $assignment):?>
		            <li>
		            <?php if($assignmentType == 'reading' && $biblioId = $assignment->assignments_bibliographyID): ?>
		                <?php $reading = spcourseware_get_bibliography_entry_by_id($biblioId); ?>
		                    <p><?php spcourseware_bibliography_short($reading, 'span'); ?><?php if($pages = $assignment->assignments_pages): ?> <?php echo $pages; endif; ?></p>
		            <?php else: ?>
		            <strong><?php echo $assignment->assignments_title; ?></strong>
		            <p><?php echo $assignment->assignments_description; ?></p>
		            <?php endif;?>
		            </li>
		        <?php endforeach; ?>
		    </ul>
		    <?php endif; ?>
		<?php endforeach; ?>
		</div>
    </div>
    <?php endforeach;; ?>
</div>
<?php else: ?>
    <p><?php echo __( 'No schedule entries.', SPCOURSEWARE_TD ); ?></p>
<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>