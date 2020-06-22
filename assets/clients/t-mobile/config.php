<?php
	//Includes
	require($_SERVER['DOCUMENT_ROOT'] . '\projecthawk\data\servers.php');
	require($_SERVER['DOCUMENT_ROOT'] . '\projecthawk\lib\extras.php');
	// require('../../lib/extras.php');
	
	//Project title
	$project = 'Hawk | Frontier';
	$client_id = '4664';

	//Database/s
	$db_name="wfm_barc";
	$db_ccms="CCMS";
	$db_avaya="Avaya";

    $mck_db = new PDO('mysql:host='.$mck_xmp['host'].';dbname='.$db_name, $mck_xmp['user'], $mck_xmp['pass']);
    $mck_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$ccms_db = new PDO('sqlsrv:Server='.$mck_srv['host'].';Database='.$db_ccms, $mck_srv['user'], $mck_srv['pass']);
	$ccms_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$avaya_db = new PDO('sqlsrv:Server='.$mck_srv['host'].';Database='.$db_avaya, $mck_srv['user'], $mck_srv['pass']);
	$avaya_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$db = new PDO('mysql:host='.$vtr_srv['host'].';dbname='.$db_name, $vtr_srv['user'], $vtr_srv['pass']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
?>
