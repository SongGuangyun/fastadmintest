<?php

namespace app\extensions\wxsdk;

use think\Cache;
use think\Env;
use think\Log;

class WxRequestHandler
{
    //最多获取token次数
    protected $maxTokenCount = 3;

    //记录token获取了几次
    protected $logTokenCount = 0;

    // token缓存名称
    public static $wxAccessToken = 'wx-access-token';

    // token 缓存时间(秒)
    public static $wxAccessTokenTime = 7000;

    // 获取缓存实例
    private function getCacheHandler()
    {
        return Cache::store('redis');
    }

    /**
     * get 微信access_token
     */
    public function getCacheAccessToken($isRefresh = false)
    {
        if ($isRefresh === false) {
            $value = $this->getCacheHandler()->get(self::$wxAccessToken);
            if ($value) {
                return $value;
            }
        }
        $value = $this->getAccessToken();
        $this->getCacheHandler()->set(self::$wxAccessToken, $value, self::$wxAccessTokenTime);
        return $value;
    }

    /**
     * set 微信access_token
     */
    public function getAccessToken()
    {
        $result = $this->request(WxUrl::ACCESS_TOKEN, [
            'grant_type' => 'client_credential',
            'appid' => Env::get('wx.appid'),
            'secret' => Env::get('wx.secret'),
        ], 'GET');
        return $result['access_token'];
    }

    /**
     * 请求微信端
     * @param  [type]  $url     [description]
     * @param  array   $params  [description]
     * @param  string  $method  [description]
     * @param  boolean $isToken [description]
     * @return [type]           [description]
     */
    public function request($url, $params = [], $method = 'POST', $isToken = false)
    {
        try {
            $client = new \GuzzleHttp\Client();
            if ($method == 'GET') {
                if ($isToken === true) {
                    $params['access_token'] = $this->getCacheAccessToken();
                }
                $getUrl = $params ? $url . '?' . http_build_query($params) : $url;
                $res = $client->request($method, $getUrl);
            } else {
                if ($isToken === true) {
                    $getUrl = $url . '?access_token=' . $this->getCacheAccessToken();
                } else {
                    $getUrl = $url;
                }
                $res = $client->request($method, $getUrl, ['json' => $params]);
            }
            if ($res->getStatusCode() != 200) {
                throw new \Exception('请求出现错误');
            }
        } catch (\Exception $exception) {
            Log::error(__CLASS__ . ' 微信接口请求失败：' . json_encode($exception->getMessage()));
            return false;
            // throw $exception;
        }
        $result = json_decode($res->getBody()->getContents(), true);
        // 微信接口请求失败
        if (isset($result['errcode'])) {
            Log::error(__CLASS__ . ' 微信接口返回失败信息：' . json_encode($result));
            //token无效判断
            if ($result['errcode'] == WxErrorCode::TOKEN_INVALID) {
                if ($this->logTokenCount > $this->maxTokenCount) {
                    Log::error(__CLASS__ . ' 微信接口返回失败信息：' . json_encode($result));
                    return false;
                }
                $this->logTokenCount++;
                $this->getCacheAccessToken(true);
                return $this->request($url, $params, $method, $isToken);
            }
        }
        return $result;
    }
}
