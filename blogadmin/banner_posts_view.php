<?php

	$currDir=dirname(__FILE__);
	include("$currDir/defaultLang.php");
	include("$currDir/language.php");
	include("$currDir/lib.php");
	@include("$currDir/hooks/banner_posts.php");
	include("$currDir/banner_posts_dml.php");

	// mm: can the current member access this page?
	$perm=getTablePermissions('banner_posts');
	if(!$perm[0]){
        $Translation ='tableAccessDenied';
        echo error_message($Translation, false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = "banner_posts";

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = array(   
		"`banner_posts`.`id`" => "id",
		"IF(    CHAR_LENGTH(`blogs1`.`title`) || CHAR_LENGTH(`blogs1`.`id`), CONCAT_WS('',   `blogs1`.`title`, ' :id ', `blogs1`.`id`), '') /* Title */" => "title",
		"`banner_posts`.`status`" => "status"
	);
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = array(   
		1 => '`banner_posts`.`id`',
		2 => 2,
		3 => 3
	);

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = array(   
		"`banner_posts`.`id`" => "id",
		"IF(    CHAR_LENGTH(`blogs1`.`title`) || CHAR_LENGTH(`blogs1`.`id`), CONCAT_WS('',   `blogs1`.`title`, ' :id ', `blogs1`.`id`), '') /* Title */" => "title",
		"`banner_posts`.`status`" => "status"
	);
	// Fields that can be filtered
	$x->QueryFieldsFilters = array(   
		"`banner_posts`.`id`" => "ID",
		"IF(    CHAR_LENGTH(`blogs1`.`title`) || CHAR_LENGTH(`blogs1`.`id`), CONCAT_WS('',   `blogs1`.`title`, ' :id ', `blogs1`.`id`), '') /* Title */" => "Title",
		"`banner_posts`.`status`" => "Status"
	);

	// Fields that can be quick searched
	$x->QueryFieldsQS = array(   
		"`banner_posts`.`id`" => "id",
		"IF(    CHAR_LENGTH(`blogs1`.`title`) || CHAR_LENGTH(`blogs1`.`id`), CONCAT_WS('',   `blogs1`.`title`, ' :id ', `blogs1`.`id`), '') /* Title */" => "title",
		"`banner_posts`.`status`" => "status"
	);

	// Lookup fields that can be used as filterers
	$x->filterers = array(  'title' => 'Title');

	$x->QueryFrom = "`banner_posts` LEFT JOIN `blogs` as blogs1 ON `blogs1`.`id`=`banner_posts`.`title` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm[2]==0 ? 1 : 0);
	$x->AllowDelete = $perm[4];
	$x->AllowMassDelete = false;
	$x->AllowInsert = $perm[1];
	$x->AllowUpdate = $perm[3];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 0;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowCSV = 1;
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;

    $Translation = 'quick search';
    $x->QuickSearchText = $Translation;
	$x->ScriptFileName = "banner_posts_view.php";
	$x->RedirectAfterInsert = "banner_posts_view.php?SelectedID=#ID#";
	$x->TableTitle = "Banner posts";
	$x->TableIcon = "resources/table_icons/32_bit.png";
	$x->PrimaryKey = "`banner_posts`.`id`";

	$x->ColWidth   = array(  150, 150);
	$x->ColCaption = array("Title", "Status");
	$x->ColFieldName = array('title', 'status');
	$x->ColNumber  = array(2, 3);

	// template paths below are based on the app main directory
	$x->Template = 'templates/banner_posts_templateTV.html';
	$x->SelectedTemplate = 'templates/banner_posts_templateTVS.html';
	$x->TemplateDV = 'templates/banner_posts_templateDV.html';
	$x->TemplateDVP = 'templates/banner_posts_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->ShowRecordSlots = 0;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HighlightColor = '#FFF0C2';

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, array('user', 'group'))){ $DisplayRecords = 'all'; }
	if($perm[2]==1 || ($perm[2]>1 && $DisplayRecords=='user' && !$_REQUEST['NoFilter_x'])){ // view owner only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `banner_posts`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='banner_posts' and lcase(membership_userrecords.memberID)='".getLoggedMemberID()."'";
	}elseif($perm[2]==2 || ($perm[2]>2 && $DisplayRecords=='group' && !$_REQUEST['NoFilter_x'])){ // view group only
		$x->QueryFrom.=', membership_userrecords';
		$x->QueryWhere="where `banner_posts`.`id`=membership_userrecords.pkValue and membership_userrecords.tableName='banner_posts' and membership_userrecords.groupID='".getLoggedGroupID()."'";
	}//elseif($perm[2]==3){ // view all
		// no further action
	//}
	elseif($perm[2]==0){ // view none
		$x->QueryFields = array("Not enough permissions" => "NEP");
		$x->QueryFrom = '`banner_posts`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: banner_posts_init
	$render=TRUE;
	if(function_exists('banner_posts_init')){
		$args=array();
		$render=banner_posts_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: banner_posts_header
	$headerCode='';
	if(function_exists('banner_posts_header')){
		$args=array();
		$headerCode=banner_posts_header($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$headerCode){
		include_once("$currDir/header.php"); 
	}else{
		ob_start(); include_once("$currDir/header.php"); $dHeader=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%HEADER%%>', $dHeader, $headerCode);
	}

	echo $x->HTML;
	// hook: banner_posts_footer
	$footerCode='';
	if(function_exists('banner_posts_footer')){
		$args=array();
		$footerCode=banner_posts_footer($x->ContentType, getMemberInfo(), $args);
	}  
	if(!$footerCode){
		include_once("$currDir/footer.php"); 
	}else{
		ob_start(); include_once("$currDir/footer.php"); $dFooter=ob_get_contents(); ob_end_clean();
		echo str_replace('<%%FOOTER%%>', $dFooter, $footerCode);
	}
?>

