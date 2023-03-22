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

class Sls
{
    public function buildSigninUrl(string $projectName, string $logstoreName, array $options = []): string
    {
        $suffix = '';
        if (isset($options['ns']) && !empty($options['ns'])) {
            $suffix = $options['ns'];
            unset($options['ns']);
        }
        $durationSeconds = 3600;
        $sts = new Sts($suffix);
        $roleArn = $sts->getRoleArn();
        $roleSessionName = 'sls-console-session' . time();
        $response = $sts->assumeRole($roleArn, $durationSeconds, $roleSessionName);
        if (empty($response)) {
            throw new \LogicException('sts.assumeRole fail');
        }
        // construct get token url
        $signInHost = "https://signin.aliyun.com";
        $signInTokenUrl = $signInHost . "/federation?Action=GetSigninToken"
            . "&AccessKeyId=" . urlencode($response['Credentials']['AccessKeyId'] ?? '')
            . "&AccessKeySecret=" . urlencode($response['Credentials']['AccessKeySecret'] ?? '')
            . "&SecurityToken=" . urlencode($response['Credentials']['SecurityToken'] ?? '')
            . "&TicketType=mini";
        // request signin
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $signInTokenUrl);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curlInit);
        curl_close($curlInit);
        $signInTokenJson = json_decode($result, true);
        $signInToken = $signInTokenJson['SigninToken'] ?? '';
        if (empty($signInToken)) {
            throw new \LogicException('sls.signInToken fail');
        }
        // https://help.aliyun.com/document_detail/103028.html
        $slsParams = array_merge([
            // 隐藏侧边导航栏
            'hideSidebar' => 'true',
            // 隐藏顶部阿里云标题栏
            'hideTopbar' => 'true',
            // 隐藏控制台首页返回按钮
            'hiddenBack' => 'true',
            // 隐藏切换Project功能
            'hiddenChangeProject' => 'true',
            // 隐藏Project概览入口
            'hiddenOverview' => 'true',
            // 关闭Tab访问的历史记录
            'ignoreTabLocalStorage' => 'true',
            //
            // 隐藏编辑、修改按钮，例如分享、查询分析属性，另存为快速查询、另存为告警等
            'readOnly' => 'true',
            // 隐藏数据加工按钮
            'hiddenEtl' => 'true',
            // 隐藏分享按钮
            'hiddenShare' => 'true',
        ], $options);
        $destination = "https://sls4service.console.aliyun.com/lognext/project/$projectName/logsearch/$logstoreName?"
            . http_build_query($slsParams);
        // construct final url
        $loginUrl = Env::get('sls.login_url');
        return $signInHost . "/federation?Action=Login"
            . "&LoginUrl=" . urlencode($loginUrl)
            . "&Destination=" . urlencode($destination)
            . "&SigninToken=" . urlencode($signInToken);
    }
}
