<?php
	
function screeny_capture( $url, $size, $clearCache = FALSE ) {
	$requestUrl="http://api.screenshotmachine.com/";
	$key=esc_attr( get_option('screeny_key') );
	$requestUrl.="?key=$key";
	$requestUrl.="&url=$url";
	$requestUrl.="&size=$size";
	
	
	if ( TRUE == $clearCache) {
		$requestUrl.="&cacheLimit=0";
	} else {
		$requestUrl.="&cacheLimit=30";
	}
	
	$requestUrl.="&timeout=10000";
	
	return $requestUrl;
	
}
	
?>