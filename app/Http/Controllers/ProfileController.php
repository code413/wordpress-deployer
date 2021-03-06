<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Replacement;
use App\Models\Version;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $profiles = Profile::paginate(20);
        session()->flash('active', 'profile');

        return view('profiles.index', ['profiles' => $profiles]);
    }

    public function create()
    {
        session()->flash('active', 'profile');

        return view('profiles.create');
    }

    public function store(Request $request)
    {
        $this->validator($request);

        $normalizedRequest = $this->normalizeRequest($request);

        Profile::create([
            'name'        => $request->name,
            'user_id'     => auth()->user()->id,
            'db_name'     => 'db-name',
            'db_user'     => $request->db_user,
            'db_password' => $this->encrypt($request->db_password),
            'db_host'     => $request->db_host,
            'path_from'   => $normalizedRequest['path_from'],
            'path_to'     => storage_path('backup/'),
            'symlink'     => $normalizedRequest['path_symlink'],
            'path_temp'   => storage_path('dbBackup/'),
            'options'     => json_encode($request->only(['disable_maintenance', 'enable_gtm', 'enable_indexing'])),
            'gtm_id'      => $request->gtm_id,
            'gtm_auth'      => $request->gtm_auth,
            'gtm_preview'      => $request->gtm_preview,
        ]);

        return redirect()->action('ProfileController@index')
            ->with('message', 'Profile created successfully ');
    }

    public function edit(Profile $profile)
    {
        session()->flash('active', 'profile');

        return view('profiles.edit')->with(['profile' => $profile]);
    }

    public function update(Request $request, Profile $profile)
    {
        $this->validator($request);

        $normalizedRequest = $this->normalizeRequest($request);

        $profile->update([
            'name'        => $request->name,
            'db_name'     => 'db-name',
            'db_user'     => $request->db_user,
            'db_password' => $this->encrypt($request->db_password),
            'db_host'     => $request->db_host,
            'path_from'   => $normalizedRequest['path_from'],
            'symlink'     => $normalizedRequest['path_symlink'],
            'options'     => json_encode($request->only(['disable_maintenance', 'enable_gtm', 'enable_indexing'])),
            'gtm_id'      => $request->gtm_id,
            'gtm_auth'      => $request->gtm_auth,
            'gtm_preview'      => $request->gtm_preview,
        ]);

        return redirect()->action('ProfileController@index')
            ->with('message', 'Profile updated successfully ');
    }

    public function destroy(Profile $profile)
    {
        Version::where('profile_id', $profile->id)->delete();
        Replacement::where('profile_id', $profile->id)->delete();
        $profile->delete();

        return redirect()->action('ProfileController@index')->with('message', 'Profile deleted successfully');
    }

    protected function encrypt($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC')
    {
        // hash
        $key = hash('sha256', $key);
        // create iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);
        // encrypt
        $output = openssl_encrypt($string, $method, $key, 0, $iv);
        // encode
        return base64_encode($output);
    }

    protected function normalizeRequest(Request $request)
    {
        $path_from = Str::startsWith($request->path_from, '/') ? $request->path_from : '/'.$request->path_from;

        $path_symlink = Str::startsWith($request->symlink, '/') ? $request->symlink : '/'.$request->symlink;

        $path_from = Str::finish($path_from, '/');
        $path_symlink = Str::endsWith($path_symlink, '/') ? substr($path_symlink, 0, -1) : $path_symlink;

        return compact('path_from', 'path_to', 'path_temp', 'path_symlink');
    }



    public function validator(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'db_user'     => 'required',
            'db_password' => 'required',
            'db_host'     => 'required',
            'path_from'   => 'required',
            'symlink'     => 'required',
        ]);
    }
}
