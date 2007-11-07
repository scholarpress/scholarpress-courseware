function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      oldonload();
      func();
    }
  }
}
// return the value of the radio button that is checked
// return an empty string if none are checked, or
// there are no radio buttons
function getCheckedValue(radioObj) {
	if(!radioObj)
		return "";
	var radioLength = radioObj.length;
	if(radioLength == undefined)
		if(radioObj.checked)
			return radioObj.value;
		else
			return "";
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			return radioObj[i].value;
		}
	}
	return "";
}
function toggleType() {

	// Get checked type
	val = getCheckedValue(document.forms['biblioform'].elements['biblio_type']);
	if ((val == 'monograph') || (val == 'textbook')) {		
		document.getElementById('biblio_journal_field').style.display = 'none';
		document.getElementById('biblio_pub_location_field').style.display = 'block';
		document.getElementById('biblio_publisher_field').style.display = 'block';
		document.getElementById('biblio_url_field').style.display = 'block';
		document.getElementById('biblio_volume_field').style.display = 'none';
		document.getElementById('biblio_issue_field').style.display = 'none';
		document.getElementById('biblio_pages_field').style.display = 'none';
	}
	else if (val == 'article') {		
		document.getElementById('biblio_journal_field').style.display = 'block';
		document.getElementById('biblio_pub_location_field').style.display = 'none';
		document.getElementById('biblio_publisher_field').style.display = 'none';
		document.getElementById('biblio_url_field').style.display = 'block';
		document.getElementById('biblio_volume_field').style.display = 'block';
		document.getElementById('biblio_issue_field').style.display = 'block';
		document.getElementById('biblio_pages_field').style.display = 'block';
		}
	else if (val == 'volumechapter') {		
		document.getElementById('biblio_journal_field').style.display = 'none';
		document.getElementById('biblio_pub_location_field').style.display = 'block';
		document.getElementById('biblio_publisher_field').style.display = 'block';
		document.getElementById('biblio_url_field').style.display = 'block';
		document.getElementById('biblio_volume_field').style.display = 'none';
		document.getElementById('biblio_issue_field').style.display = 'none';
		document.getElementById('biblio_pages_field').style.display = 'block';
		document.getElementById('biblio_website_title_field').style.display = 'none';
		document.getElementById('biblio_dateaccessed_field').style.display = 'none';
		document.getElementById('biblio_date_field').style.display = 'block';	
		}	
	else if (val == 'unpublished') {		
		document.getElementById('biblio_journal_field').style.display = 'none';
		document.getElementById('biblio_pub_location_field').style.display = 'none';
		document.getElementById('biblio_publisher_field').style.display = 'none';
		document.getElementById('biblio_url_field').style.display = 'block';
		document.getElementById('biblio_volume_field').style.display = 'none';
		document.getElementById('biblio_date_field').style.display = 'none';
		
		document.getElementById('biblio_issue_field').style.display = 'none';
		document.getElementById('biblio_pages_field').style.display = 'none';
		}	
	else if (val == 'website') {		
		document.getElementById('biblio_journal_field').style.display = 'none';
		document.getElementById('biblio_pub_location_field').style.display = 'none';
		document.getElementById('biblio_publisher_field').style.display = 'none';
		document.getElementById('biblio_url_field').style.display = 'block';
		document.getElementById('biblio_volume_field').style.display = 'none';
		document.getElementById('biblio_date_field').style.display = 'block';
		document.getElementById('biblio_dateaccessed_field').style.display = 'block';
		document.getElementById('biblio_issue_field').style.display = 'none';
		document.getElementById('biblio_pages_field').style.display = 'none';
		}	
}

function toggleAssignmentType() {
		val = getCheckedValue(document.forms['readingform'].elements['assignment_type']);
		if (val == 'reading') {		
			document.getElementById('bibfield').style.display = 'block';
			document.getElementById('pagesfield').style.display = 'block';
			document.getElementById('titlefield').style.display = 'none';
		}
		else {
			document.getElementById('bibfield').style.display = 'none';
			document.getElementById('pagesfield').style.display = 'none';
			document.getElementById('titlefield').style.display = 'block';
		}
}
