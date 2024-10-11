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

class Limits
{
    public static $map;
    public static string $file = ROOT_PATH . 'limits.yml';

    public function __construct()
    {
        if (is_null(self::$map) && file_exists(self::$file)) {
            self::$map =
                yaml_parse_file(self::$file) +
                (file_exists(self::$file . '.local') ? yaml_parse_file(self::$file . '.local') : []);
        }
    }

    public function isPermission(string $userId): bool
    {
        if (array_key_exists($userId, self::$map ?? [])) {
            return true;
        }
        return false;
    }

    public function getPerms(string $userId): array
    {
        return self::$map[$userId] ?? [];
    }
}
