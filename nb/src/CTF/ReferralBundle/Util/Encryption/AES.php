<?php

namespace CTF\ReferralBundle\Util\Encryption;

use CTF\ReferralBundle\Util\Encryption\PadCrypt;

/***
 * \CTF\ReferralBundle\Util\Encryption\AES
 * This class allows you to easily encrypt and decrypt text in AES format
 * The class automatically determines whether you need 128, 192, or 256 bits
 * based on your key size. It handles multiple padding formats.
 * 
 * Dependencies:
 * This class is dependent on PHP's mcrypt extension and a class called padCrypt
 * 
 * Information about mcrypt extension is at:
 * http://us.php.net/mcrypt
 * 
 * padCrypt class is published at:
 * http://dev.strategystar.net/2011/10/php-cryptography-padding-ansi-x-923-iso-10126-pkcs7-bit-zero/
 * 
 * The padCrypt class provides methods for padding strings with the 
 * common methods described at:
 * http://en.wikipedia.org/wiki/Padding_%28cryptography%29
 * 
 * -- AES_Encryption Information
 * 
 * Key Sizes:
 * 16 bytes = 128 bit encryption
 * 24 bytes = 192 bit encryption
 * 32 bytes = 256 bit encryption
 * 
 * Padding Formats:
 * ANSI_X.923
 * ISO_10126
 * PKCS7
 * BIT
 * ZERO
 * 
 * The default padding method in this AES_Encryption class is ZERO padding
 * ZERO padding is generally OK for paddings in messages because 
 * null bytes stripped at the end of a readable message should not hurt
 * the point of the text. If you are concerned about message integrity, 
 * you can use PKCS7 instead
 * 
 * This class does not generate keys or vectors for you. You have to 
 * generate them yourself because you need to keep track of them yourself 
 * anyway in order to decrypt AES encryptions.
 * 
 * -- Example Usage:
 * 
 * $key 	= "bac09c63f34c9845c707228b20cac5e0";
 * $iv 		= "47c743d1b21de03034e0842352ae6b98";
 * $message = "Meet me at 11 o'clock behind the monument.";
 * 
 * $AES              = new AES($key, $iv);
 * $encrypted        = $AES->encrypt($message);
 * $decrypted        = $AES->decrypt($encrypted);
 * $base64_encrypted = base64_encode($encrypted);
 * 
 * -- Credits:
 * 
 * @author Strategy Star Inc., amrith92
 * @website http://www.strategystar.net
 **/
class AES {

    private $key, $initVector, $mode, $cipher, $encryption = null;
    private $allowed_bits = array(128, 192, 256);
    private $allowed_modes = array('ecb', 'cfb', 'cbc', 'nofb', 'ofb');
    private $vector_modes = array('cbc', 'cfb', 'ofb');
    private $allowed_paddings = array(
        'ANSI_X.923' => 'ANSI_X923',
        'ISO_10126' => 'ISO_10126',
        'PKCS7' => 'PKCS7',
        'BIT' => 'BIT',
        'ZERO' => 'ZERO',
    );
    private $padCrypt_url = 'http://dev.strategystar.net/2011/10/php-cryptography-padding-ansi-x-923-iso-10126-pkcs7-bit-zero/';
    private $aesEncrypt_url = 'http://dev.strategystar.net/';

    /*     * *
     * String $key        = Your secret key that you will use to encrypt/decrypt
     * String $initVector = Your secret vector that you will use to encrypt/decrypt if using CBC, CFB, OFB, or a STREAM algorhitm that requires an IV
     * String $padding    = The padding method you want to use. The default is ZERO (aka NULL byte) [ANSI_X.923,ISO_10126,PKCS7,BIT,ZERO]
     * String $mode       = The encryption mode you want to use. The default is cbc [ecb,cfb,cbc,stream,nofb,ofb]
     * */

    public function __construct($key, $initVector = '', $padding = 'ZERO', $mode = 'cbc') {
        $mode = \strtolower($mode);
        $padding = \strtoupper($padding);

        if (!\function_exists('mcrypt_module_open')) {
            throw new \Exception('The mcrypt extension must be loaded.');
        }

        if (\strlen($initVector) != 16 && \in_array($mode, $this->vector_modes)) {
            throw new \Exception('The $initVector is supposed to be 16 bytes in for CBC, CFB, NOFB, and OFB modes.');
        } elseif (!\in_array($mode, $this->vector_modes) && !empty($initVector)) {
            throw new \Exception('The specified encryption mode does not use an initialization vector. You should pass an empty string, zero, FALSE, or NULL.');
        }

        $this->encryption = \strlen($key) * 8;

        if (!\in_array($this->encryption, $this->allowed_bits)) {
            throw new \Exception('The $key must be either 16, 24, or 32 bytes in length for 128, 192, and 256 bit encryption respectively.');
        }

        $this->key = $key;
        $this->initVector = $initVector;

        if (!\in_array($mode, $this->allowed_modes)) {
            throw new \Exception('The $mode must be one of the following: ' . \implode(', ', $this->allowed_modes));
        }

        if (!\array_key_exists($padding, $this->allowed_paddings)) {
            throw new \Exception('The $padding must be one of the following: ' . \implode(', ', $this->allowed_paddings));
        }

        $this->mode = $mode;
        $this->padding = $padding;
        $this->cipher = \mcrypt_module_open('rijndael-128', '', $this->mode, '');
        $this->block_size = \mcrypt_get_block_size('rijndael-128', $this->mode);
    }

    /*     * *
     * String $text = The text that you want to encrypt
     * */

    public function encrypt($text) {
        \mcrypt_generic_init($this->cipher, $this->key, $this->initVector);
        $encrypted_text = mcrypt_generic($this->cipher, $this->pad($text, $this->block_size));
        \mcrypt_generic_deinit($this->cipher);
        return $encrypted_text;
    }

    /*     * *
     * String $text = The text that you want to decrypt
     * */

    public function decrypt($text) {
        \mcrypt_generic_init($this->cipher, $this->key, $this->initVector);
        $decrypted_text = \mdecrypt_generic($this->cipher, $text);
        \mcrypt_generic_deinit($this->cipher);
        return $this->unpad($decrypted_text);
    }

    /*     * *
     * Use this function to export the key, init_vector, padding, and mode
     * This information is necessary to later decrypt an encrypted message
     * */

    public function getConfiguration() {
        return array(
            'key' => $this->key,
            'init_vector' => $this->initVector,
            'padding' => $this->padding,
            'mode' => $this->mode,
            'encryption' => $this->encryption . ' Bit',
            'block_size' => $this->block_size,
        );
    }

    private function pad($text, $block_size) {
        $func = 'pad_' . $this->allowed_paddings[$this->padding];
        return PadCrypt::$func($text, $block_size);
    }

    private function unpad($text) {
        $func = 'unpad_' . $this->allowed_paddings[$this->padding];
        return PadCrypt::$func($text);
    }

    public function __destruct() {
        \mcrypt_module_close($this->cipher);
    }

}
