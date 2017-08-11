<?php

if (!defined('_PS_VERSION_'))
    exit;

/**
 * Class MTTools
 */
class MTTools
{
    /**
     * @param $plain_text
     * @param $key
     *
     * @return string
     */
    public static function encrypt($plain_text, $key)
    {
        $key = str_pad($key, 32, "\0");

        $plain_text = trim( $plain_text );
        # create a random IV to use with CBC encoding
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        # creates a cipher text compatible with AES (Rijndael block size = 128)
        # to keep the text confidential
        # only suitable for encoded input that never ends with value 00h (because of default zero padding)
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
            $plain_text, MCRYPT_MODE_CBC, $iv);

        # prepend the IV for it to be available for decryption
        $ciphertext = $iv . $ciphertext;

        # encode the resulting cipher text so it can be represented by a string
        $sResult = base64_encode($ciphertext);
        return trim( $sResult );
    }

    /**
     * @param $encoded_text
     * @param $key
     * @return string
     */
    public static function decrypt($encoded_text, $key)
    {
        if (strlen($key) == 30)
            $key .= "\0\0";

        $encoded_text = trim($encoded_text);
        $ciphertext_dec = base64_decode($encoded_text);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);

        $ciphertext_dec = substr($ciphertext_dec, $iv_size);
        $sResult = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        return trim($sResult);
    }
}
