<?php
	error_reporting(E_ALL & ~(E_DEPRECATED | E_STRICT));
	define('TINYAJAX_PATH', '.');
	//opcache_reset();
	require_once("TinyAjax.php");
	require_once("TinyAjaxBehavior.php");
	require_once("vars.php");
	require_once("statusmessage.php");
	require_once("rules.php");
	require_once("recordings.php");
	require_once("series.php");
	require_once("hdhr.php");
	require_once("theme.php");
	require_once("upcoming.php");
	require_once("search.php");

	/* Prepare Ajax */
	$ajax = new TinyAjax();
	$ajax->setRequestType("POST");    // Change request-type from GET to POST
	$ajax->showLoading();             // Show loading while callback is in progress
	
	/* Export the PHP Interface */
	$ajax->exportFunction("openSeriesPage","");
	$ajax->exportFunction("openRulesPage","seriesid");
	$ajax->exportFunction("openRecordingsPage","seriesid");
	$ajax->exportFunction("openHDHRPage","");
	$ajax->exportFunction("openServerPage","");
	$ajax->exportFunction("openSearchPage","searchString");
	$ajax->exportFunction("openUpcomingPage","");
	$ajax->exportFunction("deleteRecordingByID","id, rerecord, seriesid");
	$ajax->exportFunction("deleteRuleByID","id");
	$ajax->exportFunction("deleteRuleFromSearch","searchstring, id");
	$ajax->exportFunction("createQuickRuleFromSearch","searchString, seriesid, recentonly");
	$ajax->exportFunction("createRuleFromSearch","searchString, seriesid, recentonly, start, end, channel, recordtime, recordafter");

	/* GO */
	$ajax->process(); // Process our callback

	// Apply default Theme */
	$stylesheet = getTheme();
	
	// Prep data for the page
	$statusmsg = getLatestHDHRStatus();

	//Build navigation menu for pages
	$pageTitles = array('Series','Rules', 'Recordings', 'Upcoming', 'Search','.');
	$pageNames = array('series_page', 'rules_page', 'recordings_page', 'upcoming_page', 'search_page', 'hdhr_page');
	$menu_data = file_get_contents('style/pagemenu.html');
	$menuEntries = '';
	for ($i=0; $i < count($pageNames); $i++) {
		$menuEntry = str_replace('<!-- dvrui_menu_pagename-->',$pageNames[$i],file_get_contents('style/pagemenu_entry.html'));
		$menuEntry = str_replace('<!-- dvrui_menu_pagetitle-->',$pageTitles[$i],$menuEntry);
		$menuEntries .= $menuEntry;
	}
	$menu_data = str_replace('<!-- dvrui_pagemenu_entries-->',$menuEntries,$menu_data);
	
	// --- Build Page Here ---
	$pageName = DVRUI_Vars::DVRUI_name;
	$UIVersion = "UI Version:" . DVRUI_Vars::DVRUI_version;
	$pagecontent = "";

	// --- include header ---
	$header = file_get_contents('style/header.html');
	$pagecontent = str_replace('[[pagetitle]]',$pageName,$header);
	$pagecontent = str_replace('<!-- stylesheet -->',$stylesheet,$pagecontent);
	$pagecontent = str_replace('<!-- tinyAjax -->',$ajax->drawJavaScript(false, true),$pagecontent);

	// --- Build Body ---
	$indexPage = file_get_contents('style/index_page.html');



	$rulesdata = file_get_contents('style/rules.html');
	$recordingsdata = file_get_contents('style/recordings.html');
	$seriesdata = file_get_contents('style/series.html');
	$hdhrdata = file_get_contents('style/hdhr.html');
	$updata = file_get_contents('style/upcoming.html');
	$searchdata = file_get_contents('style/search.html');

	$indexPage = str_replace('[[pagetitle]]',$pageName,$indexPage);
	$indexPage = str_replace('[[UI-Version]]',$UIVersion,$indexPage);

	$indexPage = str_replace('<!-- dvrui_pagemenu -->',$menu_data,$indexPage);
	$indexPage = str_replace('<!-- dvrui_serieslist -->',$seriesdata,$indexPage);
	$indexPage = str_replace('<!-- dvrui_ruleslist -->',$rulesdata,$indexPage);
	$indexPage = str_replace('<!-- dvrui_recordingslist -->',$recordingsdata,$indexPage);
	$indexPage = str_replace('<!-- dvrui_hdhrlist -->',$hdhrdata,$indexPage);
	$indexPage = str_replace('<!-- dvrui_upcominglist -->',$updata,$indexPage);
	$indexPage = str_replace('<!-- dvrui_searchlist -->',$searchdata,$indexPage);

	// -- Attach the Index to the Page
	$pagecontent .= $indexPage;

	// --- include footer ---
	$footer = file_get_contents('style/footer.html');
	$footer = str_replace('<!--dvr-statusmsg-->',$statusmsg,$footer);
	$pagecontent .= $footer;
	echo($pagecontent);
?>

