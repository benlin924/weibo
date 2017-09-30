<?php 
/**
 * 异位或者加密字符串
 * @param [string] $value [需要加密的字符串]
 * @param [integer] $type [加密解密（0：加密，1:解密）]
 * @return [string]  [加密或解密后字符串]
 */

function enctypetion($value,$type=0){
	$key = md5(C('ENCTYPTION_KEY'));
	if(!$type){
		return str_replace('=','',base64_encode($value ^ $key));
	};
	$value = base64_decode($value);
	return $value ^ $key;
}


 ?>