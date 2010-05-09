<?php

function spcourseware_get_bibliography_entries($type=null, $limit=null)
{
    global $wpdb;
	$bibliography_table_name = $wpdb->prefix . "bibliography";
	
	$sql = "SELECT * FROM " . $bibliography_table_name;
	
	if($type) {
	    $sql .= " WHERE type=".$type;
	}
	
	$sql .= " ORDER BY author_last, title ASC";
	
	if($limit != null) {
	    $sql .= " LIMIT ". $limit;
	}
	
	$results = $wpdb->get_results($sql, OBJECT);
	return $results;
}

function spcourseware_get_bibliography_entry_by_id($id) 
{
    global $wpdb;
	$bibliography_table_name = $wpdb->prefix . "bibliography";
	$sql = "SELECT * from " . $bibliography_table_name . " WHERE entryID=".$id;
	return $wpdb->get_row($sql, OBJECT);
}

function spcourseware_delete_bibliography_entry($id)
{
    global $wpdb;
    if($id) {
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "bibliography WHERE entryID = '" . $id . "'");
    }
}

function spcourseware_add_bibliography_entry()
{
    global $wpdb;
    
    // print_r($_REQUEST); exit;
    
    $author_last = !empty($_REQUEST['biblio_author_last']) ? $_REQUEST['biblio_author_last'] : '';
    $author_first = !empty($_REQUEST['biblio_author_first']) ? $_REQUEST['biblio_author_first'] : '';
    $author_two_last = !empty($_REQUEST['biblio_author_two_last']) ? $_REQUEST['biblio_author_two_last'] : '';
    $author_two_first = !empty($_REQUEST['biblio_author_two_first']) ? $_REQUEST['biblio_author_two_first'] : '';
    $title = !empty($_REQUEST['biblio_title']) ? $_REQUEST['biblio_title'] : '';
    $short_title = !empty($_REQUEST['biblio_short_title']) ? $_REQUEST['biblio_short_title'] : '';
    $journal = !empty($_REQUEST['biblio_journal']) ? $_REQUEST['biblio_journal'] : '';
    $volume_title = !empty($_REQUEST['biblio_volume_title']) ? $_REQUEST['biblio_volume_title'] : '';
    $volume_editors = !empty($_REQUEST['biblio_volume_editors']) ? $_REQUEST['biblio_volume_editors'] : '';
    $website_title = !empty($_REQUEST['biblio_website_title']) ? $_REQUEST['biblio_website_title'] : '';
    $pub_location = !empty($_REQUEST['biblio_pub_location']) ? $_REQUEST['biblio_pub_location'] : '';
    $publisher = !empty($_REQUEST['biblio_publisher']) ? $_REQUEST['biblio_publisher'] : '';
    $date = !empty($_REQUEST['biblio_date']) ? $_REQUEST['biblio_date'] : '';
    $dateaccessed = !empty($_REQUEST['biblio_dateaccessed']) ? $_REQUEST['biblio_dateaccessed'] : '';
    $url = !empty($_REQUEST['biblio_url']) ? $_REQUEST['biblio_url'] : '';
    $volume = !empty($_REQUEST['biblio_volume']) ? $_REQUEST['biblio_volume'] : '';
    $issue = !empty($_REQUEST['biblio_issue']) ? $_REQUEST['biblio_issue'] : '';
    $pages = !empty($_REQUEST['biblio_pages']) ? $_REQUEST['biblio_pages'] : '';
    $description = !empty($_REQUEST['biblio_description']) ? $_REQUEST['biblio_description'] : '';
    $type = !empty($_REQUEST['biblio_type']) ? $_REQUEST['biblio_type'] : '';
    
    // echo $author_last; exit;
    
	$sql = "INSERT INTO " . $wpdb->prefix . "bibliography SET author_last = '" . $author_last . "', author_first = '" . $author_first . "', author_two_last = '" . $author_two_last . "', author_two_first = '" . $author_two_first . "', title = '" . $title . "', short_title = '" . $short_title . "', journal = '" . $journal . "', volume_title = '" . $volume_title . "', volume_editors = '" . $volume_editors . "', website_title = '" . $website_title . "', pub_location = '" . $pub_location . "', publisher = '" . $publisher . "', date = '" . $date . "',  dateaccessed = '" . $dateaccessed . "',url = '" . $url . "', volume = '" . $volume ."', issue = '" . $issue . "', pages = '" . $pages . "', description = '" . $description . "', type = '" . $type . "'";
    
    $wpdb->query($sql);
    
    $sqlCheck = "SELECT entryID FROM " . $wpdb->prefix . "bibliography WHERE author_last = '" . $author_last . "' and author_first = '" . $author_first . "' and author_two_last = '" . $author_two_last . "' and author_two_first = '" . $author_two_first . "' and title = '" . $title . "' and short_title = '" . $short_title . "' and journal = '" . $journal . "' and volume_title = '" . $volume_title . "' and volume_editors = '" . $volume_editors . "' and website_title = '" . $website_title . "' and pub_location = '" . $pub_location . "' and publisher = '" . $publisher . "' and date = '" . $date . "' and dateaccessed = '" . $dateaccessed . "' and url = '" . $url . "' and volume = '" . $volume ."' and issue = '" . $issue . "' and pages = '" . $pages . "' and description = '" . $description . "' and type = '" . $type . "' LIMIT 1";
    
    $check = $wpdb->get_results($sqlCheck);
    if ( !empty($check) || !empty($check[0]->entryID) ) { 
        echo '<div class="updated"><p>'. sprintf( __( 'Bibliography %1$d updated successfully.', SPCOURSEWARE_TD ), $entryID ).'</p></div>';
    }
    
}

