<?php

namespace App\Jobs;

use App\Models\Version;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Process;

class deleteDbFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $version;
    protected $destination;
    protected $temporaryPath;

    public function __construct($version)
    {
        $this->version = $version;
        $version = Version::find($version);
        $this->temporaryPath = $version->profile->path_temp;
    }


    public function handle()
    {
        $name = "v$this->version";
        $cmd = "rm -f {$this->temporaryPath}{$name}.sql";
        (new Process($cmd))->run();
    }
}
