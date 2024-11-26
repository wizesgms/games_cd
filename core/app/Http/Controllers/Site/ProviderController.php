<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ApiActive;
use App\Models\ApiProvider;

class ProviderController extends Controller
{
    public function index()
    {
        $pageTitle = "Api Settings";
        $apia = ApiActive::First();
        $api = ApiProvider::all();
        return view('site.api',compact('pageTitle','api','apia'));
    }

    public function edit($id , Request $request)
    {
        $api = ApiProvider::find($id);
        $api->apikey = $request->apikey;
        $api->secretkey = $request->secretkey;
        $api->url = $request->url;
        $api->save();

        return back();
    }

    public function gunakan($id)
    {
        $api = ApiProvider::find($id);
        $apia = ApiActive::First();
        $apia->provider_id = $api->id;
        $apia->title = $api->provider;
        $apia->save();

        return back();
    }
}