function spcourseware_update_bibliography_entry($id)
{
    global $wpdb;
        
    // print_r($_REQUEST); exit;
    $author_last = !empty($_REQUEST['biblio_author_last']) ? $_REQUEST['biblio_author_last'] : '';
    $author_first = !empty($_REQUEST['biblio_author_first']) ? $_REQUEST['biblio_author_first'] : '';
    $author_two_last = !empty($_REQUEST['biblio_author_two_last']) ? $_REQUEST['biblio_author_two_last'] : '';
    $author_two_first = !empty($_REQUEST['biblio_author_two_first']) ? $_REQUEST['biblio_author_two_first'] : '';
    $title = !empty($_REQUEST['biblio_title']) ? $_REQUEST['biblio_title'] : '';
    $short_title = !empty($_REQUEST['biblio_short_title']) ? $_REQUEST['biblio_short_title'] : '';
    $journal = !empty($_REQUEST['biblio_journal']) ? $_REQUEST['biblio_journal'] : '';
    $volume_title = !empty($_REQUEST['biblio_volume_title']) ? $_REQUEST['biblio_volume_title'] : '';
    $volume_editors = !empty($_REQUEST['biblio_volume_editors']) ? $_REQUEST['biblio_volume_editors'] : '';
    $website_title = !empty($_REQUEST['biblio_website_title']) ? $_REQUEST['biblio_website_title'] : '';
    $pub_location = !empty($_REQUEST['biblio_pub_location']) ? $_REQUEST['biblio_pub_location'] : '';
    $publisher = !empty($_REQUEST['biblio_publisher']) ? $_REQUEST['biblio_publisher'] : '';
    $date = !empty($_REQUEST['biblio_date']) ? $_REQUEST['biblio_date'] : '';
    $dateaccessed = !empty($_REQUEST['biblio_dateaccessed']) ? $_REQUEST['biblio_dateaccessed'] : '';
    $url = !empty($_REQUEST['biblio_url']) ? $_REQUEST['biblio_url'] : '';
    $volume = !empty($_REQUEST['biblio_volume']) ? $_REQUEST['biblio_volume'] : '';
    $issue = !empty($_REQUEST['biblio_issue']) ? $_REQUEST['biblio_issue'] : '';
    $pages = !empty($_REQUEST['biblio_pages']) ? $_REQUEST['biblio_pages'] : '';
    $description = !empty($_REQUEST['biblio_description']) ? $_REQUEST['biblio_description'] : '';
    $type = !empty($_REQUEST['biblio_type']) ? $_REQUEST['biblio_type'] : '';

    if ( empty($id) ) {
    	echo '<div class="error"><p>'. __( '<strong>Failure:</strong> No bibliography ID given.', SPCOURSEWARE_TD ).'</p></div>';
    } else {
        $sql = "UPDATE " . $wpdb->prefix . "bibliography SET author_last = '" . $author_last . "', author_first = '" . $author_first . "', author_two_last = '" . $author_two_last . "', author_two_first = '" . $author_two_first . "', title = '" . $title . "', short_title = '" . $short_title . "', journal = '" . $journal . "', volume_title = '" . $volume_title . "', volume_editors = '" . $volume_editors . "', website_title = '" . $website_title . "', pub_location = '" . $pub_location . "', publisher = '" . $publisher . "', date = '" . $date . "', dateaccessed = '" . $dateaccessed . "', url = '" . $url . "', volume = '" . $volume ."', issue = '" . $issue . "', pages = '" . $pages . "', description = '" . $description . "', type = '" . $type . "' WHERE entryID = '" . $id . "'";
        $wpdb->query($sql);
        
        $sqlCheck = "SELECT entryID FROM " . $wpdb->prefix . "bibliography WHERE author_last = '" . $author_last . "' and author_first = '" . $author_first . "' and author_two_last = '" . $author_two_last . "' and author_two_first = '" . $author_two_first . "' and title = '" . $title . "' and short_title = '" . $short_title . "' and journal = '" . $journal . "' and volume_title = '" . $volume_title . "' and volume_editors = '" . $volume_editors . "' and website_title = '" . $website_title . "' and pub_location = '" . $pub_location . "' and publisher = '" . $publisher . "' and date = '" . $date . "' and dateaccessed = '" . $dateaccessed . "' and url = '" . $url . "' and volume = '" . $volume ."' and issue = '" . $issue . "' and pages = '" . $pages . "' and description = '" . $description . "' and type = '" . $type . "' LIMIT 1";
        $check = $wpdb->get_results($sqlCheck);
        if ( !empty($check) || !empty($check[0]->id) ) { 
            echo '<div class="updated"><p>'. sprintf( __( 'Bibliography %1$d updated successfully.', SPCOURSEWARE_TD ), $id ) .'</p></div>';
        }
    }
}

