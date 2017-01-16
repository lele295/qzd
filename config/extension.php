<?php
return [
    'version'=>env('VERSION','1.1.0'),
    'mtitle'=>env('MTITLE','签署协议'),
    'test_host'=>'http://qzd.yiqitansuo.com/',
    'facepp'=>['key'=>env('FACEPPKEY'),'secret'=>env('FACEPPSECRET')],
    'face_link_fate'=>env('FACELINKFATE',0.5),
    'face_link_switch'=>env('FACELINKSWITCH',true),
    'face_link_key'=>env('FACELINKKEY'),
    'face_link_secret'=>env('FACELINKSECRET'),
	// 2016-07-05新增
	'face_compare_accessid' => env('FACE_COMPARE_ACCESSID', '33009'),
	'face_compare_accesskey' => env('FACE_COMPARE_ACCESSKEY', '352d0eb43aabd8f818a9a7bf693fda8c'),
	'yitu_public_key' => storage_path().'/yitu/staging.public.pem',
	'yitu_api_ocr' => env('YITU_API_OCR', 'http://10.28.1.99:9500/face/basic/ocr'),
	'yitu_api_compare' => env('YITU_API_COMPARE', 'http://10.28.1.99:9500/face/v1/algorithm/recognition/face_pair_verification'),
	'yitu_api_decode' => env('TITU_API_DECODE','http://10.28.1.99:9500/face/basic/check_image_package'),
    'yitu_api_timeout' => env('YITU_API_TIMEOUT', 10),
	'bank_auth'=>env('BANK_AUTH',false)
];