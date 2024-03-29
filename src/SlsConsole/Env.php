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

class Env
{
    /**
     * 获取环境变量值
     *
     * @access public
     *
     * @param string $name 环境变量名（支持二级 . 号分割）
     * @param null|string $default 默认值
     *
     * @return mixed
     */
    public static function get(string $name, ?string $default = null)
    {
        $result = getenv(ENV_PREFIX . strtoupper(str_replace('.', '_', $name)));
        if (false !== $result) {
            if ('false' === $result) {
                $result = false;
            } elseif ('true' === $result) {
                $result = true;
            }
            return $result;
        }
        return $default;
    }
}
