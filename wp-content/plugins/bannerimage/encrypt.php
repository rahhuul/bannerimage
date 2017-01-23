<?php

class CriptoPHP{
    private $extension = ".cphp";
    private $key = "siddhi_123"; //YOUR PRIVATE KEY
    
    public function __construct(){
        
    }
    private function readFile($script){
        $fp = fopen($script,"r");
        $data = fread($fp,filesize($script));
        fclose($fp);
        return $data;
    }
    public function encryptPHP($script){
        $data = $this->readFile($script.".php");
        $this->saveEncrypt($data,$script);
    }
    public function decryptAndInclude($script){
        $data = $this->decrypt($this->readFile($script.".cphp"));
        eval("?> ".$data." <?php");
    }
    private function saveEncrypt($data,$script){
        $fp = fopen($script.$this->extension,"w");
        fwrite($fp,$this->encrypt($data));
        fclose($fp);
    }
    private function encrypt($data){
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$this->key,$data,MCRYPT_MODE_CBC,"\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"));
    }
    private function decrypt($data){
        $decode = base64_decode($data);
        return mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$this->key,$decode,MCRYPT_MODE_CBC,"\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
    }
}
//ENCRYPT
$CriptoPHP = new CriptoPHP();
$CriptoPHP->encryptPHP("exampleScript");
//DECRYPT
$CriptoPHP->decryptAndInclude("exampleScript");
?>