<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 04, 2010, 10:44 PM
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */


/**
 * Encrypt and decrypt class
 *
 *
 * @package   system
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class Crypter
{
    /**
     * Private key
     *
     * @var string
     */
    private $_key;
    
    
    /**
     * Constructor.
     *
     * Create an instance of this class
     *
     * @param string $key Private key
     *
     * @return void
     */
    public function __construct($key)
    {
        $this->_key = $key;  
    }
    
    /**
     * RC4 encrpyt
     *
     * @param string $data Data string to be encrypted
     * @param string $row Raw data
     * 
     * @return string RC4 encrypted string
     */
    public function rc4encrypt($data, $raw=0)
    {
        return $this->rc4endecrypt($data, 0, $raw);
    }

    /**
     * RC4 decrypt
     *
     * @param string $data Encrypted string to be decrypted
     * @param string $raw Raw string
     *
     * @return string Decrypted string
     */
    public function rc4decrypt($data, $raw=0)
    {
        return $this->rc4endecrypt($data, 1, $raw);
    }

    /**
     * RC4 encrypt / decrypt
     *
     * @param string $data Data string to be encrypted/decrypted
     * @param int $decrypt Flag for encrypt / decrypt
     * @param string $raw Raw string
     *
     * @return string RC4 encrypted/decrypted string
     */
    public function rc4endecrypt($data, $decrypt=0, $raw=0)
    {
        if (!$raw && $decrypt) $data = pack("H*", $data);
        
        $key        = array();
        $box        = array();
        $temp_swap  = "";
        $pwd_length = 0;

        $pwd_length = strlen($this->_key);

        for ($i = 0; $i <= 255; $i++) {
            $key[$i] = ord(substr($this->_key, ($i % $pwd_length), 1));
            $box[$i] = $i;
        }

        $x = 0;
        for ($i = 0; $i <= 255; $i++) {
            $x          = ($x + $box[$i] + $key[$i]) % 256;
            $temp_swap  = $box[$i];
            $box[$i]    = $box[$x];
            $box[$x]    = $temp_swap;
        }

        $temp       = "";
        $k          = "";
        $cipherby   = "";
        $cipher     = "";
        $a          = 0;
        $j          = 0;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $a          = ($a + 1) % 256;
            $j          = ($j + $box[$a]) % 256;
    
            $temp       = $box[$a];
            $box[$a]    = $box[$j];
            $box[$j]    = $temp;
    
            $k          = $box[(($box[$a] + $box[$j]) % 256)];
            $cipherby   = ord(substr($data, $i, 1)) ^ $k;
            $cipher    .= chr($cipherby);
        }
            
        if (!$raw && !$decrypt) $cipher = implode("", unpack("H*", $cipher));
        
        return $cipher;
    }
    
    /**
     * Encrypt string
     *
     * @param string $data String to be encrypted
     * @param int $raw Raw salt flag
     *
     * @return string Encrypted string
     */
    public function encrypt($data, $raw="")
    {
        $mt         = explode(" ",microtime());
        $time_stamp = $mt[1]+floor($mt[0]*1000000);
        $key        = sprintf("%012.0f",floor(((((($time_stamp%139968)*3877)%139968)+29573)%139968)/139968*1000000000000)).$this->_key;
        
        $str        = $this->rc4encrypt($data, $key , $raw);
        $salt       = sprintf("%08x",$time_stamp - (ord($str)*4000000+ord($str)));
        
        if ($raw) $salt = pack("H*", $salt);
    
        $str    = $salt.$str;
        
        return $str;
    }
    
    /**
     * Decrypt string
     *
     * @param string $data Encrypted string to be decrypted
     * @param int $raw Raw salt flag
     *
     * @return string Decrypted string
     */
    public function decrypt($data,  $raw="") {
        $salt       = ($raw ? implode("", unpack("H*", substr($data, 0, 4))) : substr($data, 0, 8));
        $data       = substr($data, ($raw ? 4 : 8));
        $time_stamp = hexdec($salt) + (ord($data)*4000000+ord($data));
        $key        = sprintf("%012.0f",floor(((((($time_stamp%139968)*3877)%139968)+29573)%139968)/139968*1000000000000)).$this->_key;
        
        $str        = $this->rc4decrypt($data, $key, $raw);
        
        return $str;
    }
}
?>