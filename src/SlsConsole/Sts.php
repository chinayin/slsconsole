<?php

namespace SlsConsole;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Sts
{
    private $ns;

    public function __construct($suffix = '')
    {
        $ns = 'sls' . (empty($suffix) ? '' : "_$suffix");
        $this->ns = $ns;
        try {
            AlibabaCloud::accessKeyClient(
                Env::get("$ns.access_key_id"),
                Env::get("$ns.access_key_secret")
            )->regionId('cn-beijing')->asDefaultClient();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function getRoleArn(): string
    {
        return Env::get("{$this->ns}.role_arn", '');
    }

    public function assumeRole(
        string $roleArn,
        int $durationSeconds = 3600,
        string $roleSessionName = '',
        string $policy = ''
    ) {
        try {
            $response = AlibabaCloud::sts()->V20150401()->assumeRole()
                ->withDurationSeconds($durationSeconds)
                ->withRoleArn($roleArn)
                ->withRoleSessionName($roleSessionName)
//                ->withPolicy($policy)
                ->request();
            return $response->toArray();
        } catch (ServerException $e) {
            var_dump($e->getErrorMessage());
        } catch (ClientException $e) {
            var_dump($e->getErrorMessage());
        }
        return false;
    }
}
