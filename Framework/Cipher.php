<?php
namespace Framework;

class Cipher
{

    public static function base64EncodeSafe($string)
    {
        $data = base64_encode($string);
        $data = str_replace(
            [
                '+',
                '/',
                '=',
            ],
            [
                '-',
                '_',
                '',
            ],
            $data
        );
        return $data;
    }

    public static function base64DecodeSafe($string)
    {
        $data = str_replace(
            [
                '-',
                '_',
            ],
            [
                '+',
                '/',
            ],
            $string
        );
        $mod4 = (strlen($data) % 4);
        if ($mod4) {
            $data .= substr('====', $mod4);
        }

        return base64_decode($data);
    }

    public static function encrypt($message, $key = 'DHEbSK1KmU3MQnuhmSQfDIM7adKaBIPMpzWB4GJw')
    {
        if (! $message) {
            return false;
        }

        if (mb_strlen($key, '8bit') < 32) {
            throw new \Exception("Needs a 256-bit key!");
        }

        $ivsize = openssl_cipher_iv_length('aes-256-cbc');
        $iv     = openssl_random_pseudo_bytes($ivsize);

        $ciphertext = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return trim(self::base64EncodeSafe($iv . $ciphertext));
    }

    public static function decrypt($message, $key = 'DHEbSK1KmU3MQnuhmSQfDIM7adKaBIPMpzWB4GJw')
    {
        if (! $message) {
            return false;
        }

        if (mb_strlen($key, '8bit') < 32) {
            throw new \Exception("Needs a 256-bit key!");
        }

        $message    = self::base64DecodeSafe($message);
        $ivsize     = openssl_cipher_iv_length('aes-256-cbc');
        $iv         = mb_substr($message, 0, $ivsize, '8bit');
        $ciphertext = mb_substr($message, $ivsize, null, '8bit');

        return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    }
}
