<?php

namespace App\Http\Controllers;

use App\Jobs\activate;
use App\Jobs\createSymlink;
use App\Jobs\renameDirectory;
use App\Models\Version;

class DeploymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Version $version)
    {
        try {
            session()->flash('active', 'profile');
            dispatch(new createSymlink($version));
            dispatch(new renameDirectory($version));
            dispatch(new activate($version->id));
        } catch (\Exception $e) {
            return back()->with('error', "Version $version->id deploy failed.".$e->getMessage());
        }

        return back()->with('message', "Version $version->id deployed successfully.");
    }
}
