<?php
define('SP_BIBLIOGRAPHY_PAGE', '<spbibliography />');

// Handles the bibliography management page
function bibliography_manage()
{
	global $table_prefix, $wpdb;

	$updateaction = !empty($_REQUEST['updateaction']) ? $_REQUEST['updateaction'] : '';
	$entryID = !empty($_REQUEST['entryID']) ? $_REQUEST['entryID'] : '';
	
	if (isset($_REQUEST['action']) ):
		if ($_REQUEST['action'] == 'delete_biblio') 
		{
			$entryID = intval($_GET['entryID']);
			if (empty($entryID))
			{
				?><div class="error"><p><strong>Failure:</strong> No Biblio-ID given. I guess I deleted nothing successfully.</p></div><?php
			}
			else
			{
				$wpdb->query("DELETE FROM " . $table_prefix . "bibliography WHERE entryID = '" . $entryID . "'");
				$sql = "SELECT entryID FROM " . $table_prefix . "bibliography WHERE entryID = '" . $entryID . "'";
				$check = $wpdb->get_results($sql);
				if ( empty($check) || empty($check[0]->entryID) )
				{
					?><div class="updated"><p>Biblio Entry <?php echo $entryID; ?> deleted successfully.</p></div><?php
				}
				else
				{
					?><div class="error"><p><strong>Failure:</strong> Ninjas proved my kung-fu to be too weak to delete that entry.</p></div><?php
				}
			}
		} // end delete_biblio block
	endif;
	
	if ( $updateaction == 'update_biblio' )
	{
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
		
		if ( empty($entryID) )
		{
			?><div class="error"><p><strong>Failure:</strong> No biblio-id given. Can't save nothing. Giving up...</p></div><?php
		}
		else
		{
			$sql = "UPDATE " . $table_prefix . "bibliography SET author_last = '" . $author_last . "', author_first = '" . $author_first . "', author_two_last = '" . $author_two_last . "', author_two_first = '" . $author_two_first . "', title = '" . $title . "', short_title = '" . $short_title . "', journal = '" . $journal . "', volume_title = '" . $volume_title . "', volume_editors = '" . $volume_editors . "', website_title = '" . $website_title . "', pub_location = '" . $pub_location . "', publisher = '" . $publisher . "', date = '" . $date . "', dateaccessed = '" . $dateaccessed . "', url = '" . $url . "', volume = '" . $volume ."', issue = '" . $issue . "', pages = '" . $pages . "', description = '" . $description . "', type = '" . $type . "' WHERE entryID = '" . $entryID . "'";
			$wpdb->get_results($sql);
			$sql = "SELECT entryID FROM " . $table_prefix . "bibliography WHERE author_last = '" . $author_last . "' and author_first = '" . $author_first . "' and author_two_last = '" . $author_two_last . "' and author_two_first = '" . $author_two_first . "' and title = '" . $title . "' and short_title = '" . $short_title . "' and journal = '" . $journal . "' and volume_title = '" . $volume_title . "' and volume_editors = '" . $volume_editors . "' and website_title = '" . $website_title . "' and pub_location = '" . $pub_location . "' and publisher = '" . $publisher . "' and date = '" . $date . "' and dateaccessed = '" . $dateaccessed . "' and url = '" . $url . "' and volume = '" . $volume ."' and issue = '" . $issue . "' and pages = '" . $pages . "' and description = '" . $description . "' and type = '" . $type . "' LIMIT 1";
			$check = $wpdb->get_results($sql);
			if ( empty($check) || empty($check[0]->entryID) )
			{
				?><div class="error"><p><strong>Failure:</strong> The Evil Monkey Overlord wouldn't let me update your entry. Try again?</p></div><?php
			}
			else
			{
				?><div class="updated"><p>Biblio <?php echo $entryID; ?> updated successfully.</p></div><?php
			}
		}
	} // end update_biblio block
	elseif ( $updateaction == 'add_biblio' )
	{
		$author_last = !empty($_REQUEST['biblio_author_last']) ? $_REQUEST['biblio_author_last'] : '';
		$author_first = !empty($_REQUEST['biblio_author_first']) ? $_REQUEST['biblio_author_first'] : '';
		$author_two_last = !empty($_REQUEST['biblio_author_two_last']) ? $_REQUEST['biblio_author_two_last'] : '';
		$author_two_first = !empty($_REQUEST['biblio_author_two_first']) ? $_REQUEST['biblio_author_two_first'] : '';
		$title = !empty($_REQUEST['biblio_title']) ? $_REQUEST['biblio_title'] : '';
		$short_title = !empty($_REQUEST['biblio_short_title']) ? $_REQUEST['biblio_short_title'] : '';
		$journal = !empty($_REQUEST['biblio_journal']) ? $_REQUEST['biblio_journal'] : '';
		$pub_location = !empty($_REQUEST['biblio_pub_location']) ? $_REQUEST['biblio_pub_location'] : '';
		$publisher = !empty($_REQUEST['biblio_publisher']) ? $_REQUEST['biblio_publisher'] : '';
		$date = !empty($_REQUEST['biblio_date']) ? $_REQUEST['biblio_date'] : '';
		$dateaccessed = !empty($_REQUEST['biblio_dateaccessed']) ? $_REQUEST['biblio_dateaccessed'] : '';
		$website_title = !empty($_REQUEST['biblio_website_title']) ? $_REQUEST['biblio_website_title'] : '';
		$volume_title = !empty($_REQUEST['biblio_volume_title']) ? $_REQUEST['biblio_volume_title'] : '';
		$volume_editors = !empty($_REQUEST['biblio_volume_editors']) ? $_REQUEST['biblio_volume_editors'] : '';
		$url = !empty($_REQUEST['biblio_url']) ? $_REQUEST['biblio_url'] : '';
		$volume = !empty($_REQUEST['biblio_volume']) ? $_REQUEST['biblio_volume'] : '';
		$issue = !empty($_REQUEST['biblio_issue']) ? $_REQUEST['biblio_issue'] : '';
		$pages = !empty($_REQUEST['biblio_pages']) ? $_REQUEST['biblio_pages'] : '';
		$description = !empty($_REQUEST['biblio_description']) ? $_REQUEST['biblio_description'] : '';
		$type = !empty($_REQUEST['biblio_type']) ? $_REQUEST['biblio_type'] : '';
		
		$sql = "INSERT INTO " . $table_prefix . "bibliography SET author_last = '" . $author_last . "', author_first = '" . $author_first . "', author_two_last = '" . $author_two_last . "', author_two_first = '" . $author_two_first . "', title = '" . $title . "', short_title = '" . $short_title . "', journal = '" . $journal . "', volume_title = '" . $volume_title . "', volume_editors = '" . $volume_editors . "', website_title = '" . $website_title . "', pub_location = '" . $pub_location . "', publisher = '" . $publisher . "', date = '" . $date . "',  dateaccessed = '" . $dateaccessed . "',url = '" . $url . "', volume = '" . $volume ."', issue = '" . $issue . "', pages = '" . $pages . "', description = '" . $description . "', type = '" . $type . "'";
		$wpdb->get_results($sql);
		$sql = "SELECT entryID FROM " . $table_prefix . "bibliography WHERE author_last = '" . $author_last . "' and author_first = '" . $author_first . "' and author_two_last = '" . $author_two_last . "' and author_two_first = '" . $author_two_first . "' and title = '" . $title . "' and short_title = '" . $short_title . "' and journal = '" . $journal . "' and volume_title = '" . $volume_title . "' and volume_editors = '" . $volume_editors . "' and website_title = '" . $website_title . "' and pub_location = '" . $pub_location . "' and publisher = '" . $publisher . "' and date = '" . $date . "'and dateaccessed = '" . $dateaccessed . "' and url = '" . $url . "' and volume = '" . $volume ."' and issue = '" . $issue . "' and pages = '" . $pages . "' and description = '" . $description . "' and type = '" . $type . "'";
		$check = $wpdb->get_results($sql);
		if ( empty($check) || empty($check[0]->entryID) )
		{
			?><div class="error"><p><strong>Failure:</strong> Try again? </p></div><?php
		}
		else
		{
			?><div class="updated"><p>Writing up a storm! Biblio id <?php echo $check[0]->entryID;?> added successfully.</p></div><?php
		}
	} // end add_biblio block
	?>

	<div class=wrap>
	<?php
	if ( $_REQUEST['action'] == 'edit_biblio' )
	{
		?>
		<h2><?php _e('Edit Bibliography Entry'); ?></h2>
		<?php
		if ( empty($entryID) )
		{
			echo "<div class=\"error\"><p>I didn't get an entry identifier from the query string. Giving up...</p></div>";
		}
		else
		{
			bibliography_editform('update_biblio', $entryID);
		}	
	}
	else
	{
		?>
		<h2><?php _e('Add Bibliography Entry'); ?></h2>
		<?php bibliography_editform(); ?>
	
		<h2><?php _e('Manage Bibliography'); ?></h2>
		<?php
			bibliography_displaylist();
	}
	?>
	</div><?php
}

