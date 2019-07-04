<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class findAndReplaceInDirectory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile;
    protected $name;
    protected $destination;
    public function __construct(Profile $profile,$name)
    {
        $this->profile = $profile;
        $this->name = $name;

    }


    public function handle()
    {
        foreach ($this->profile->replacements()->where('type','File')->get() as $replacement)
        {
            $this->replaceText($replacement->from, $replacement->to, $this->profile->path_to.$this->name.'/'.$replacement->path, $replacement->pattern ?? null);
        }
    }
}
