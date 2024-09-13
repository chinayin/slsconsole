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

use Symfony\Component\Ldap\Exception\InvalidCredentialsException;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\Ldap;

class SecurityLdap
{
    private $ldap;
    private ?string $baseDn;

    public function __construct()
    {
        $dsn = Env::get('ldap.dsn');
        $this->baseDn = Env::get('ldap.base_dn');
        $this->ldap = Ldap::create('ext_ldap', ['connection_string' => $dsn]);
    }


    private function buildUserDn(string $username): string
    {
        return "uid=$username,{$this->baseDn}";
    }

    public function login(string $username, string $password): bool
    {
        try {
            $dn = $this->buildUserDn($username);
            $this->ldap->bind($dn, $password);
            return true;
        } catch (LdapException|InvalidCredentialsException $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }
        return false;
    }
}
