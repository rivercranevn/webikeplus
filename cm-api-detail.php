<?php
//set_time_limit(0);
error_reporting(1);
ini_set('display_errors', 'Off');
require("security.php");
header("Content-Type: application/json");
require("config.php");
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
			$arrContent = array(); 	
			$cmDataList = @json_decode($db->Curl($config['post_api']."?config={$post_source}&sort={$order_by}&per_page={$post_limit}&recommend=all&post_id={$post_id}"));
			$doc=new DOMDocument();	
			foreach($cmDataList->data as $k => $v){				
				//preg_match_all('/<img[^>]+>/i',$v->post_content, $img); 
				preg_match( '@src="([^"]+)"@' , $v->post_content, $match );				
				$arrContent[$k]['post_source'] = $v->config_id; 
				$arrContent[$k]['post_id'] = $v->id; 
				$arrContent[$k]['post_title'] = @strip_tags(html_entity_decode($v->post_title)); 
				$arrContent[$k]['post_content'] = $v->post_content; 
				$arrContent[$k]['post_name'] = $v->post_slug; 
				$arrContent[$k]['post_term_id'] = null; 
				$arrContent[$k]['post_eye_catch_img'] = isset($match[1])?$match[1]:null; 
				$arrContent[$k]['post_link'] = $v->full_url; 
				$arrContent[$k]['post_date'] = $v->created_at; 
				$arrContent[$k]['modify_date'] = $v->updated_at; 
				$arrContent[$k]['alter_date'] = $v->approved_at; 
				$arrContent[$k]['alter_user'] = 'System'; 
			}			
			//print_r($cmDataList); exit; 			
			$arrReturn	= array('response_code'=>'S000', 'response_message'=>'OK', 'data'=>$arrContent);						
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