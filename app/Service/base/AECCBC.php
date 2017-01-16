<?php
namespace App\Service\base;
class AECCBC{
//    public function __construct($iv,){
//
//    }
//    static $encryType = 'hex';
    static $encryType = 'base64';
    static $iv = "1234567890123456"; /* 必须16位哦 */

    /* 采用128位加密，密钥也必须是16位 */
    static public function aes_encode($sourcestr, $key = 'bqjr012345678910')
    {
        switch(self::$encryType){
            case 'hex':
                return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $sourcestr, MCRYPT_MODE_CBC, AECCBC::$iv));
                break;
            case 'base64':
                return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $sourcestr, MCRYPT_MODE_CBC, AECCBC::$iv));
                break;
        }
//        $tmp = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $sourcestr, MCRYPT_MODE_CBC, AECCBC::$iv));
//        return bin2hex($tmp);
    }

    static public function aes_decode($crypttext, $key = 'bqjr012345678910')
    {
        switch(self::$encryType) {
            case 'hex':
                return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, hex2bin($crypttext), MCRYPT_MODE_CBC, AECCBC::$iv), "\0");
                break;
            case 'base64':
                return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($crypttext), MCRYPT_MODE_CBC, AECCBC::$iv), "\0");
                break;
        }
    }
}