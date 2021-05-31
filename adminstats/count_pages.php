<?php
include("db.php");

$con = mysqli_connect("localhost", "root", "", "qlblog_nhomST");
$sql = "SELECT * FROM page_hits ORDER BY page";

if ($result = mysqli_query($con, $sql)) {
    // Return the number of rows in result set
    $rowcount = mysqli_num_rows($result);
    printf("%d", $rowcount);
    // Free result set
    mysqli_free_result($result);
}

mysqli_close($con);
?>
