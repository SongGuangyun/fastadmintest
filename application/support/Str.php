<?php

namespace app\support;

class Str
{
    public static function limit($value, $limit = 100, $end = '...')
    {
        // 返回字符串的宽度
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }
        // mb_strimwidth 获取按指定宽度截断的字符串
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }

    public static function length($value, $encoding = null)
    {
        if ($encoding) {
            return mb_strlen($value, $encoding);
        }
        return mb_strlen($value);
    }

    public static function random($length = 16)
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    public static function rsa_public_encrypt($content, $publicKey)
    {
        $encrypted = '';
        openssl_public_encrypt($content, $encrypted, openssl_pkey_get_public($publicKey), OPENSSL_PKCS1_OAEP_PADDING);

        return base64_encode($encrypted);
    }

    /**
     * 维信 获取签名
     * @param  array  $attributes    [description]
     * @param  [type] $key           [description]
     * @param  string $encryptMethod [description]
     * @return [type]                [description]
     */
    public static function generate_sign(array $attributes, $key, $encryptMethod = 'md5')
    {
        ksort($attributes);

        $attributes['key'] = $key;
        return strtoupper(call_user_func_array($encryptMethod, [urldecode(http_build_query($attributes))]));
    }

    public static function get_client_ip()
    {
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            // for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }

    public static function get_server_ip()
    {
        if (!empty($_SERVER['SERVER_ADDR'])) {
            $ip = $_SERVER['SERVER_ADDR'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
        } else {
            // for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }

}
