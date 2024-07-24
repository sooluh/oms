<?php

return [
    'pre-commit' => [
        \Igorsgm\GitHooks\Console\Commands\Hooks\PintPreCommitHook::class, // Laravel Pint
        \App\Console\GitHooks\PrettierPreCommitHook::class, // Prettier
        \App\Console\GitHooks\VitePreCommitHook::class, // Vite Build
    ],
    'prepare-commit-msg' => [],
    'commit-msg' => [],
    'post-commit' => [],
    'pre-rebase' => [],
    'post-rewrite' => [],
    'post-checkout' => [],
    'post-merge' => [],
    'pre-push' => [],
    'code_analyzers' => [
        'laravel_pint' => [
            'path' => env('LARAVEL_PINT_PATH', 'vendor/bin/pint'),
            'config' => env('LARAVEL_PINT_CONFIG'),
            'preset' => env('LARAVEL_PINT_PRESET', 'laravel'),
            'file_extensions' => env('LARAVEL_PINT_FILE_EXTENSIONS', '/\.php$/'),
            'run_in_docker' => env('LARAVEL_PINT_RUN_IN_DOCKER', false),
            'docker_container' => env('LARAVEL_PINT_DOCKER_CONTAINER', ''),
        ],
        'php_code_sniffer' => [
            'phpcs_path' => env('PHPCS_PATH', 'vendor/bin/phpcs'),
            'phpcbf_path' => env('PHPCBF_PATH', 'vendor/bin/phpcbf'),
            'config' => env('PHPCS_STANDARD_CONFIG', 'phpcs.xml'),
            'file_extensions' => env('PHPCS_FILE_EXTENSIONS', '/\.php$/'),
            'run_in_docker' => env('LARAVEL_PHPCS_RUN_IN_DOCKER', false),
            'docker_container' => env('LARAVEL_PHPCS_DOCKER_CONTAINER', ''),
        ],
        'larastan' => [
            'path' => env('LARASTAN_PATH', 'vendor/bin/phpstan'),
            'config' => env('LARASTAN_CONFIG', 'phpstan.neon'),
            'additional_params' => env('LARASTAN_ADDITIONAL_PARAMS', '--xdebug'),
            'run_in_docker' => env('LARAVEL_LARASTAN_RUN_IN_DOCKER', false),
            'docker_container' => env('LARAVEL_LARASTAN_DOCKER_CONTAINER', ''),
        ],
        'blade_formatter' => [
            'path' => env('BLADE_FORMATTER_PATH', 'node_modules/.bin/blade-formatter'),
            'config' => env('BLADE_FORMATTER_CONFIG', '.bladeformatterrc.json'),
            'file_extensions' => env('BLADE_FORMATTER_FILE_EXTENSIONS', '/\.blade\.php$/'),
            'run_in_docker' => env('BLADE_FORMATTER_RUN_IN_DOCKER', false),
            'docker_container' => env('BLADE_FORMATTER_DOCKER_CONTAINER', ''),
        ],
        'prettier' => [
            'path' => env('PRETTIER_PATH', 'node_modules/.bin/prettier'),
            'config' => env('PRETTIER_CONFIG', '.prettierrc.json'),
            'additional_params' => env('PRETTIER_ADDITIONAL_PARAMS', ''),
            'file_extensions' => env('PRETTIER_FILE_EXTENSIONS', '/\.(jsx?|tsx?|vue)$/'),
            'run_in_docker' => env('PRETTIER_RUN_IN_DOCKER', false),
            'docker_container' => env('PRETTIER_DOCKER_CONTAINER', ''),
        ],
        'eslint' => [
            'path' => env('ESLINT_PATH', 'node_modules/.bin/eslint'),
            'config' => env('ESLINT_CONFIG', '.eslintrc.js'),
            'additional_params' => env('ESLINT_ADDITIONAL_PARAMS', ''),
            'file_extensions' => env('ESLINT_FILE_EXTENSIONS', '/\.(jsx?|tsx?|vue)$/'),
            'run_in_docker' => env('ESLINT_RUN_IN_DOCKER', false),
            'docker_container' => env('ESLINT_DOCKER_CONTAINER', ''),
        ],
    ],
    'artisan_path' => base_path('artisan'),
    'output_errors' => false,
    'automatically_fix_errors' => false,
    'rerun_analyzer_after_autofix' => false,
    'stop_at_first_analyzer_failure' => true,
    'debug_commands' => false,
];
