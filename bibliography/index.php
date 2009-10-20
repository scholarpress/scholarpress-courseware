<?php
require_once('helpers.php');

function spcourseware_bibliography_manage() {
    
    $id = !empty($_REQUEST['entry_id']) ? $_REQUEST['entry_id'] : '';
    $updateAction = !empty($_REQUEST['update_action']) ? $_REQUEST['update_action'] : '';
    
    if (isset($_REQUEST['action']) ) {
        if ($_REQUEST['action'] == 'delete_biblio') {
            spcourseware_delete_bibliography_entry($id);
        }
    }
    if ( $updateAction == 'update_biblio' ) {
        if ( empty($id) ) {
            echo '<div class="error"><p><strong>Failure:</strong> No schedule ID given.</p></div>';
        } else {
            spcourseware_update_bibliography_entry($id);
        }
    } elseif ( $updateAction == 'add_biblio' ) {
        spcourseware_add_bibliography_entry();
    }
    
    ?>
    <div class="wrap">
    <h2><?php _e('Bibliography | ScholarPress Courseware'); ?></h2>
    
    <?php spcourseware_bibliography_navigation(); ?>
    <?php if($_REQUEST['view'] == 'form'): ?>

    <?php if ( $_REQUEST['action'] == 'update_biblio' ): ?>
        <?php spcourseware_bibliography_edit_form('update_biblio', $id); ?>
    <?php else: ?>
        <?php spcourseware_bibliography_edit_form(); ?>
    <?php endif; ?>
    <?php else: ?>
    <h3>Bibliography</h3>
    <?php
    $entries = spcourseware_get_bibliography_entries();
    if($entries):
    ?>
    

    <table class="widefat fixed" cellspacing="0">
    	<thead>
    	<tr>
        	<th scope="col" id="title" class="manage-column column-title" style="">Title</th>
        	<th scope="col" id="author-name" class="manage-column column-author-name" style="">Author Name</th>
        	<th scope="col" id="bibligraphy-type" class="manage-column column-type" style="">Type</th>
    	</tr>
    	</thead>

    	<tfoot>
    	<tr>
        	<th scope="col" id="title" class="manage-column column-title" style="">Title</th>
        	<th scope="col" id="author-name" class="manage-column column-author-name" style="">Author Name</th>
        	<th scope="col" id="bibligraphy-type" class="manage-column column-type" style="">Type</th>
    	</tr>
    	</tfoot>

    	<tbody>
            <tr valign="middle" class="alternate">
            
            <?php foreach($entries as $entry): ?>
                <th scope="row" class="column-name"><strong><a class='row-title' href='admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=update_biblio&amp;entry_id=<?php echo $entry->entryID;?>' title='Edit &#8220;Development Blog&#8221;'><?php echo $entry->title; ?></a></strong>
                    <br />
                    <div class="row-actions">
                        <span class='edit'><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=update_biblio&amp;entry_id=<?php echo $entry->entryID;?>">Edit</a> | </span>
                        <span class='delete'><a class='submitdelete' href='admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_biblio&amp;entry_id=<?php echo $entry->entryID;?>' onclick="if ( confirm('You are about to delete this link \'Development Blog\'\n  \'Cancel\' to stop, \'OK\' to delete.') ) { return true;}return false;">Delete</a></span>
                    </div>
                </th>
                <td><?php echo $entry->author_last; ?><?php if($entry->author_first) echo ', '.$entry->author_first; ?></td>
                <td><?php echo $entry->type; ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        </table>
        
        <?php else: ?>
            <p>No bibliography entries.</p>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <br class="clear" />
    <?php
}
?>