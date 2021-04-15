<?php
error_reporting(1);
ini_set('display_errors', 'On');
require("security.php");
header("Content-Type: application/json");
require("class.php");
$db = new Database();
$db->connect();

$order_by 		= isset($_GET['order_by'])?$_GET['order_by']:'ASC';
$post_source 	= isset($_GET['post_source'])?(int)$_GET['post_source']:1;
$category_id 	= isset($_GET['category_id'])?(int)$_GET['category_id']:null;
$app_secret = 'e1111a26fc31aa10f444f69f41f4d5a'; 
$app_id 	= 'webikeplus-app';				
$check_sign 	= substr(hash_hmac('sha256', $app_id.'_'.$config['domain'], $app_secret),0,10);
//69d676a0b9
//https: 7e44162c02
$arrReturn = array(); 
if($config['key']!=$check_sign)
{	
	$arrReturn	= array('response_code'=>'S001', 'response_message'=>'https://'.$_SERVER['HTTP_HOST'], 'key'=>$check_sign);
	echo json_encode($arrReturn);
	exit();
}else{			
	if(!empty($config['key']))
	{
		$arrReturn	= array('response_code'=>'S000', 'response_message'=>'OK', 'data'=>array());
		if(!empty($app_secret)){
			$sll = $db->select("tbl_plus_wp_term","*","post_source = {$post_source}","ORDER BY post_term_id ASC",0,10000);
			$aContent = $db->getResult($sll); 			
			$category_tree = $db->buildTree($aContent,0);
			//print_r($category_tree); exit; 
			
			$arrReturn	= array('response_code'=>'S000', 'response_message'=>'OK', 'data'=>$category_tree);						
		}		
		echo json_encode($arrReturn);	
		exit(); 
	}else{					
		$arrReturn	= array('response_code'=>'S000', 'response_message'=>'OK', 'data'=>array());
		echo json_encode($arrReturn);	
		exit(); 
	}
	exit();  	
}
?>