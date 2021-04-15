<?php
error_reporting(1);
ini_set('display_errors', 'On');
require("config.php");
header("Content-Type: application/json");
$order_by 		= isset($_GET['order_by'])?$_GET['order_by']:'ASC';
$post_source 	= isset($_GET['post_source'])?(int)$_GET['post_source']:1;
$category_id 	= isset($_GET['category_id'])?(int)$_GET['category_id']:null;
$app_secret = 'e1111a26fc31aa10f444f69f41f4d5a'; 
$app_id 	= 'webikeplus-app';				
$check_sign 	= substr(hash_hmac('sha256', $app_id.'_'.$config['domain'], $app_secret),0,10);

$db = new Database();
$db->connect();

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
			//?sort=&page=1&per_page=10&search=&config=52
			$sll = $db->Curl($config['category_api']);
			//$aContent = $db->getResult($sll); 			
			//$category_tree = $db->buildTree($aContent,0);
			print_r($sll); exit; 
			
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