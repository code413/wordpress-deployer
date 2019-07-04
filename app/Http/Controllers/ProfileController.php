<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Replacement;
use App\Models\Version;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $profiles = Profile::paginate(20);
        session()->flash('active','profile');
        return view('profiles.index',['profiles'=>$profiles]);
    }


    public function create()
    {
        session()->flash('active','profile');
        return view('profiles.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_password' => 'required',
            'db_host' => 'required',
            'path_from' => 'required',
            'path_to' => 'required',
            'symlink' => 'required',
            'path_temp' => 'required',

        ]);

        Profile::create([
            'name'=>$request->name,
            'user_id'=>auth()->user()->id,
            'db_name'=>$request->db_name,
            'db_user'=>$request->db_user,
            'db_password'=>$this->encrypt($request->db_password),
            'db_host'=>$request->db_host,
            'path_from'=>substr($request->path_from,-1) == '/' ? $request->path_from : $request->path_from.'/',
            'path_to'=>substr($request->path_to,-1) == '/' ? $request->path_to : $request->path_to.'/',
            'symlink'=>substr($request->path_temp,-1) != '/' ? $request->symlink : substr($request->symlink,0,-1),
            'path_temp'=>substr($request->path_temp,-1) == '/' ? $request->path_temp : $request->path_temp.'/',
            'options'=>json_encode($request->only(['disable_maintenance','enable_gtm','enable_indexing'])),
        ]);

        return redirect()->action('ProfileController@index')
            ->with('message', 'Profile created successfully ');

    }


    public function edit(Profile $profile)
    {
        session()->flash('active','profile');
        return view('profiles.edit')->with(['profile'=>$profile]);
    }


    public function update(Request $request, Profile $profile)
    {

        $request->validate([
            'name' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_password' => 'required',
            'db_host' => 'required',
            'path_from' => 'required',
            'path_to' => 'required',
            'symlink' => 'required',
            'path_temp' => 'required',
        ]);

        $path_from = startsWith('/', $request->path_from) ?  $request->path_from : '/'.$request->path_from;
        $path_to = startsWith('/', $request->path_to) ?  $request->path_to : '/'.$request->path_to;
        $path_temp = startsWith('/', $request->path_temp) ?  $request->path_temp : '/'.$request->path_temp;
        $path_symlink = startsWith('/', $request->symlink) ?  $request->symlink : '/'.$request->symlink;

        $path_from = endsWith('/', $path_from) ?  $path_from : $path_from.'/';
        $path_to = endsWith('/', $path_to) ?  $path_to : $path_to.'/';
        $path_temp = endsWith('/', $path_temp) ?  $path_temp : $path_temp.'/';
        $path_symlink = substr($path_symlink,-1) != '/' ? $path_symlink : substr($path_symlink,0,-1);

        $profile->update([
            'name'=>$request->name,
            'db_name'=>$request->db_name,
            'db_user'=>$request->db_user,
            'db_password'=>$this->encrypt($request->db_password),
            'db_host'=>$request->db_host,
            'path_from'=>$path_from,
            'path_to'=>$path_to,
            'symlink'=>$path_symlink,
            'path_temp'=>$path_temp,
            'options'=>json_encode($request->only(['disable_maintenance','enable_gtm','enable_indexing'])),
        ]);

        return redirect()->action('ProfileController@index')
            ->with('message', 'Profile updated successfully ');
    }

    public function destroy(Profile $profile)
    {

        Version::where('profile_id',$profile->id)->delete();
        Replacement::where('profile_id',$profile->id)->delete();
        $profile->delete();

        return redirect()->action('ProfileController@index')->with('message','Profile deleted successfully');
    }

    protected function encrypt($string, $key = 'PrivateKey', $secret = 'SecretKey', $method = 'AES-256-CBC') {
        // hash
        $key = hash('sha256', $key);
        // create iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret), 0, 16);
        // encrypt
        $output = openssl_encrypt($string, $method, $key, 0, $iv);
        // encode
        return base64_encode($output);
    }

}
