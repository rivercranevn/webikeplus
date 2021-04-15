<?php
//set_time_limit(0);
error_reporting(1);
ini_set('display_errors', 'Off');
require("security.php");
header("Content-Type: application/json");
require("class.php");
$db = new Database();
$db->connect();

$post_id 	= (int)$_GET['post_id'];
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
			$sll = $db->select("tbl_plus_wp_content","*",array("alter_user"=>'System', 'post_id'=>$post_id),"ORDER BY post_id ASC",0,1);
			$aContent = $db->getResult($sll); 
			$arrReturn	= array('response_code'=>'S000', 'response_message'=>'OK', 'data'=>$aContent);						
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