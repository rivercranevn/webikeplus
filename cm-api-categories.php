<?php
error_reporting(1);
ini_set('display_errors', 'On');
require("config.php");
header("Content-Type: application/json");
$order_by 		= isset($_GET['order_by'])?$_GET['order_by']:'ASC';
$post_source 	= isset($_GET['post_source'])?(int)$_GET['post_source']:1;
$category_id 	= isset($_GET['category_id'])?(int)$_GET['category_id']:null;
$post_limit 	= isset($_GET['post_limit'])?(int)$_GET['post_limit']:20;
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
			$arrContent = array(); 	
			$cmDataList = @json_decode($db->Curl($config['category_api']."?config={$post_source}&sort={$order_by}&per_page={$post_limit}&page=1"));	
			$arrImgs = array(); 
			foreach($cmDataList->data as $k => $v){				
				$arrContent[$k]['post_source'] = $v->config_id; 
				$arrContent[$k]['post_term_id'] = $v->id; 
				$arrContent[$k]['post_term_name'] = @strip_tags(html_entity_decode($v->title)); 
				$arrContent[$k]['post_term_parent_id'] = $v->parent_id;  
			}			
			$category_tree = $db->buildTree($arrContent,0);
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