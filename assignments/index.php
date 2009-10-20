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
	<h2>Assignments | ScholarPress Courseware</h2>
    <?php spcourseware_assignments_navigation(); ?>
            <?php if($_REQUEST['view'] == 'form'): ?>

            <?php if ( $_REQUEST['action'] == 'update_assignment' ): ?>
                <?php if (empty($assignmentID) ): ?>
                <div class="error"><p>No assignment entry specified.</p></div>
                <?php else: ?>
                <?php spcourseware_assignment_edit_form('update_assignment', $assignmentID); ?>	
                <?php endif; ?>
            <?php else: ?>
                <?php spcourseware_assignment_edit_form(); ?>
            <?php endif; ?>
            <?php else: ?>

    		<h3><?php _e('Manage Assignments'); ?></h3>
            <?php 
            $assignments = spcourseware_get_assignment_entries();
            if($assignments): ?>
            <div class="clear"></div>
            <table class="widefat fixed" cellspacing="0">
            	<thead>
            	<tr>
                	<th scope="col" class="manage-column column-title">Title</th>
                	<th scope="col" class="manage-column column-date">Date</th>
                	<th scope="col" class="manage-column">Type</th>
                	<th scope="col"class="manage-column column-description">Description</th>
            	</tr>
            	</thead>
            	<tfoot>
                	<tr>
                    	<th scope="col" class="manage-column column-title">Title</th>
                    	<th scope="col" class="manage-column column-date">Date</th>
                    	<th scope="col" class="manage-column">Type</th>
                    	<th scope="col"class="manage-column column-description">Description</th>
                	</tr>
            	</tfoot>
            	<tbody>
            <?php foreach($assignments as $assignment): ?>
                <tr valign="center">
                    <th scope="row">
                        <strong><a class="row-title" href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=update_assignment&amp;assignment_id=<?php echo $assignment->assignmentID;?>"><?php echo $assignment->assignments_title; ?></a></strong>
                        <br />
                        <div class="row-actions">
                            <span class='edit'><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=update_assignment&amp;assignment_id=<?php echo $assignment->assignmentID;?>" class="edit"><?php echo __('Edit'); ?></a> | </span>
                            <span class='delete'><a class="submitdelete" href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_assignment&amp;assignment_id=<?php echo $assignment->assignmentID;?>" onclick="if ( confirm('You are about to delete this link \'Development Blog\'\n  \'Cancel\' to stop, \'OK\' to delete.') ) { return true;}return false;">Delete</a></span>
                        </div>
                    </th>
                    <td>
                        <?php echo $assignment->assignment ?>
                    </td>
                    <td>
                        <?php echo $assignment->assignments_type; ?>
                    </td>
                    <td>
                        <?php echo $assignment->assignments_description; ?>
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