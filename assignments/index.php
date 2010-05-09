<?php

// Add management pages to the administration panel; sink function for 'admin_menu' hook

function spcourseware_assignments_manage() {
    
    $updateaction = !empty($_REQUEST['update_action']) ? $_REQUEST['update_action'] : '';
	$assignmentID = !empty($_REQUEST['assignment_id']) ? $_REQUEST['assignment_id'] : '';
	
	// Delete an assignment
    if (@$_REQUEST['action'] == 'delete_assignment' && !empty($assignmentID)) {
        spcourseware_delete_assignment_entry($assignmentID);
    }
    
    // Adding and updating assignments
    if ( $updateaction == 'update_assignment' && !empty($assignmentID)) {
        spcourseware_update_assignment_entry($assignmentID);
    } elseif($updateaction == 'add_assignment') {
        spcourseware_add_assignment_entry();
    }
	?>
	<div class="wrap">
	<h2><?php echo __( 'Assignments', SPCOURSEWARE_TD ); ?> | <?php echo __( 'ScholarPress Courseware', SPCOURSEWARE_TD ); ?></h2>
    <?php spcourseware_assignments_navigation(); ?>
            <?php if($_REQUEST['view'] == 'form'): ?>

            <?php if ( $_REQUEST['action'] == 'update_assignment' ): ?>
                <?php if (empty($assignmentID) ): ?>
                <div class="error"><p><?php echo __( 'No assignment entry specified.', SPCOURSEWARE_TD ); ?></p></div>
                <?php else: ?>
                <?php spcourseware_assignment_edit_form('update_assignment', $assignmentID); ?>	
                <?php endif; ?>
            <?php else: ?>
                <?php spcourseware_assignment_edit_form(); ?>
            <?php endif; ?>
            <?php else: ?>

    		<h3><?php echo __( 'Manage Assignments', SPCOURSEWARE_TD ); ?></h3>
            <?php 
            $assignments = spcourseware_get_assignment_entries();
            if($assignments): ?>
            <div class="clear"></div>
            <table class="widefat fixed" cellspacing="0">
            	<thead>
            	<tr>
                	<th scope="col" class="manage-column column-title"><?php echo __( 'Title', SPCOURSEWARE_TD ); ?></th>
                	<th scope="col"class="manage-column column-description"><?php echo __( 'Description', SPCOURSEWARE_TD ); ?></th>
                	
                	<th scope="col" class="manage-column column-date"><?php echo __( 'Date', SPCOURSEWARE_TD ); ?></th>
            	</tr>
            	</thead>
            	<tfoot>
                	<tr>
                    	<th scope="col" class="manage-column column-title"><?php echo __( 'Title', SPCOURSEWARE_TD ); ?></th>
                    	<th scope="col"class="manage-column column-description"><?php echo __( 'Description', SPCOURSEWARE_TD ); ?></th>
                    	
                    	<th scope="col" class="manage-column column-date"><?php echo __( 'Date', SPCOURSEWARE_TD ); ?></th>
                	</tr>
            	</tfoot>
            	<tbody>
            <?php foreach($assignments as $assignment): ?>
                <tr valign="center">
                    <th scope="row">
                        <?php
                        if($assignment->assignments_type == 'reading') {
                            $bibEntry = spcourseware_get_bibliography_entry_by_id($assignment->assignments_bibliographyID);
                            $title = $bibEntry->title;
                        } else {
                            $title = $assignment->assignments_title;
                        }
                        
                        ?>
                        <strong><?php echo ucwords($assignment->assignments_type); ?>: 
                        <a class="row-title" href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=update_assignment&amp;assignment_id=<?php echo $assignment->assignmentID;?>"><?php echo $title; ?></a></strong>
                        <br />
                        <div class="row-actions">
                            <span class='edit'><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=update_assignment&amp;assignment_id=<?php echo $assignment->assignmentID;?>" class="edit"><?php echo __('Edit', SPCOURSEWARE_TD ); ?></a> | </span>
                            <span class='delete'><a class="submitdelete" href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_assignment&amp;assignment_id=<?php echo $assignment->assignmentID;?>" onclick="if ( confirm(<?php echo __('You are about to delete this link "Development Blog"\n  "Cancel" to stop, "OK" to delete.', SPCOURSEWARE_TD ); ?>) ) { return true;}return false;"><?php echo __( 'Delete', SPCOURSEWARE_TD ); ?></a></span>
                        </div>
                    </th>
                    <td>
                        <?php echo $assignment->assignments_description; ?>
                    </td>
                    <td>                        
                    <?php $schedule = spcourseware_get_schedule_entry_by_id($assignment->assignments_scheduleID); $scheduleDate = strtotime($schedule->schedule_date); echo date('F j, Y', $scheduleDate); ?>
                    </td>

                </tr>
            <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
    	<?php endif; ?>
    	</div>
<?php
}
?>