// Displays the list of bibliography entries
function bibliography_displaylist() 
{
	global $wpdb, $table_prefix;
	
	$biblios = $wpdb->get_results("SELECT * FROM " . $table_prefix . "bibliography ORDER BY entryID DESC");
	
	if ( !empty($biblios) )
	{
		?>
			<table width="100%" cellpadding="3" cellspacing="3">
			<tr>
				<th scope="col"><?php _e('ID') ?></th>
				<th scope="col"><?php _e('Last') ?></th>
				<th scope="col"><?php _e('First') ?></th>
				<th scope="col"><?php _e('Title') ?></th>
				<th scope="col"><?php _e('Type') ?></th>
				<th scope="col"><?php _e('Edit') ?></th>
				<th scope="col"><?php _e('Delete') ?></th>
			</tr>
		<?php
		$class = '';
		foreach ( $biblios as $biblio )
		{
			$class = ($class == 'alternate') ? '' : 'alternate';
			?>
			<tr class="<?php echo $class; ?>">
				<th scope="row"><?php echo $biblio->entryID; ?></th>
				<td><?php echo $biblio->author_last ?></td>
				<td><?php echo $biblio->author_first ?></td>
				<td><?php echo $biblio->title ?></td>
				<td><?php echo $biblio->type; ?></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=edit_biblio&amp;entryID=<?php echo $biblio->entryID;?>" class="edit"><?php echo __('Edit'); ?></a></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_biblio&amp;entryID=<?php echo $biblio->entryID;?>" class="delete" onclick="return confirm('Are you sure you want to delete this entry?')"><?php echo __('Delete'); ?></a></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		?>
		<p><?php _e("You haven't entered any biblio entries yet.") ?></p>
		<?php	
	}
}


// Displays the add/edit form
function bibliography_editform($mode='add_biblio', $entryID=false)
{
	global $wpdb, $table_prefix;
	$data = false;
	
	if ( $entryID !== false )
	{
		// this next line makes me about 200 times cooler than you.
		if ( intval($entryID) != $entryID )
		{
			echo "<div class=\"error\"><p>Bad Monkey! No banana!</p></div>";
			return;
		}
		else
		{
			$data = $wpdb->get_results("SELECT * FROM " . $table_prefix . "bibliography WHERE entryID = '" . $entryID . " LIMIT 1'");
			if ( empty($data) )
			{
				echo "<div class=\"error\"><p>I couldn't find a quote linked up with that identifier. Giving up...</p></div>";
				return;
			}
			$data = $data[0];
		}	
	}
	
	?>

	<form name="biblioform" id="biblioform" class="wrap" method="post" action="">
		<input type="hidden" name="updateaction" value="<?php echo $mode?>">
		<input type="hidden" name="entryID" value="<?php echo $entryID?>">
	
		<div id="item_manager">
			<div style="float: left; width: 98%; clear: both;" class="top">

				<div style="float: right; width: 200px;" class="top">
				<fieldset class="small" id="biblio_type_field"><legend><?php _e('Type'); ?></legend>
					<input type="radio" name="biblio_type" onclick="toggleType()" class="input" value="monograph" 
					<?php if ( empty($data) || $data->type=='monograph' ) echo "checked" ?>/>  Book <br />
					
					<input type="radio" name="biblio_type" onclick="toggleType()" class="input" value="article" 
					<?php if ( !empty($data) && $data->type=='article' ) echo "checked" ?>/>  Article 
					<br />
					<input type="radio" name="biblio_type" onclick="toggleType()" class="input" value="volumechapter" 
					<?php if ( !empty($data) && $data->type=='volumechapter' ) echo "checked" ?>/>  Volume Chapter 
					<br />
					<input type="radio" name="biblio_type" onclick="toggleType()" class="input" value="unpublished" 
					<?php if ( !empty($data) && $data->type=='unpublished' ) echo "checked" ?>/>  Unpublished 
					<br />
					<input type="radio" name="biblio_type" onclick="toggleType()" class="input" value="website" 
					<?php if ( !empty($data) && $data->type=='website' ) echo "checked" ?>/>  Website 
					<br />
					<input type="radio" name="biblio_type" onclick="toggleType()" class="input" value="webpage" 
					<?php if ( !empty($data) && $data->type=='webpage' ) echo "checked" ?>/>  Webpage 
					<br />
					
					<input type="radio" name="biblio_type" onclick="toggleType()" class="input" value="video" 
					<?php if ( !empty($data) && $data->type=='video' ) echo "checked" ?>/>  Video 
					<br />
					<input type="radio" name="biblio_type" onclick="toggleType()" class="input" value="audio" 
					<?php if ( !empty($data) && $data->type=='audio' ) echo "checked" ?>/>  Audio 
					
				</fieldset>
					
				</div>

				<!-- List URL -->
				<fieldset class="small" id="biblio_author_last_field"><legend><?php _e('Author Last Name'); ?></legend>
					<input type="text" name="biblio_author_last" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_last); ?>" />
				</fieldset>
				<fieldset class="small" id="biblio_author_first_field"><legend><?php _e('Author First Name'); ?></legend>
					<input type="text" name="biblio_author_first" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_first); ?>" />
				</fieldset>
				<fieldset class="small" id="biblio_author_two_last_field"><legend><?php _e('Author Two Last Name'); ?></legend>
					<input type="text" name="biblio_author_two_last" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_two_last); ?>" />
				</fieldset>
				<fieldset class="small" id="biblio_author_two_first_field"><legend><?php _e('Author Two First Name'); ?></legend>
					<input type="text" name="biblio_author_two_first" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->author_two_first); ?>" />
				</fieldset>
				<fieldset class="small" id="biblio_title_field"><legend><?php _e('Title'); ?></legend>
					<input type="text" name="biblio_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->title); ?>" />
				</fieldset>
				<fieldset class="small" id="biblio_short_title_field"><legend><?php _e('Short Title'); ?></legend>
					<input type="text" name="biblio_short_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->short_title); ?>" />
				</fieldset>
				<fieldset class="small" id="biblio_journal_field"><legend><?php _e('Journal'); ?></legend>
					<input type="text" name="biblio_journal" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->journal); ?>" />
				</fieldset>
				
				<fieldset class="small" id="biblio_volume_title_field"><legend><?php _e('Volume Title'); ?></legend>
					<input type="text" name="biblio_volume_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume_title); ?>" />
				</fieldset>
				
				<fieldset class="small" id="biblio_volume_editors_field"><legend><?php _e('Volume Editor(s)'); ?></legend>
					<input type="text" name="biblio_volume_editors" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume_editors); ?>" />
				</fieldset>
				
				<fieldset class="small" id="biblio_pub_location_field"><legend><?php _e('Place of Publication'); ?></legend>
					<input type="text" name="biblio_pub_location" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->pub_location); ?>" />
				</fieldset>
				<fieldset class="small" id="biblio_publisher_field"><legend><?php _e('Publisher'); ?></legend>
					<input type="text" name="biblio_publisher" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->publisher); ?>" />
				</fieldset>
				<fieldset class="small" id="biblio_website_title_field"><legend><?php _e('Website Title'); ?></legend>
					<input type="text" name="biblio_website_title" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->website_title); ?>" />
				</fieldset>
				
				<fieldset class="small" id="biblio_date_field"><legend><?php _e('Date Published'); ?></legend>
					<input type="text" name="biblio_date" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->date); ?>" />
				</fieldset>
				
				<fieldset class="small" id="biblio_dateaccessed_field"><legend><?php _e('Date Accessed'); ?></legend>
					<input type="text" name="biblio_dateaccessed" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->dateaccessed); ?>" />
				</fieldset>
				
				<fieldset class="small" id="biblio_url_field"><legend><?php _e('URL'); ?></legend>
					<input type="text" name="biblio_url" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->url); ?>" />
				</fieldset>
				
				<fieldset class="small" id="biblio_volume_field"><legend><?php _e('Volume'); ?></legend>
					<input type="text" name="biblio_volume" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->volume); ?>" />
				</fieldset>

				<fieldset class="small" id="biblio_issue_field"><legend><?php _e('Issue'); ?></legend>
					<input type="text" name="biblio_issue" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->issue); ?>" />
				</fieldset>

				<fieldset class="small" id="biblio_pages_field"><legend><?php _e('Pages'); ?></legend>
					<input type="text" name="biblio_pages" class="input" size=45 value="<?php if ( !empty($data) ) echo htmlspecialchars($data->pages); ?>" />
				</fieldset>

				<fieldset class="small" id="biblio_description_field"><legend><?php _e('Description'); ?></legend>
					<textarea name="biblio_description" class="input" cols=45 rows=7><?php if ( !empty($data) ) echo htmlspecialchars($data->description); ?></textarea>
				</fieldset>

				<input type="submit" name="save" class="button bold" value="Save &raquo;" />


			</div>
			<div style="clear:both; height:1px;">&nbsp;</div>
		</div>
	</form>
	<?php
}

