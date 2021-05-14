<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$currDir = dirname(__FILE__);
include("./defaultLang.php");
include("./language.php");
include("./lib.php");

$x = new DataList;
$x->TableTitle = "Homepage";
$tablesPerRow = 2;
$arrTables = getTableList();

// according to provided GET parameters, either log out, show login form (possibly with a failed login message), or show homepage
if (isset($_GET['signOut'])) {
    logOutUser();
    redirect("index.php?signIn=1");
} elseif (isset($_GET['loginFailed']) || isset($_GET['signIn'])) {
    if (!headers_sent() && isset($_GET['loginFailed'])) header('HTTP/1.0 403 Forbidden');
    include("./login.php");
} else {
    include("./main.php");
}
