<?php
//set_time_limit(0);
error_reporting(1);
ini_set('display_errors', 'Off');
require("security.php");
header("Content-Type: application/json");
require("config.php");
$db = new Database();
$db->connect();

//$Domain = 'https://www.webike.net'; 
//$key 	= '2a80445b8b';	
$order_by 		= isset($_GET['order_by'])?$_GET['order_by']:null;
$category_id 	= isset($_GET['category_id'])?(int)$_GET['category_id']:1;
$post_source 	= isset($_GET['post_source'])?(int)$_GET['post_source']:52;
$post_offset 	= isset($_GET['post_offset'])?(int)$_GET['post_offset']:0;
$post_limit 	= isset($_GET['post_limit'])?(int)$_GET['post_limit']:20;
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
			$arrContent = array(); 	
			$cmDataList = @json_decode($db->Curl($config['post_api']."?config={$post_source}&sort={$order_by}&per_page={$post_limit}&recommend=all&types[]={$category_id}"));	
			$arrImgs = array(); 
			foreach($cmDataList->data as $k => $v){
				preg_match_all('/<img[^>]+>/i',$v->post_content, $imgs[$k]); 
				if(isset($imgs[$k][0])){
					foreach($imgs[$k][0] as $img){
						preg_match( '@src="([^"]+)"@' , $img, $match[$k] );		
						$arrImgs[$k][] = $match[$k][1];
					}
				}
				$arrContent[$k]['post_source'] = $v->config_id; 
				$arrContent[$k]['post_id'] = $v->id; 
				$arrContent[$k]['post_title'] = @strip_tags(html_entity_decode($v->post_title)); 
				$arrContent[$k]['post_name'] = $v->post_slug; 
				$arrContent[$k]['post_term_id'] = $category_id;
				$arrContent[$k]['post_term_name'] = '一時リスト';				
				$arrContent[$k]['post_eye_catch_imgs'] = (isset($arrImgs[$k])&&!empty($arrImgs[$k]))?$arrImgs[$k]:null; 
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