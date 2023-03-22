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

class SlsConfigs
{
    public static $map;
    public static string $file = ROOT_PATH . 'slsconfigs.yml';

    public function __construct()
    {
        if (is_null(self::$map) && file_exists(self::$file)) {
            self::$map = yaml_parse_file(self::$file);
        }
    }

    public function getConfigs(): array
    {
        return self::$map;
    }

    public function get(string $tag): array
    {
        return self::$map[$tag] ?? [];
    }
}
