<?php

namespace SlsConsole;

class Limits
{
    public static $map;
    public static $file = ROOT_PATH . 'limits.json5';

    public function __construct()
    {
        if (is_null(self::$map)) {
            if (file_exists(self::$file)) {
                self::$map = json5_decode(file_get_contents(self::$file), true);
            }
        }
    }

    public function isPermission(string $user_id): bool
    {
        if (array_key_exists($user_id, self::$map ?? [])) {
            return true;
        }
        return false;
    }

    public function getPerms(string $user_id): array
    {
        return self::$map[$user_id] ?? [];
    }
}
