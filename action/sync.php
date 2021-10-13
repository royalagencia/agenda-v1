<?php
include_once("../set/dados_db.php");
include_once("../set/config.php");
@session_start();
include_once("../set/Du-Art_functions.php");
$sync = new SyncDB();
$sync->exe();
?>