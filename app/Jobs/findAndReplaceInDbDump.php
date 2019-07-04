<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class findAndReplaceInDbDump implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    protected $profile;
    public function __construct($path,Profile $profile)
    {
        $this->path = $path;
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->profile->replacements()->where('type','Database')->get() as $replacement)
        {
            $file = file_get_contents($this->path);
            $file = str_replace($replacement->from, $replacement->to, $file);
            file_put_contents($this->path, $file);
        }
    }
}
