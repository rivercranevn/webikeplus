<?php
error_reporting(1);
ini_set('display_errors', 'Off');
require("security.php");
header("Content-Type: application/json");
require("class.php");
$db = new Database();
$db->connect();

//$Domain = 'https://www.webike.net'; 
//$key 	= '2a80445b8b';	
$order_by 		= isset($_GET['order_by'])?$_GET['order_by']:'ASC';
$category_id 	= isset($_GET['category_id'])?(int)$_GET['category_id']:null;
$post_source 	= isset($_GET['post_source'])?(int)$_GET['post_source']:1;
$post_offset 	= isset($_GET['post_offset'])?(int)$_GET['post_offset']:0;
$post_limit 	= isset($_GET['post_limit'])?(int)$_GET['post_limit']:5;
$app_secret 	= 'e1111a26fc31aa10f444f69f41f4d5a'; 
$app_id 		= 'webikeplus-app';				
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
			$filter = array("post_source"=>5, 'post_source'=>$post_source); 
			if($category_id >0){
				$filter = array("post_source"=>5, 'post_source'=>$post_source,'post_term_id'=>$category_id); 
			}	
			$sll = $db->select("tbl_plus_wp_content","post_source, post_id, post_title, post_name, post_term_id, post_eye_catch_img, post_link, post_date, modify_date, alter_date, alter_user",$filter,"ORDER BY post_term_id {$order_by}",0,$post_limit);
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