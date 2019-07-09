<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class createSymlink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $version;
    protected $destination;
    protected $targetSymlink;
    protected $name;

    public function __construct($version)
    {
        $this->version = $version;
        $this->destination = $version->profile->path_to;
        $this->targetSymlink = $version->profile->symlink;
        $this->name = "v{$version->id}";
    }

    public function handle()
    {
        /*Check if deploying from old version directory or not*/
        if (is_dir("{$this->destination}{$this->name}x")) {
            rename("{$this->destination}{$this->name}x", "{$this->destination}{$this->name}");
        }
        /*Check symlink directory exists or not, then rename the old directory*/
        if (is_dir($this->targetSymlink) && !is_link("{$this->targetSymlink}")) {
            rename("{$this->targetSymlink}", "{$this->targetSymlink}x");
        }

        /*Create new symlink*/
        $cmd = "ln -snf {$this->destination}{$this->name} {$this->targetSymlink}";
        (new Process($cmd))->run();
    }
}
