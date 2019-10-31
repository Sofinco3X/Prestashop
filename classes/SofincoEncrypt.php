<?php
/**
* Sofinco PrestaShop Module
*
* Feel free to contact Verifone at support@paybox.com for any
* question.
*
* LICENSE: This source file is subject to the version 3.0 of the Open
* Software License (OSL-3.0) that is available through the world-wide-web
* at the following URI: http://opensource.org/licenses/OSL-3.0. If
* you did not receive a copy of the OSL-3.0 license and are unable
* to obtain it through the web, please send a note to
* support@e-transactions.fr so we can mail you a copy immediately.
*
*  @category  Module / payments_gateways
*  @version   3.0.11
*  @author    BM Services <contact@bm-services.com>
*  @copyright 2012-2016 Sofinco
*  @license   http://opensource.org/licenses/OSL-3.0
*  @link      http://www.e-transactions.fr/
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class SofincoEncrypt
{
    /**
     * You can change this method if you want to use another key than the
     * one provided by PrestaShop.
     * @return string Key used for encryption
     */
    private function _getKey()
    {
        // $key = Configuration::get('PS_NEWSLETTER_RAND');
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $key = _NEW_COOKIE_KEY_;
        } else {
            $key = _RIJNDAEL_KEY_;
        }

        return $key;
    }
    /**
     * Encrypt $data using 3DES
     *
     * 3.0.11 Test mcrypt function / Add OpenSSL support
     *
     * @version  3.0.11
     * @param string $data The data to encrypt
     * @return string The result of encryption
     * @see Helper_Encrypt::_getKey()
     */
    public function encrypt($data)
    {
        if (empty($data)) {
            return '';
        }

        // Prepare key
        $key = $this->_getKey();
        $key = substr($key, 0, 24);
        while (strlen($key) < 24) {
            $key .= substr($key, 0, 24 - strlen($key));
        }

        if(function_exists('mcrypt_encrypt')) {
            // First encode data to base64 (see end of descrypt)
            $data = base64_encode($data);

            // Prepare mcrypt
            $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');

            // Init vector
            $size = mcrypt_enc_get_iv_size($td);
            $iv = mcrypt_create_iv($size, MCRYPT_RAND);
            mcrypt_generic_init($td, $key, $iv);

            // Encrypt
            $result = mcrypt_generic($td, $data);

            // Encode (to avoid data loose when saved to database or
            // any storage that does not support null chars)
            $result = base64_encode($result);
        } elseif(function_exists('openssl_encrypt')) {
            $ivlen = openssl_cipher_iv_length($cipher='AES-128-CBC');
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($data, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
            $result = base64_encode($iv.$hmac.$ciphertext_raw);
        } else {
            $result = $data;
        }

        return $result;
    }

    /**
     * Decrypt $data using 3DES
     *
     * 3.0.11 Test mcrypt function / Add OpenSSL support
     *
     * @version  3.0.11
     * @param string $data The data to decrypt
     * @return string The result of decryption
     * @see Helper_Encrypt::_getKey()
     */
    public function decrypt($data)
    {
        if (empty($data)) {
            return '';
        }

        // Prepare key
        $key = $this->_getKey();
        $key = substr($key, 0, 24);
        while (strlen($key) < 24) {
            $key .= substr($key, 0, 24 - strlen($key));
        }

        if(function_exists('mcrypt_encrypt')) {
            // First decode encrypted message (see end of encrypt)
            $data = base64_decode($data);

            // Prepare mcrypt
            $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');

            // Init vector
            $size = mcrypt_enc_get_iv_size($td);
            $iv = mcrypt_create_iv($size, MCRYPT_RAND);
            mcrypt_generic_init($td, $key, $iv);

            // Decrypt
            $result = mdecrypt_generic($td, $data);

            // Remove any null char (data is base64 encoded so no data loose)
            $result = rtrim($result, "\0");

            // Decode data
            $result = base64_decode($result);
        } elseif(function_exists('openssl_encrypt')) {
            $c = base64_decode($data);
            $ivlen = openssl_cipher_iv_length($cipher='AES-128-CBC');
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len=32);
            $ciphertext_raw = substr($c, $ivlen+$sha2len);
            $result = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);

            //PHP 5.6+ timing attack safe comparison
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
            if (!hash_equals($hmac, $calcmac)) {
                $result = '';
            }
        } else {
            $result = $data;
        }

        return $result;
    }
}
