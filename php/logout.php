<?php
session_start();

session_unset(); // очищає всі дані
session_destroy(); // знищує сесію

header("Location: home.php");
exit();