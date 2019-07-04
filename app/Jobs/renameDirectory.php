<?php

namespace App\Jobs;

use App\Models\Version;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Process;

class renameDirectory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $version;
    protected $destination;


    public function __construct(Version $version)
    {
        $this->version = $version;
        $this->destination = $version->profile->path_to;
    }


    public function handle()
    {
//        dd($this->version);
        $version = Version::where('is_active',1)->where('profile_id',$this->version->profile_id)->first();
        if (isset($version) && $version->id != $this->version->id)
        {
            $cmd = "mv {$this->destination}v{$version->id} {$this->destination}v{$version->id}x";
            (new Process($cmd))->run();
        }
    }
}
