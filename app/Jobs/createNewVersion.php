<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Models\Version;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class createNewVersion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }


    public function handle()
    {
        return Version::create(['profile_id'=>$this->profile->id]);
    }
}
