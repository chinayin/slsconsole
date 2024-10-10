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

use AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleRequest;
use Darabonba\OpenApi\Models\Config;

class Sts
{
    private string $ns;
    private \AlibabaCloud\SDK\Sts\V20150401\Sts $client;

    public function __construct($suffix = '')
    {
        $ns = 'sls' . (empty($suffix) ? '' : "_$suffix");
        $this->ns = $ns;
        $config = new Config([
            'accessKeyId' => $this->getEnvValue('access_key_id'),
            'accessKeySecret' => $this->getEnvValue('access_key_secret'),
            'regionId' => 'cn-beijing'
        ]);
        $this->client = new \AlibabaCloud\SDK\Sts\V20150401\Sts($config);
    }

    private function getEnvValue(string $key)
    {
        return Env::get("{$this->ns}.{$key}");
    }

    public function getRoleArn(): string
    {
        return $this->getEnvValue("role_arn");
    }

    /**
     * @url https://api.aliyun.com/api/Sts/2015-04-01/AssumeRole?RegionId=cn-beijing
     */
    public function assumeRole(int $durationSeconds = 3600): array
    {
        $assumeRoleRequest = new AssumeRoleRequest();
        $assumeRoleRequest->durationSeconds = $durationSeconds;
        $assumeRoleRequest->roleArn = $this->getRoleArn();
        $roleSessionName = 'sls-console-session' . time();
        $assumeRoleRequest->roleSessionName = $roleSessionName;
        $response = $this->client->assumeRole($assumeRoleRequest);
        return $response->body->toMap();
    }


}
