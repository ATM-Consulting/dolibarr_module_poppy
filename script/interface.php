<?php
	ob_start();

	ini_set('display_errors','On');
	error_reporting(E_ALL);

	require('../config.php');
	dol_include_once('/user/class/usergroup.class.php');
	dol_include_once('/core/lib/date.lib.php');
	dol_include_once('/expedition/class/expedition.class.php');

	ob_clean();
	
	$PDOdb = new TPDOdb;

	$get = isset($_REQUEST['get'])?$_REQUEST['get']:'';
	$put = isset($_REQUEST['put'])?$_REQUEST['put']:'';
	
	_get($PDOdb,$get);
	_put($PDOdb,$put);

function _get(&$PDOdb,$case) {
	switch ($case) {
		case 'shipping-details':
            $TTask = _getShippingDetails($PDOdb,GETPOST('id'));
			__out($TTask, 'json');
			break;
		
		case 'logged-status':
			print 'ok';
			
			break;
		default:
			
			break;
	}
	
}

function _put(&$PDOdb,$case) {
	
	switch ($case) {
	}
}

function _getShippingDetails(&$PDOdb, $id) {
	global $db,$langs,$user,$conf;
	$Tab=array();
	
	$expedition = new Expedition($db);
	$expedition->fetch($id);
	
	foreach($expedition->lines as &$line) {
		
		if($line->fk_product>0) {
			
			$line->product = new Product($db);
			$line->product->fetch($line->fk_product);
			
			$line->barcode = $line->product->barcode;
		}
		
		$Tab[] = $line;
		
	}
	
	return $Tab;
}
