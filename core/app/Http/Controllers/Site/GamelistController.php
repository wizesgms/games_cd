<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameList;
use App\Models\ProviderList;

class GamelistController extends Controller
{
    public function gamelist($provider)
    {
        $pageTitle = "Game List";
        $games = GameList::where('Provider', $provider)->get();
        return view('site.gamelist', compact('pageTitle', 'games'));
    }

    public function updateGame($id, Request $request)
    {
        $games = GameList::find($id);
        $games->GameImage = $request->image;
        $games->save();

        return redirect()->back();
    }

    public function updateImage($provider)
    {
        $games = GameList::where('Provider', $provider)->get();
        foreach ($games as $gms) {
            $lowerss = strtolower($provider);
            $rpl = str_replace("https://cloud.g11329.net/SlotImages/jili/","https://cloud.g11329.net/SlotImages/Jili/",$gms->GameImage);
            $gms->GameImage = $rpl;
            $gms->save();
        }

        return $rpl;
    }

    public function deleteGame($id, Request $request)
    {
        $games = GameList::find($id);
        $games->delete();

        return redirect()->back();
    }

    public function upload(Request $request)
    {
        $games = new GameList();
        $games->Provider = $request->Provider;
        $games->GameName = $request->GameName;
        $games->GameCode = $request->GameCode;
        $games->GameType = $request->GameType;
        $games->ProviderCode = $request->ProviderCode;
        $games->GameImage = $request->image;
        $games->Category = $request->Category;
        $games->save();

        return redirect()->back();
    }

    public function provider()
    {
        $pageTitle = "Provider List";
        $provider = ProviderList::all();
        return view('site.provider.list',compact('pageTitle','provider'));
    }
}
