<?php

if (!file_exists(__DIR__ . '/src')) {
    exit(0);
}

$fileHeaderComment = <<<'EOF'
This file is part of the SlsConsole package.

@link   https://github.com/chinayin/slsconsole

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return (new PhpCsFixer\Config())
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PHP71Migration' => true,
        '@PHPUnit75Migration:risky' => true,
        'header_comment' => ['header' => $fileHeaderComment],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->ignoreVCSIgnored(true)
            ->files()
            ->name('*.php')
            ->exclude('vendor')
            ->in(__DIR__)
    );
