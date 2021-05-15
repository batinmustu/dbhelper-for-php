<?php
include "DBHelper/DBHelper.php";
use \DBHelper\DBHelper;

$db = new DBHelper();

 $db->connect();

// somethings...

 $db->disconnect();