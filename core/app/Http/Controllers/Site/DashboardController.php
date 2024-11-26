<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AgentApi;
use App\Models\ApiActive;
use App\Models\ApiProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\UserPlayer;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Dashboard";

        if (auth()->user()->level == 1) {
            $player = UserPlayer::where('agent_code')->count();
        } else {
            $player = UserPlayer::where('agent_code', auth()->user()->agentcode)->count();
        }

        $user = User::where('level', '!=' , 1)->count();
        $player = UserPlayer::where('agent_code', auth()->user()->agentcode)->count();
        $game_count = DB::table('game_lists')->count();
        $provider_count = DB::table('provider_lists')->where('status',1)->count();
        return view('site.dashboard', compact('pageTitle','player','game_count','provider_count','user'));
    }

    public function generate(Request $request)
    {
        $provider = ApiProvider::find($request->provider);
        $agent = new AgentApi();
        $agent->agentcode = Str::random(7);
        $agent->secretkey = Str::random(10);
        $agent->signature = Str::random(32);
        $agent->provider = $request->provider;
        $agent->provider_name = $provider->provider;
        $agent->save();

        return back();
    }

    public function delete($id)
    {
        $agent = AgentApi::find($id);
        $agent->delete();

        return back();
    }

    public function balance()
    {
        $api = ApiActive::first();
        $apis = ApiProvider::find($api->provider_id);

        if ($api->provider_id == 1) {
            $endpoint = $apis->url;
            $postArray = [
                'method' => $apis->apikey,
                'agent_code' => $apis->apikey,
                'agent_token' => $apis->secretkey
            ];
        } else {
            $endpoint = $apis->url;
            $postArray = [
                'method' => 'money_info',
                'agent_code' => $apis->apikey,
                'agent_token' => $apis->secretkey
            ];
        }

        $jsonData = json_encode($postArray);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response);

        $balance = number_format($result->agent->balance, 2);
        return response()->json(['balance' => $balance]);
    }

    public function profile()
    {
        $pageTitle = "Profile";
        $user = auth()->user();
        return view('site.profile',compact('pageTitle','user'));
    }

    public function changepw(Request $request)
    {
        if ($request->NewPassword != $request->ConfirmPassword) {
            return back()->with('error','New password is not the same as the confirm password');
        } elseif (Hash::check($request->OldPassword,auth()->user()->password)) {
            $admin = User::find(auth()->user()->id);
            $admin->password = Hash::make($request->newPassword);
            $admin->save();
            return back()->with('success','Password Sucessfully update');
        } else {
            return back()->with('error','Current password invalid!');
        }
    }
}
