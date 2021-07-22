<?php

namespace app\extensions\wxsdk;

use think\Env;
use think\Log;
use app\support\Str;

class WxService
{
    //请求对象
    protected $requestHandler;

    protected $baseUrl = 'https://api.weixin.qq.com/sns';
    protected $secret_key = 'DSADA';
    protected $notify_url = '';

    public function __construct()
    {
        $this->requestHandler = new WxRequestHandler();
        $this->wx_appid = Env::get('wx.appid');
        $this->wx_secret = Env::get('wx.secret');
    }

    /**
     * 获取网页授权token
     * @param  [type] $code code
     */
    public function getOauth2AccessToken($code)
    {
        $params = [
            'appid' => $this->wx_appid,
            'secret' => $this->wx_secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $response = $this->requestHandler->request($this->baseUrl . '/oauth2/access_token', $params, 'GET');
        if (array_key_exists('errcode', $response)) {
            Log::error(__CLASS__ . ' getOauth2AccessToken failed ' . json_encode($response));
            return false;
        }
        return $response;
    }

    /**
     * 获取用户信息
     * @param  [type] $code [description]
     * @return [type]       [description]
     */
    public function getWxUserInfo($code)
    {
        $access_data = $this->getOauth2AccessToken($code);
        if (!$access_data) {
            return false;
        }
        $params = [
            'access_token' => $access_data['access_token'],
            'openid' => $access_data['openid'],
            'lang' => 'zh_CN',
        ];
        $response = $this->requestHandler->request($this->baseUrl . '/userinfo', $params, 'GET');
        return $response;
    }

    /**
     * 登录凭证校验（微信小程序登录）
     * @param  string $code 登录时获取的 code
     */
    public function code2Session(string $code)
    {
        $params = [
            'appid' => $this->wx_appid,
            'secret' => $this->wx_secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        ];
        $response = $this->requestHandler->request($this->baseUrl . '/jscode2session', $params, 'GET');
        if (array_key_exists('errcode', $response)) {
            Log::error(__CLASS__ . ' code2Session failed ' . json_encode($response));
            return false;
        }
    }

    // 获取基础accesstoken
    public function getCacheAccessToken()
    {
        return $this->requestHandler->getCacheAccessToken();
    }

    public function unify(array $params)
    {
        if (empty($params['spbill_create_ip'])) {
            $params['spbill_create_ip'] = ('NATIVE' === $params['trade_type']) ? Str::get_server_ip() : Str::get_client_ip();
        }
        $params['appid'] = $this->wx_appid;
        $params['notify_url'] = $params['notify_url'] ?? $this->notify_url;
        $params['sign'] = Str::generate_sign($params, $this->secret_key);
        return $this->requestHandler->request($this->baseUrl . 'pay/unifiedorder', $params);
    }

}
