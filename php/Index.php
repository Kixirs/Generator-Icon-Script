<?php 
include "header.php";
define('PROFILE_START_TIME', microtime(true));
define('PROFILE_START_MEMORY', memory_get_usage());
?>

<div class = "container">
    <?php include "sidebar.php"; ?>
    <?php 
        require_once __DIR__ . '/../database/db.php';
        $pdo = getDBConnection(); 
        include "home.php"; 
    ?>
</div>

<?php
$profileEndTime = microtime(true);
$profileEndMemory = memory_get_usage();

$totalTime = $profileEndTime - PROFILE_START_TIME;
$totalMemory = $profileEndMemory - PROFILE_START_MEMORY;

echo sprintf(
    "<!-- PERFORMANCE REPORT: Execution Time: %f seconds | Memory Used: %s KB -->",
    round($totalTime, 4),
    round($totalMemory / 1024, 2)
);