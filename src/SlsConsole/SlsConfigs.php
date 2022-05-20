<?php

namespace SlsConsole;

class SlsConfigs
{
    public static $map;
    public static $file = ROOT_PATH . 'slsconfigs.json5';

    public function __construct()
    {
        if (is_null(self::$map)) {
            if (file_exists(self::$file)) {
                self::$map = json5_decode(file_get_contents(self::$file), true);
            }
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