function spcourseware_bibliography_navigation()
{
    ?>
        <p>
			<a href="admin.php?page=<?php echo $_GET['page']; ?>"><?php echo __( 'View Bibliography', SPCOURSEWARE_TD ); ?></a> |
			<a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;view=form&amp;action=add_biblio"><?php echo __( 'Add an Bibliography Entry', SPCOURSEWARE_TD ); ?></a>
		</p>
<?php
}

function spcourseware_bibliography_edit_form($mode='add_biblio', $id=false)
{
	$data = false;
	
	if($mode == 'add_biblio') {
	    echo '<h3>'. __( 'Add an Bibliography Entry', SPCOURSEWARE_TD ) .'</h3>';
	}
	
	if ( $id !== false ) {
		if ( intval($id) != $id ){
			echo '<div class="error"><p>'. sprintf( __( 'Bibliography ID %s1d is not a valid integer.', SPCOURSEWARE_TD), $id ) .'</p></div>';
			return;
		} else {
			$data = spcourseware_get_bibliography_entry_by_id($id);
			echo '<h3>'. __( 'Update Bibliography Entry #', SPCOURSEWARE_TD) .$id. '</h3>';
			if ( empty($data) ) {
				echo "<div class=\"error\"><p>". __( "I couldn't find a quote linked up with that identifier. Giving up...", SPCOURSEWARE_TD ) ."</p></div>";
				return;
			}
		}	
	}
	
    ?>
    
    <form name="biblioform" id="biblioform" class="wrap" method="post" action="">
    		<input type="hidden" name="update_action" value="<?php echo $mode; ?>">
    		<input type="hidden" name="entry_id" value="<?php echo $id; ?>">
            <script type="text/javascript" charset="utf-8">
        	jQuery(function($){

                $('#assignment_title').hide();
                $('#assignment_bibliography_entry').show();

                $("#biblio_type option:selected").each(function(){
                    
                    var val = this.value;
                    
                    if ((val == 'monograph') || (val == 'textbook')) {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').show();
                        $('#biblio_publisher_field').show();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    } else if (val == 'article') {      
                        $('#biblio_journal_field').show();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').show();
                        $('#biblio_issue_field').show();
                        $('#biblio_pages_field').show();
                    } else if (val == 'volumechapter') {        
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').show();
                        $('#biblio_publisher_field').show();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').show();
                        $('#biblio_website_title_field').hide();
                        $('#biblio_dateaccessed_field').hide();
                        $('#biblio_date_field').show(); 
                    } else if (val == 'unpublished') {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_date_field').hide();

                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    } else if (val == 'website') {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_date_field').show();
                        $('#biblio_dateaccessed_field').show();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    }
                });

                $("select#biblio_type").change(function () {
                    var val = this.value;
                    
                    if ((val == 'monograph') || (val == 'textbook')) {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').show();
                        $('#biblio_publisher_field').show();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    } else if (val == 'article') {      
                        $('#biblio_journal_field').show();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').show();
                        $('#biblio_issue_field').show();
                        $('#biblio_pages_field').show();
                    } else if (val == 'volumechapter') {        
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').show();
                        $('#biblio_publisher_field').show();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').show();
                        $('#biblio_website_title_field').hide();
                        $('#biblio_dateaccessed_field').hide();
                        $('#biblio_date_field').show(); 
                    } else if (val == 'unpublished') {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_date_field').hide();

                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    } else if (val == 'website') {      
                        $('#biblio_journal_field').hide();
                        $('#biblio_pub_location_field').hide();
                        $('#biblio_publisher_field').hide();
                        $('#biblio_url_field').show();
                        $('#biblio_volume_field').hide();
                        $('#biblio_date_field').show();
                        $('#biblio_dateaccessed_field').show();
                        $('#biblio_issue_field').hide();
                        $('#biblio_pages_field').hide();
                    }
                });

        	});
        	</script>
    					<table class="form-table">
    						
    						<tr valign="top">
    						<th>
                            <label for="biblio_type"><?php echo __( 'Type of Bibliography Item' ); ?></labgel></th>
                            <td>
                                <select name="biblio_type" id="biblio_type">
                                    <option><?php echo __( 'Choose a type', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_book" value="monograph"<?php if ( empty($data) || $data->type=='monograph' ) echo ' selected="selected"'; ?>><?php echo __( 'Book', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_article" value="article"<?php if ( !empty($data) && $data->type=='article' ) echo ' selected="selected"'; ?>><?php echo __( 'Article', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_volume" value="volumechapter"<?php if ( !empty($data) && $data->type=='volumechapter' ) echo ' selected="selected"'; ?>><?php echo __( 'Volume Chapter', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_unpublished" value="unpublished"<?php if ( !empty($data) && $data->type=='unpublished' ) echo ' selected="selected"' ?>><?php echo __( 'Unpublished', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_website" value="website"<?php if ( !empty($data) && $data->type=='website' ) echo ' selected="selected"'; ?>><?php echo __( 'Website', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_webpage" value="webpage"<?php if ( !empty($data) && $data->type=='webpage' ) echo ' selected="selected"'; ?>><?php echo __( 'Webpage', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_video" value="video"<?php if ( !empty($data) && $data->type=='video' ) echo ' selected="selected"'; ?>><?php echo __( 'Video', SPCOURSEWARE_TD ); ?></option>
                                    <option id="biblio_audio" value="audio"<?php if ( !empty($data) && $data->type=='audio' ) echo ' selected="selected"'; ?>><?php echo __( 'Audio', SPCOURSEWARE_TD ); ?></option>
                            </select>
                            </td>
                            </tr>
                            
                            <tr valign="top">

    						<th scope="row"><?php echo __( 'Author(s)', SPCOURSEWARE_TD ); ?></th>
    						<td class="inside withlabels">
    							<p><label for="biblio_author_last"><?php echo __( 'Author Last Name', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_author_last" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_last); ?>" />
    							<p><label for="biblio_author_first"><?php echo __( 'Author First Name', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_author_first" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_first); ?>" />
    							<p><label for="biblio_author_two_last"><?php echo __( 'Author Two Last Name', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_author_two_last" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_two_last); ?>" />
    							<p><label for="biblio_author_two_first"><?php echo __( 'Author Two First Name', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_author_two_first" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_two_first); ?>" />
    						</td>
</tr>
<tr valign="top">
    						<th><span><?php echo __( 'Publish Information', SPCOURSEWARE_TD ); ?></span></th>
    						<td>
    						<fieldset class="small" id="biblio_title_field">
    							<p><label for="biblio_title"><?php echo __( 'Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->title); ?>" />
    						</fieldset>
    						<fieldset class="small" id="biblio_short_title_field">
    							<p><label for="biblio_short_title"><?php echo __( 'Short Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_short_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->short_title); ?>" />
    						</fieldset>
    						<fieldset class="small" id="biblio_journal_field">
    							<p><label for="biblio_journal"><?php echo __( 'Journal Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_journal" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->journal); ?>" />
    						</fieldset>

    						<fieldset class="small" id="biblio_volume_title_field">
    							<p><label for="biblio_volume_title"><?php echo __( 'Volume Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_volume_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume_title); ?>" />
    						</fieldset>

    						<fieldset class="small" id="biblio_volume_editors_field">
    							<p><label for="biblio_volume_editors"><?php echo __( 'Volume Editor(s)', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_volume_editors" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume_editors); ?>" />
    						</fieldset>

    						<fieldset class="small" id="biblio_pub_location_field">
    							<p><label for="biblio_pub_location"><?php echo __( 'Place of Publication', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_pub_location" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->pub_location); ?>" />
    						</fieldset>
    						<fieldset class="small" id="biblio_publisher_field">
    							<p><label for="biblio_publisher"><?php echo __( 'Publisher', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_publisher" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->publisher); ?>" />
    						</fieldset>
    						<fieldset class="small" id="biblio_website_title_field">
    							<p><label><?php echo __( 'Website Title', SPCOURSEWARE_TD ); ?></label></p>
    							<input type="text" name="biblio_website_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->website_title); ?>" />
    						</fieldset>							
    						</td>
    						</tr>
                            <tr valign="top">

    						<th class='hndle'><span><?php echo __( 'Additional Information', SPCOURSEWARE_TD ); ?></span></th>
    						<td class="inside withlabels">
    							<fieldset class="small" id="biblio_date_field">
    								<p><label for="biblio_date"><?php echo __( 'Date Published', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_date" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->date); ?>" />
    							</fieldset>				
    							<fieldset class="small" id="biblio_dateaccessed_field">
    								<p><label for="biblio_dateaccessed"><?php echo __( 'Date Accessed', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_dateaccessed" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->dateaccessed); ?>" />
    							</fieldset>				
    							<fieldset class="small" id="biblio_url_field">
    								<p><label for="biblio_url"><?php echo __( 'URL', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_url" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->url); ?>" />
    							</fieldset>			
    							<fieldset class="small" id="biblio_volume_field">
    								<p><label for="biblio_volume"><?php echo __( 'Volume', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_volume" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume); ?>" />
    							</fieldset>
    							<fieldset class="small" id="biblio_issue_field">
    								<p><label for="biblio_issue"><?php echo __( 'Issue', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_issue" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->issue); ?>" />
    							</fieldset>
    							<fieldset class="small" id="biblio_pages_field">
    								<p><label for="biblio_pages"><?php echo __( 'Pages', SPCOURSEWARE_TD ); ?></label></p>
    								<input type="text" name="biblio_pages" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->pages); ?>" />
    							</fieldset>
    							<fieldset class="small" id="biblio_description_field">
    								<p><label for="biblio_description"><?php echo __( 'Description', SPCOURSEWARE_TD ); ?></label></p>
    								<textarea name="biblio_description" class="input" cols=45 rows=7><?php if ( !empty($data) ) echo htmlspecialchars($data->description); ?></textarea>
    							</fieldset>
    						</td>
    					</tr>
    					</table>
		<p class="submit clear"><input type="submit" name="save" class="button-primary" value="<?php echo __( 'Save Entry &raquo;', SPCOURSEWARE_TD ); ?>" /></p>
    	</form>
    	<div class="clear"></div>
    
    <?php
}

// Print small biblio entry (eg title and link only, for use in sidebar)
function spcourseware_bibliography_short($entry, $wrapper='p') 
{ ?>
    <<?php echo $wrapper; ?> class="hcite <?php echo $entry->type; ?>">
    <?php if(!empty($entry->author_last)) { ?><span class="creator fn"><span class="family-name"><?php echo ($entry->author_last); ?></span></span><?php if( !empty($entry->author_two_last)) { ?> and <span class="creator fn"><span class="family-name"><?php echo ($entry->author_two_last); ?></span></span><?php } ?>, <? } if(!empty($entry->url)) { ?><a href="<?php echo $entry->url; ?>"><?php } ?><?php if(!empty($entry->short_title)){ ?> <span class="title"><?php if($entry->type != 'monograph' && $entry->type !='website') { ?>&#8220;<?php } ?><?php echo ($entry->short_title); ?><?php if($entry->type != 'monograph' && $entry->type !='website') { ?></span>.&#8221;<?php } else{ ?>.<?php } ?><?php } elseif(!empty($entry->title)){ ?> <span class="title"><?php if($entry->type != 'monograph' && $entry->type !='website') { ?>&#8220;<?php } ?><?php echo ($entry->title); ?><?php if($entry->type != 'monograph' && $entry->type !='website') { ?></span>.&#8221;<?php } else { ?>.<?php } } if(!empty($entry->url)){?></a><?php } ?>
    </<?php echo $wrapper; ?>>
    <?php 
}

// Print full biblio entry 
function spcourseware_bibliography_full($entry,$bibid=false,$description=false) 
{ ?>
    <div class="hcite <?php echo $entry->type; ?>"<?php if($bibid==true){?> id="bib-entry-<?php echo $entry->entryID; ?>"<?php } ?>>
    <p><?php if( !empty($entry->author_last)): ?><span class="creator fn"><span class="family-name"><?php echo ($entry->author_last); ?></span>, <span class="given-name"><?php echo ($entry->author_first); ?></span><?php if( !empty($entry->author_two_last)): ?> and <span class="given-name"><?php echo ($entry->author_two_first); ?></span> <span class="family-name"><?php echo ($entry->author_two_last); ?></span><?php endif; ?>. <?php endif; ?><?php if($entry->type != 'monograph' && $entry->type !='website'){ ?>&#8220;<?php } if(!empty($entry->url)){ ?><a href="<?php echo($entry->url); ?>"><?php } ?><span class="title"><?php echo nl2br($entry->title); ?></span><?php if ( !empty($entry->url)){ ?></a><?php } ?>.<?php if($entry->type != 'monograph' && $entry->type !='website') { ?>&#8221;<?php } ?>
	
    <?php if ($entry->type == 'monograph'): ?>
    <?php if ( !empty($entry->pub_location)) { ?><span class="location"><?php echo nl2br($entry->pub_location); ?></span>:<?php } ?><?php if (!empty($entry->publisher)) { ?> <span><?php echo nl2br($entry->publisher); ?></span>,<?php } ?> <?php if( !empty($entry->date)) { ?><span class="date"><?php echo ($entry->date); ?></span>.<?php } ?>

    <?php elseif ($entry->type == 'volumechapter'): ?>
    <?php if(!empty($entry->volume_title)) { ?><span class="volume-title"><?php echo nl2br($entry->volume_title); ?></span>. <?php } if(!empty($entry->volume_editors)) { ?><span class="volume-editors"><?php echo $entry->volume_editors; ?>, ed.</span> <?php } if ( !empty($entry->pub_location)) { ?><span class="location"><?php echo nl2br($entry->pub_location); ?></span>:<?php } ?><?php if (!empty($entry->publisher)) { ?> <span><?php echo nl2br($entry->publisher); ?></span>,<?php } ?> <?php if( !empty($entry->date)) { ?><span class="date"><?php echo ($entry->date); ?></span><?php } ?><?php if ( !empty($entry->pages)) { ?>: <span class="pages"><?php echo ($entry->pages); ?></span><?php } ?>.

    <?php elseif ($entry->type == 'article'): ?>
    <span class="journal"><?php echo nl2br($entry->journal); ?></span> <?php if ( !empty($entry->volume) || !empty($entry->issue)) { ?><span class="volume"><?php echo nl2br($entry->volume); ?></span>, no. <span class="issue"><?php echo nl2br($entry->issue); ?></span><?php } ?> <?php if( !empty($entry->date)) { ?>(<span class="date"><?php echo ($entry->date); ?></span>)<?php } ?><?php if ( !empty($entry->pages)) { ?>: <span class="pages"><?php echo ($entry->pages); ?></span><?php } ?>. 

    <?php elseif ($entry->type == 'website'): ?>
    <?php if( !empty($entry->date)) { ?><span class="date-published"><?php echo ($entry->date); ?></span><?php } ?><?php if( !empty($entry->dateaccessed)) { ?>. (Accessed <span class="date-accesed"><?php echo ($entry->dateaccessed); ?></span>)<?php } if(!empty($entry->date) || !empty($entry->dateaccessed)) { ?>.<?php } ?> 

    <?php elseif ($entry->type == 'webpage'): ?>
    <?php if(!empty($entry->website_title)) { ?><span class="website-title"><?php echo $entry->website_title; ?></span>. <?php } ?><?php if( !empty($entry->date)) { ?><span class="date-published"><?php echo ($entry->date); ?></span><?php } ?><?php if( !empty($entry->dateaccessed)) { ?>. (Accessed <span class="date-accesed"><?php echo ($entry->dateaccessed); ?></span>)<?php } if(!empty($entry->date) || !empty($entry->dateaccessed)) { ?>.<?php } ?>

    <?php elseif($entry->type == 'unpublished'): ?>
    <?php if( !empty($entry->date)) { ?><span class="date-published"><?php echo ($entry->date); ?>.</span><?php } ?> 

    <?php else: ?>
    <?php if( !empty($entry->date)) { ?><span class="date-published"><?php echo ($entry->date); ?></span>.<?php } ?>
    <?php endif;  ?></p>
    <?php if ( $description==true && !empty($entry->description) ) { ?>
    	<p class="description"><?php echo nl2br($entry->description);?></p>
    <?php } ?>
    </div>
    <?php
}


?>