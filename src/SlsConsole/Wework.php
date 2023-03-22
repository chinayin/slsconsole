<?php

/*
 * This file is part of the SlsConsole package.
 *
 * @link   https://github.com/chinayin/slsconsole
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SlsConsole;

class Wework
{
    /**
     * 获取用户信息
     *
     * @param string $code
     *
     * @return array
     */
    public function getUserInfo(string $code): array
    {
        if (empty($code)) {
            throw new \RuntimeException('wework.getUserInfo code is empty');
        }
        $url = sprintf(
            'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=%s&code=%s',
            $this->queryAccessToken(),
            $code
        );
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $url);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curlInit);
        curl_close($curlInit);
        $body = json_decode($result, true);
        $errCode = $body['errcode'] ?? '-1';
        $errMessage = $body['errmsg'] ?? '';
        if (!empty($errCode)) {
            throw new \RuntimeException('wework.getUserInfo fail');
        }
        return [
            'user_id' => $body['UserId'] ?? '',
            'open_id' => $body['OpenId'] ?? '',
        ];
    }

    private function queryAccessToken(): string
    {
        $configs = [
            'corpid' => Env::get('wework.corpid'),
            'secret' => Env::get('wework.secret'),
        ];
        return $this->queryAccessTokenForWework($configs);
    }

    /**
     * 获取token
     * @param array $options
     * @return string
     */
    public function queryAccessTokenForWework(array $options): string
    {
        $corpId = $options['corpid'] ?? '';
        $secret = $options['secret'] ?? '';
        if (empty($corpId) || empty($secret)) {
            throw new \RuntimeException('corpid or secret is empty');
        }
        // 远程获取
        $url = sprintf(
            'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s',
            $corpId,
            $secret
        );
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $url);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curlInit);
        curl_close($curlInit);
        $body = json_decode($result, true);
        $errCode = $body['errcode'] ?? '-1';
        $accessToken = $body['access_token'] ?? '';
        if (empty($accessToken)) {
            throw new \RuntimeException('wework.gettoken fail');
        }
        return $accessToken;
    }
}
