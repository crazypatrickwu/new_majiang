<?php
$alipay_config['partner']				= '2088521529672715';
$alipay_config['key']					= '2zrf6nbsnrdib3nev0tsqkr65k7s98bm';
$alipay_config['seller_id']				= $alipay_config['partner'];
$alipay_config['private_key_path'] 		= VENDOR_PATH.'Alipay/key/rsa_private_key.pem';
$alipay_config['ali_public_key_path']	= VENDOR_PATH.'Alipay/key/alipay_public_key.pem';
$alipay_config['sign_type']    			= strtoupper('RSA');
$alipay_config['input_charset']			= strtolower('utf-8');
$alipay_config['cacert']    			= VENDOR_PATH.'Alipay/cacert.pem';
$alipay_config['transport']    			= 'http';
?>