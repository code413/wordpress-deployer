<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if (is_dir("{$this->destination}{$this->name}x"))
        {

            rename("{$this->destination}{$this->name}x","{$this->destination}{$this->name}");
//            $cmd = "mv {$this->destination}{$this->name}x {$this->destination}{$this->name}";
//            (new Process($cmd))->run();
        }

        if (!is_link("{$this->targetSymlink}"))
        {
            rename("{$this->targetSymlink}", "{$this->targetSymlink}x");
        }


        $cmd = "ln -snf {$this->destination}{$this->name} {$this->targetSymlink}";
        (new Process($cmd))->run();
    }
}