// Print small biblio entry (eg title and link only, for use in sidebar)
function bib_printsmall($bibliosmall, $class='class="bib_small"') { ?>
<p class="hcite <?php echo $bibliosmall->type; ?>">
<?php if(!empty($bibliosmall->author_last)): ?><span class="author"><span class="family-name"><?php echo ($bibliosmall->author_last); ?></span><?php if( !empty($bibliosmall->author_two_last)): ?> and <span class="family-name"><?php echo ($bibliosmall->author_two_last); ?></span><?php endif; ?>, fff<?php endif; ?><?php if(!empty($bibliosmall->url)){?><a href="<?php echo $bibliosmall->url; ?>"><?php } ?><?php if(!empty($bibliosmall->short_title)): ?> Foo<span class="title"><?php echo ($bibliosmall->short_title); ?></span><?php elseif(!empty($bibliosmall->title)): ?> <span class="title"><?php echo ($bibliosmall->title); ?></span><?php endif; ?><?php if(!empty($bibliosmall->url)){?></a><?php } ?>.
</p>
<?php }

// Print full biblio entry 
function bib_printfull($bibliofull,$description=false) { ?>
<div class="hcite <?php echo $bibliofull->type; ?>">
<p><?php if( !empty($bibliofull->author_last)): ?><span class="creator fn"><span class="family-name"><?php echo ($bibliofull->author_last); ?></span>, <span class="given-name"><?php echo ($bibliofull->author_first); ?></span><?php if( !empty($bibliofull->author_two_last)): ?> and <span class="given-name"><?php echo ($bibliofull->author_two_first); ?></span> <span class="family-name"><?php echo ($bibliofull->author_two_last); ?></span><?php endif; ?>. <?php endif; ?><?php if($bibliofull->type != 'monograph'){ ?>&#8220;<?php } ?><?php if(!empty($bibliofull->url)){ ?><a href="<?php echo($bibliofull->url); ?>"><?php } ?><span class="title"><?php echo nl2br($bibliofull->title); ?></span><?php if ( !empty($bibliofull->url)){ ?></a><?php } ?>.<?php if($bibliofull->type != 'monograph') { ?>&#8221;<?php } ?>
	
<?php if ($bibliofull->type == 'monograph'): ?>
<?php if ( !empty($bibliofull->pub_location)) { ?><span class="location"><?php echo nl2br($bibliofull->pub_location); ?></span>:<?php } ?><?php if (!empty($bibliofull->publisher)) { ?> <span><?php echo nl2br($bibliofull->publisher); ?></span>,<?php } ?> <?php if( !empty($bibliofull->date)) { ?><span class="date"><?php echo ($bibliofull->date); ?></span>.<?php } ?>

<?php elseif ($bibliofull->type == 'article'): ?>
<span class="journal"><?php echo nl2br($bibliofull->journal); ?></span> <?php if ( !empty($bibliofull->volume) || !empty($bibliofull->issue)) { ?><span class="volume"><?php echo nl2br($bibliofull->volume); ?></span>, no. <span><?php echo nl2br($bibliofull->issue); ?></span><?php } ?> <?php if( !empty($bibliofull->date)) { ?>(<span class="date"><?php echo ($bibliofull->date); ?></span>)<?php } ?><?php if ( !empty($bibliofull->pages)) { ?>: <span class="pages"><?php echo ($bibliofull->pages); ?></span><?php } ?>. 

<?php elseif ($bibliofull->type == 'website'): ?>
<?php if( !empty($bibliofull->date)) { ?><span class="date-published"><?php echo ($bibliofull->date); ?></span><?php } ?><?php if( !empty($bibliofull->dateaccessed)) { ?>. (Accessed <span class="date-accesed"><?php echo ($bibliofull->dateaccessed); ?></span>)<?php } if(!empty($bibliofull->date) || !empty($bibliofull->dateaccessed)) { ?>.<?php } ?> 

<?php elseif ($bibliofull->type == 'webpage'): ?>
<?php if(!empty($bibliofull->website_title)) { ?><span class="website-title"><?php echo $bibliofull->website_title; ?></span>. <?php } ?><?php if( !empty($bibliofull->date)) { ?><span class="date-published"><?php echo ($bibliofull->date); ?></span><?php } ?><?php if( !empty($bibliofull->dateaccessed)) { ?>. (Accessed <span class="date-accesed"><?php echo ($bibliofull->dateaccessed); ?></span>)<?php } if(!empty($bibliofull->date) || !empty($bibliofull->dateaccessed)) { ?>.<?php } ?>

<?php elseif($bibliofull->type == 'unpublished'): ?>
<?php if( !empty($bibliofull->date)) { ?><span class="date-published"><?php echo ($bibliofull->date); ?>.</span><?php } ?> 

<?php else: ?>
<?php if( !empty($bibliofull->date)) { ?><span class="date-published"><?php echo ($bibliofull->date); ?></span>.<?php } ?>
<?php endif;  ?></p>
<?php if ( $description==true && !empty($bibliofull->description) ) { ?>
	<p class="description"><?php echo nl2br($bibliofull->description);?></p>
<?php } ?>
</div>
	<?php
}


// Print specific bibliography entry
function bib_specific($id, $full="small")
{
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . "bibliography";
	$sql = "select * from " . $table_name . " where entryID='{$id}'";
	$result = $wpdb->get_results($sql);
	
	if ( !empty($result) ) {
		if ($full=="full") {
			bib_printfull($result[0]);
		} 
		else {
			bib_printsmall($result[0]);
		}
	}	
}

// Print all bibliography entries onto a page, sorted by type
function bib_page($data,$headings='h4')
{
	global $wpdb, $table_prefix;
	$table_name = $table_prefix . "bibliography";
	$start = strpos($data, SP_BIBLIOGRAPHY_PAGE);
	if ( $start != false )
	{
		ob_start();
		global $wpdb;

			$sql_monograph = $wpdb->get_results("SELECT entryID FROM " . $table_name . " WHERE (type='monograph')");
		if (count($sql_monograph) > 0 )
		{
			echo "<".$headings.">Monographs</".$headings.">";
			$published = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE (type='monograph') ORDER BY 'entryID' DESC");
			if ( !empty($published) )
			{
				foreach ( $published as $row )
				{
					bib_printfull($row);
				}
			}
		}

		$sql_unpublished = $wpdb->get_results("SELECT entryID FROM " . $table_name . " WHERE (type='unpublished')");
		if (count($sql_unpublished) > 0 )
		{
			echo "<".$headings.">Unpublished</".$headings.">";
			$published = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE (type='unpublished') ORDER BY 'entryID' DESC");
			if ( !empty($published) )
			{
				foreach ( $published as $row )
				{
					bib_printfull($row);
				}
			}
		}

		$sql_article = $wpdb->get_results("SELECT entryID FROM " . $table_name . " WHERE (type='article')");
		if (count($sql_article) > 0 )
		{
			echo "<".$headings.">Articles</".$headings.">";
			$published = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE (type='article') ORDER BY 'entryID' DESC");
			if (!empty($published))
			{
				foreach ( $published as $row )
				{ 
	  			bib_printfull($row);
				}
			}
		}

		$sql_website = $wpdb->get_results("SELECT entryID FROM " . $table_name . " WHERE (type='website')");
		if (count($sql_website) > 0)
		{
			echo "<".$headings.">Websites</".$headings.">";
			$published = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE (type='website') ORDER BY 'entryID' DESC");
			if (!empty($published))
			{
				foreach ( $published as $row )
				{
					bib_printfull($row);
				}
			}
		}

		$sql_webpages = $wpdb->get_results("SELECT entryID FROM " . $table_name . " WHERE (type='webpage')");
		if (count($sql_webpages) > 0)
		{
			echo "<".$headings.">Webpages</".$headings.">";
			$published = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE (type='webpage') ORDER BY 'entryID' DESC");
			if (!empty($published))
			{
				foreach ( $published as $row )
				{
					bib_printfull($row);
				}
			}
		}

		$sql_videos = $wpdb->get_results("SELECT entryID FROM " . $table_name . " WHERE (type='video')");
		if (count($sql_videos) > 0)
		{
			echo "<".$headings.">Video</".$headings.">";
			$published = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE (type='video') ORDER BY 'entryID' DESC");
			if (!empty($published))
			{
				foreach ( $published as $row )
				{
					bib_printfull($row);
				}
			}
		}

		$sql_audios = $wpdb->get_results("SELECT entryID FROM " . $table_name . " WHERE (type='audio')");
		if (count($sql_audios) > 0)
		{
			echo "<".$headings.">Audio</".$headings.">";
			$published = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE (type='audio') ORDER BY 'entryID' DESC");
			if (!empty($published))
			{
				foreach ( $published as $row )
				{
					bib_printfull($row);
				}
			}
		}	

		$contents = ob_get_contents();
		ob_end_clean();
		$data = substr_replace($data, $contents, $start, strlen(SP_BIBLIOGRAPHY_PAGE));
	}
	return $data;
}
?>