<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Replacement;
use Illuminate\Http\Request;

class ReplacementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Profile $profile)
    {
        session()->flash('active', 'profile');
        $replacements = Replacement::where('profile_id', $profile->id)->get();

        return view('replacements.index', ['replacements'=>$replacements, 'profile'=>$profile]);
    }

    public function create(Profile $profile)
    {
        session()->flash('active', 'profile');

        return view('replacements.create', compact('profile'));
    }

    public function store(Request $request, Profile $profile)
    {
        $request->validate([
            'from'    => 'required',
            'to'      => 'required',
            'type'    => 'required',
            'path'    => 'required_if:type,File',
            'pattern' => 'required_if:type,File',
        ]);

        Replacement::create(
            [
                'from'       => $request->from,
                'to'         => $request->to,
                'type'       => $request->type,
                'path'       => $request->path,
                'pattern'    => $request->pattern,
                'profile_id' => $profile->id,
            ]
        );

        return redirect()->action('ReplacementController@index', ['profile'=>$profile->id])
            ->with('message', 'Replacement created successfully ');
    }

    public function edit(Replacement $replacement)
    {
        session()->flash('active', 'profile');

        return view('replacements.edit')->with(['replacement'=>$replacement]);
    }

    public function update(Request $request, Replacement $replacement)
    {
        $request->validate([
            'from'    => 'required',
            'to'      => 'required',
            'type'    => 'required',
            'path'    => 'required_if:type,File',
            'pattern' => 'required_if:type,File',
        ]);

        $replacement->update([
            'from'   => $request->from,
            'to'     => $request->to,
            'type'   => $request->type,
            'path'   => $request->path,
            'pattern'=> $request->pattern,
            ]);

        return redirect()->action('ReplacementController@index', ['profile'=>$replacement->profile_id])
            ->with('message', 'Replacements updated successfully ');
    }

    public function destroy(Replacement $replacement)
    {
        $profile = $replacement->profile_id;
        $replacement->delete();

        return redirect()->action('ReplacementController@index', ['profile'=>$profile]);
    }
}
