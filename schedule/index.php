<?php

function spcourseware_schedule_manage() 
{
    $scheduleID = !empty($_REQUEST['schedule_id']) ? $_REQUEST['schedule_id'] : '';
    $updateAction = !empty($_REQUEST['update_action']) ? $_REQUEST['update_action'] : '';
    
    if (isset($_REQUEST['action']) ) {
        if ($_REQUEST['action'] == 'delete_schedule') {
            spcourseware_delete_schedule_entry($scheduleID);
        }
    }
    if ( $updateAction == 'update_schedule' ) {
        if ( empty($scheduleID) ) {
            echo '<div class="error"><p>'. __( '<strong>Failure:</strong> No schedule ID given.', SPCOURSEWARE_TD ) .'</p></div>';
        } else {
            spcourseware_update_schedule_entry($scheduleID);
        }
    } elseif ( $updateAction == 'add_schedule' ) {
        spcourseware_add_schedule_entry();
        echo '<div class="updated">'. __( 'Schedule entry added!', SPCOURSEWARE_TD ) .'</div>';
    }
?>

<div class="wrap">
    <h2><?php echo __( 'Schedule', SPCOURSEWARE_TD ); ?> | <?php echo __( 'ScholarPress Courseware', SPCOURSEWARE_TD ); ?></h2>
    <?php spcourseware_schedule_navigation(); ?>
    <?php if($_REQUEST['view'] == 'form'): ?>
    
    <?php if ( $_REQUEST['action'] == 'edit_schedule' ): ?>
        <?php if ( empty($scheduleID) ): ?>
        <div class="error"><p><?php echo __( 'No schedule entry specified.', SPCOURSEWARE_TD ); ?></p></div>
        <?php else: ?>
        <h3><?php echo __( 'Edit Schedule Entry #', SPCOURSEWARE_TD ) .$scheduleID; ?></h3>
        <?php spcourseware_schedule_edit_form('update_schedule', $scheduleID); ?>	
        <?php endif; ?>
    <?php else: ?>
        <h3><?php echo __( 'Add Schedule Entry', SPCOURSEWARE_TD ); ?></h3>
        <?php spcourseware_schedule_edit_form(); ?>
    <?php endif; ?>
    <?php else: ?>
        <h3><?php echo __( 'Schedule Entries', SPCOURSEWARE_TD ); ?></h3>
        <?php if($schedules = spcourseware_get_schedule_entries()): ?>
        <div class="clear"></div>
        <table class="widefat fixed" cellspacing="0">
        	<thead>
        	<tr>
            	<th scope="col" class="manage-column column-url"><?php echo __( 'Title', SPCOURSEWARE_TD ); ?></th>
            	<th scope="col" class="manage-column column-name"><?php echo __( 'Date', SPCOURSEWARE_TD ); ?></th>
            	<th scope="col"class="manage-column column-name"><?php echo __('Description', SPCOURSEWARE_TD ); ?></th>
        	</tr>
        	</thead>
        	<tfoot>
            	<tr>
            	<th scope="col" class="manage-column column-url"><?php echo __( 'Title', SPCOURSEWARE_TD ); ?></th>
            	<th scope="col" class="manage-column column-name"><?php echo __( 'Date', SPCOURSEWARE_TD ); ?></th>
            	<th scope="col"class="manage-column column-name"><?php echo __( 'Description', SPCOURSEWARE_TD ); ?></th>
            	</tr>
        	</tfoot>

        	<tbody>
        	<?php
        	$class = '';
    		foreach ( $schedules as $schedule ) :
    		$class = ($class == 'alternate') ? '' : 'alternate';
            ?>
            
                <tr valign="middle" class="<?php echo $class; ?>">
                    <th scope="row" class="column-name">
                    <strong><a class="row-title" href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=edit_schedule&amp;schedule_id=<?php echo $schedule->scheduleID;?>"><?php echo $schedule->schedule_title ?></a></strong> 
                        <br />
                        <div class="row-actions">
                            <span class='edit'><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=edit_schedule&amp;schedule_id=<?php echo $schedule->scheduleID;?>" class="edit"><?php echo __('Edit', SPCOURSEWARE_TD); ?></a> | </span>
                            <span class='delete'><a class="submitdelete" href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_schedule&amp;schedule_id=<?php echo $schedule->scheduleID;?>" onclick="if ( confirm('<?php echo __( "You are about to delete this link 'Development Blog'\n  'Cancel' to stop, 'OK' to delete.", SPCOURSEWARE_TD ); ?>') ) { return true;}return false;"><?php echo __( 'Delete', SPCOURSEWARE_TD ); ?></a></span>
                        </div>
                    </th>
                    <td><?php echo $schedule->schedule_date; ?></td>
                    <td><?php echo $schedule->schedule_description; ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            </table>        
            <?php endif; ?>
            <?php endif; ?>
            <br class="clear" />
</div>
<?php
}
?>