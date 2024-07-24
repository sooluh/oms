<?php

namespace App\Console\GitHooks;

use Closure;
use Igorsgm\GitHooks\Contracts\PreCommitHook;
use Igorsgm\GitHooks\Git\ChangedFiles;
use Igorsgm\GitHooks\Traits\ProcessHelper;

class PrettierPreCommitHook implements PreCommitHook
{
    use ProcessHelper;

    public function getName(): ?string
    {
        return 'Prettier';
    }

    public function handle(ChangedFiles $files, Closure $next)
    {
        $this->runCommands('node_modules/.bin/prettier --write ./resources/js ./resources/scss');
        $this->runCommands('git add -A');

        return $next($files);
    }
}
