<?php

namespace App\Console\GitHooks;

use Closure;
use Igorsgm\GitHooks\Contracts\PreCommitHook;
use Igorsgm\GitHooks\Git\ChangedFiles;
use Igorsgm\GitHooks\Traits\ProcessHelper;

class VitePreCommitHook implements PreCommitHook
{
    use ProcessHelper;

    public function getName(): ?string
    {
        return 'Build Asset';
    }

    public function handle(ChangedFiles $files, Closure $next)
    {
        $this->runCommands('node_modules/.bin/vite build');
        $this->runCommands('git add -A');

        return $next($files);
    }
}
