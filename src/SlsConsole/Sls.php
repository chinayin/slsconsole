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

use AlibabaCloud\SDK\Sls\V20201230\Models\CreateTicketRequest;
use Darabonba\OpenApi\Models\Config;

class Sls
{
    private Config $config;
    private \AlibabaCloud\SDK\Sls\V20201230\Sls $client;

    public function __construct(Config $config = null)
    {
        $this->config = $config;
        $this->client = new \AlibabaCloud\SDK\Sls\V20201230\Sls($this->config);
    }

    /**
     * @param int $expirationSeconds
     * @param int $accessTokenExpirationSeconds
     * @return string
     * @url https://api.aliyun.com/api/Sls/2020-12-30/CreateTicket?sdkStyle=dara&RegionId=cn-beijing&tab=DEMO&lang=PHP
     */
    public function createTicket(int $expirationSeconds = 86400, int $accessTokenExpirationSeconds = 86400): string
    {
        $createTicketRequest = new CreateTicketRequest();
        $createTicketRequest->expirationTime = $expirationSeconds;
        $createTicketRequest->accessTokenExpirationTime = $accessTokenExpirationSeconds;

        //        try {
        $response = $this->client->createTicket($createTicketRequest);
        return $response->body->ticket;
        //        } catch (\Exception $ex) {
        //            if (!($ex instanceof TeaError)) {
        //                $ex = new TeaError([], $ex->getMessage(), $ex->getCode(), $ex);
        //            }
        //            //("ERROR::createTicket: {$ex->code}, {$ex->message}");
        //        }
    }

    /**
     * @param string $projectName
     * @param string $logstoreName
     * @param array $options
     * @return string
     *
     * @url https://help.aliyun.com/zh/sls/developer-reference/console-embedding-and-sharing-new-version-2
     */
    public function buildEmbedUrl(string $region, string $projectName, string $logstoreName, array $options = []): string
    {
        $ticket = $this->createTicket();
        if (empty($ticket)) {
            throw new AppException('buildEmbedUrl, create ticket failed.');
        }
        // https://help.aliyun.com/document_detail/103028.html
        $params = array_merge(
            [
                'slsRegion' => $region,
                'sls_ticket' => $ticket,
            ],
            [
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
            ],
            $options
        );
        return "https://sls.console.aliyun.com/lognext/project/$projectName/logsearch/$logstoreName?" . http_build_query($params);
    }


    /**
     *
     * 老版本获取登陆地址
     *
     * @param string $projectName
     * @param string $logstoreName
     * @param array $options
     * @return string
     * @deprecated
     *
     */
    public function buildSigninUrl(string $projectName, string $logstoreName, array $options = []): string
    {
        // construct get token url
        $signInHost = "https://signin.aliyun.com";
        $signInTokenUrl = "{$signInHost}/federation?" . http_build_query([
                'Action' => 'GetSigninToken',
                'AccessKeyId' => $this->config->accessKeyId,
                'AccessKeySecret' => $this->config->accessKeySecret,
                'SecurityToken' => $this->config->securityToken,
                'TicketType' => 'mini',
            ]);
        // request signin
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $signInTokenUrl);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curlInit);
        curl_close($curlInit);
        $signInTokenJson = json_decode($result, true);
        $signInToken = $signInTokenJson['SigninToken'] ?? '';
        if (empty($signInToken)) {
            throw new AppException('sls.signInToken fail');
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
        return "{$signInHost}/federation?Action=Login"
            . "&LoginUrl=" . urlencode($loginUrl)
            . "&Destination=" . urlencode($destination)
            . "&SigninToken=" . urlencode($signInToken);
    }
}
