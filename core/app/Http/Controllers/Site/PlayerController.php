<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\UserPlayer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = "Player List";

        if (auth()->user()->level == 1) {
            $player = UserPlayer::where('agent_code')->orderBy('created_at','desc');
        } else {
            $player = UserPlayer::where('agent_code',auth()->user()->agentcode)->orderBy('created_at','desc');
        }

        if ($request->ajax()) {
            return DataTables::of($player)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    $cbtrn = $row->created_at;
                    return $cbtrn;
                })
                ->addColumn('balance', function ($row) {
                    $balance = number_format($row->balance ,2);
                    return $balance;
                })
                ->rawColumns(['created_at','balance'])
                ->make(true);
        }
        return view('site.player.list',compact('pageTitle'));
    }
}
