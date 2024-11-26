<?php

namespace App\Http\Controllers\Callback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\ApiSeamles;
use App\Models\BuyGames;
use App\Models\GamesHistory;
use App\Models\UserPlayer;
use App\Models\Wager;
use App\Models\GameList;
use App\Models\AgentApi;

class CallbackController extends Controller
{
    public function seamles($method)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        switch ($method) {
            case 'GetBalance':
                return $this->GetBalance($data);
                break;
            case 'PlaceBet':
                return $this->PlaceBet($data);
                break;
            case 'GameResult':
                return $this->GameResult($data);
                break;
            case 'Rollback':
                return $this->Rollback($data);
                break;
            case 'CancelBet':
                return $this->CancelBet($data);
                break;
            case 'Bonus':
                return $this->Bonus($data);
                break;
            case 'Jackpot':
                return $this->Jackpot($data);
                break;
            case 'BuyIn':
                return $this->BuyIn($data);
                break;
            case 'BuyOut':
                return $this->BuyOut($data);
                break;
            case 'PushBet':
                return $this->PushBet($data);
                break;
            case 'MobileLogin':
                return $this->MobileLogin($data);
                break;
            default:
                abort(404);
        }
    }

    private function GetBalance($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        $player = DB::table('users')->where('userCode',$data['MemberName'])->first();
        $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'getbalance', $agentApi->secretkey);

        if ($data['Sign'] != $getsign) {
            return response()->json([
                'ErrorCode' => 1004,
                'ErrorMessage' => 'API Invalid Sign',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif (!$player) {
            return response()->json([
                'ErrorCode' => 1000,
                'ErrorMessage' => 'API Member Not Exists',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif (!empty($player)) {
            return response()->json([
                'ErrorCode' => 0,
                'ErrorMessage' => 'Success',
                'Balance' => $player->balance,
                'BeforeBalance' => 0
            ]);
        }
    }

    private function PlaceBet($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        foreach ($data['Transactions'] as $trans) {
            $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'placebet', $agentApi->secretkey);
            $transaction = GamesHistory::where('TransactionID', $trans['TransactionID'])->first();
                    $player = DB::table('users')->where('userCode',$data['MemberName'])->first();


            $ballbefore = $player->balance;
            $balance = $player->balance += $trans['TransactionAmount'];

            $insuff = preg_replace("/[^0-9]/", "",$trans['TransactionAmount']);

            if ($data['Sign'] != $getsign) {
                return response()->json([
                    'ErrorCode' => 1004,
                    'ErrorMessage' => 'API Invalid Sign',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!$player) {
                return response()->json([
                    'ErrorCode' => 1000,
                    'ErrorMessage' => 'API Member Not Exists',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!empty($player)) {

                if ($trans['BetAmount'] > $ballbefore) {
                    return response()->json([
                        'ErrorCode' => 1001,
                        'ErrorMessage' => 'API Member Insufficient Balance',
                        'Balance' => $balance,
                        'BeforeBalance' => $ballbefore
                    ]);
                } elseif (!empty($transaction)) {
                    return response()->json([
                        'ErrorCode' => 1003,
                        'ErrorMessage' => 'API Duplicate Transaction',
                        'Balance' => 0,
                        'BeforeBalance' => 0
                    ]);
                }
                $this->insertTrans($data);

                DB::table('users')->where('userCode', $data['MemberName'])->update([
                    'balance' => $balance,
                ]);

                DB::table('users')->where('userCode', $data['MemberName'])->update([
                    'balance' => $balance,
                ]);

                return response()->json([
                    'ErrorCode' => 0,
                    'ErrorMessage' => 'Success',
                    'Balance' => $balance,
                    'BeforeBalance' => $ballbefore
                ]);
            }
        }
    }

    private function GameResult($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        foreach ($data['Transactions'] as $trans) {
            $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'gameresult', $agentApi->secretkey);
            $transaction = GamesHistory::where('TransactionID', $trans['TransactionID'])->first();
            $getwager = GamesHistory::where('WagerID', $trans['WagerID'])->first();
            $getwager2 = BuyGames::where('WagerID', $trans['WagerID'])->first();
                    $player = DB::table('users')->where('userCode',$data['MemberName'])->first();

            $ballbefore = $player->balance;
            $this->insertTrans($data);

            if ($data['Sign'] != $getsign) {
                return response()->json([
                    'ErrorCode' => 1004,
                    'ErrorMessage' => 'API Invalid Sign',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!$player) {
                return response()->json([
                    'ErrorCode' => 1000,
                    'ErrorMessage' => 'API Member Not Exists',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!empty($player)) {

                $balance = $player->balance += $trans['TransactionAmount'];
                if (!$getwager && !$getwager2) {
                    return response()->json([
                        'ErrorCode' => 1006,
                        'ErrorMessage' => 'API Bet Not Exist',
                        'Balance' => 0,
                        'BeforeBalance' => 0
                    ]);
                } elseif (!empty($transaction)) {
                    return response()->json([
                        'ErrorCode' => 1003,
                        'ErrorMessage' => 'API Duplicate Transaction',
                        'Balance' => 0,
                        'BeforeBalance' => 0
                    ]);
                }

                DB::table('users')->where('userCode', $data['MemberName'])->update([
                    'balance' => $balance,
                ]);

                return response()->json([
                    'ErrorCode' => 0,
                    'ErrorMessage' => 'Success',
                    'Balance' => $balance,
                    'BeforeBalance' => $ballbefore
                ]);
            }
        }
    }

    private function Rollback($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        foreach ($data['Transactions'] as $trans) {
            $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'rollback', $agentApi->secretkey);
            $getwager = GamesHistory::where('WagerID', $trans['WagerID'])->first();
            $transaction = GamesHistory::where('TransactionID', $trans['TransactionID'])->first();
                    $player = DB::table('users')->where('userCode',$data['MemberName'])->first();

            $ballbefore = $player->balance;
            $this->insertTrans($data);

            if ($data['Sign'] != $getsign) {
                return response()->json([
                    'ErrorCode' => 1004,
                    'ErrorMessage' => 'API Invalid Sign',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!$player) {
                return response()->json([
                    'ErrorCode' => 1000,
                    'ErrorMessage' => 'API Member Not Exists',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!empty($player)) {
                $balance = $player->balance += $trans['TransactionAmount'];
                if (!empty($transaction)) {
                    return response()->json([
                        'ErrorCode' => 1003,
                        'ErrorMessage' => 'API Duplicate Transaction',
                        'Balance' => 0,
                        'BeforeBalance' => 0
                    ]);
                }

                DB::table('users')->where('userCode', $data['MemberName'])->update([
                    'balance' => $balance,
                ]);

                return response()->json([
                    'ErrorCode' => 0,
                    'ErrorMessage' => 'Success',
                    'Balance' => $balance,
                    'BeforeBalance' => $ballbefore
                ]);
            }
        }
    }

    private function CancelBet($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        foreach ($data['Transactions'] as $trans) {
            $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'cancelbet', $agentApi->secretkey);
            $getwager = GamesHistory::where('WagerID', $trans['WagerID'])->first();
            $transaction = GamesHistory::where('TransactionID', $trans['TransactionID'])->first();
                    $player = DB::table('users')->where('userCode',$data['MemberName'])->first();

            $ballbefore = $player->balance;
            $this->insertTrans($data);

            if ($data['Sign'] != $getsign) {
                return response()->json([
                    'ErrorCode' => 1004,
                    'ErrorMessage' => 'API Invalid Sign',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!$player) {
                return response()->json([
                    'ErrorCode' => 1000,
                    'ErrorMessage' => 'API Member Not Exists',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!empty($player)) {

                $balance = $player->balance += $trans['TransactionAmount'];
                if (!empty($transaction)) {
                    return response()->json([
                        'ErrorCode' => 1003,
                        'ErrorMessage' => 'API Duplicate Transaction',
                        'Balance' => 0,
                        'BeforeBalance' => 0
                    ]);
                }

                DB::table('users')->where('userCode', $data['MemberName'])->update([
                    'balance' => $balance,
                ]);

                return response()->json([
                    'ErrorCode' => 0,
                    'ErrorMessage' => 'Success',
                    'Balance' => $balance,
                    'BeforeBalance' => $ballbefore
                ]);
            }
        }
    }

    private function Bonus($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        foreach ($data['Transactions'] as $trans) {
            $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'bonus', $agentApi->secretkey);
            $transaction = GamesHistory::where('TransactionID', $trans['TransactionID'])->first();
                    $player = DB::table('users')->where('userCode',$data['MemberName'])->first();

            $ballbefore = $player->balance;
            $this->insertTrans($data);

            if ($data['Sign'] != $getsign) {
                return response()->json([
                    'ErrorCode' => 1004,
                    'ErrorMessage' => 'API Invalid Sign',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!$player) {
                return response()->json([
                    'ErrorCode' => 1000,
                    'ErrorMessage' => 'API Member Not Exists',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!empty($player)) {

                $balance = $player->balance += $trans['TransactionAmount'];
                if (!empty($transaction)) {
                    return response()->json([
                        'ErrorCode' => 1003,
                        'ErrorMessage' => 'API Duplicate Transaction',
                        'Balance' => 0,
                        'BeforeBalance' => 0
                    ]);
                }
                DB::table('users')->where('userCode', $data['MemberName'])->update([
                    'balance' => $balance,
                ]);

                return response()->json([
                    'ErrorCode' => 0,
                    'ErrorMessage' => 'Success',
                    'Balance' => $balance,
                    'BeforeBalance' => $ballbefore
                ]);
            }
        }
    }

    private function Jackpot($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        foreach ($data['Transactions'] as $trans) {
            $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'jackpot', $agentApi->secretkey);
            $transaction = GamesHistory::where('TransactionID', $trans['TransactionID'])->first();
                    $player = DB::table('users')->where('userCode',$data['MemberName'])->first();

            $ballbefore = $player->balance;
            $this->insertTrans($data);

            if ($data['Sign'] != $getsign) {
                return response()->json([
                    'ErrorCode' => 1004,
                    'ErrorMessage' => 'API Invalid Sign',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!$player) {
                return response()->json([
                    'ErrorCode' => 1000,
                    'ErrorMessage' => 'API Member Not Exists',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!empty($player)) {

                $balance = $player->balance += $trans['JackpotAmount'];
                if (!empty($transaction)) {
                    return response()->json([
                        'ErrorCode' => 1003,
                        'ErrorMessage' => 'API Duplicate Transaction',
                        'Balance' => 0,
                        'BeforeBalance' => 0
                    ]);
                }

                DB::table('users')->where('userCode', $data['MemberName'])->update([
                    'balance' => $balance,
                ]);

                return response()->json([
                    'ErrorCode' => 0,
                    'ErrorMessage' => 'Success',
                    'Balance' => $balance,
                    'BeforeBalance' => $ballbefore
                ]);
            }
        }
    }

    private function BuyIn($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        $trans = $data['Transaction'];
        $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'buyin', $agentApi->secretkey);
        $transaction = BuyGames::where('TransactionID', $trans['TransactionID'])->first();
                $player = DB::table('users')->where('userCode',$data['MemberName'])->first();
        $ballbefore = $player->balance;
        $this->insertTransBuy($trans,$data['MemberName']);

        if ($data['Sign'] != $getsign) {
            return response()->json([
                'ErrorCode' => 1004,
                'ErrorMessage' => 'API Invalid Sign',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif (!$player) {
            return response()->json([
                'ErrorCode' => 1000,
                'ErrorMessage' => 'API Member Not Exists',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif (!empty($player)) {

            $balance = $player->balance += $trans['TransactionAmount'];
            $insuff = preg_replace("/[^0-9]/", "",$trans['TransactionAmount']);
            if (!empty($transaction)) {
                return response()->json([
                    'ErrorCode' => 1003,
                    'ErrorMessage' => 'API Duplicate Transaction',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif ($trans['BetAmount'] < $ballbefore) {
                return response()->json([
                    'ErrorCode' => 1001,
                    'ErrorMessage' => 'API Member Insufficient Balance',
                    'Balance' => $balance,
                    'BeforeBalance' => $ballbefore
                ]);
            }

            $player->balance = $balance;
            $player->save();

            return response()->json([
                'ErrorCode' => 0,
                'ErrorMessage' => 'Success',
                'Balance' => $balance,
                'BeforeBalance' => $ballbefore
            ]);
        }
    }

    private function BuyOut($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
        $trans = $data['Transaction'];
        $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'buyout', $agentApi->secretkey);
        $transaction = BuyGames::where('TransactionID', $trans['TransactionID'])->first();
                $player = DB::table('users')->where('userCode',$data['MemberName'])->first();
        $ballbefore = $player->balance;
        $this->insertTransBuy($trans,$data['MemberName']);

        if ($data['Sign'] != $getsign) {
            return response()->json([
                'ErrorCode' => 1004,
                'ErrorMessage' => 'API Invalid Sign',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif (!$player) {
            return response()->json([
                'ErrorCode' => 1000,
                'ErrorMessage' => 'API Member Not Exists',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif (!empty($player)) {
            $balance = $player->balance += $trans['TransactionAmount'];

            if (!empty($transaction)) {
                return response()->json([
                    'ErrorCode' => 1003,
                    'ErrorMessage' => 'API Duplicate Transaction',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            }

            $player->balance = $balance;
            $player->save();

            return response()->json([
                'ErrorCode' => 0,
                'ErrorMessage' => 'Success',
                'Balance' => $balance,
                'BeforeBalance' => $ballbefore
            ]);
        }
    }

    private function PushBet($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
                $player = DB::table('users')->where('userCode',$data['MemberName'])->first();
        $ballbefore = $player->balance;
        foreach ($data['Transactions'] as $trans) {
            $this->insertTransBuy($trans,$data['MemberName']);

            if (!$player) {
                return response()->json([
                    'ErrorCode' => 1000,
                    'ErrorMessage' => 'API Member Not Exists',
                    'Balance' => 0,
                    'BeforeBalance' => 0
                ]);
            } elseif (!empty($player)) {
                $balance = $player->balance += $trans['TransactionAmount'];

                return response()->json([
                    'ErrorCode' => 0,
                    'ErrorMessage' => 'Success',
                    'Balance' => $balance,
                    'BeforeBalance' => $ballbefore
                ]);
            }
        }
    }

    private function MobileLogin($data)
    {
        $agentApi = ApiSeamles::where('apikey', $data['OperatorCode'])->first();
                $player = DB::table('users')->where('userCode',$data['MemberName'])->first();
        $getsign = $this->generateSign($agentApi->apikey, $data['RequestTime'], 'mobilelogin', $agentApi->secretkey);

        if ($data['Sign'] != $getsign) {
            return response()->json([
                'ErrorCode' => 1004,
                'ErrorMessage' => 'API Invalid Sign',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif (!$player) {
            return response()->json([
                'ErrorCode' => 1000,
                'ErrorMessage' => 'API Member Not Exists',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif ($player->password !== $data['Password']) {
            return response()->json([
                'ErrorCode' => 1000,
                'ErrorMessage' => 'API Member Password Not Match',
                'Balance' => 0,
                'BeforeBalance' => 0
            ]);
        } elseif (!empty($player)) {
            return response()->json([
                'ErrorCode' => 0,
                'ErrorMessage' => 'Success',
                'Balance' => $player->balance,
                'BeforeBalance' => 0
            ]);
        }
    }

    public function launchGame(Request $request)
    {
        if (!$request->agentcode) {
            return response()->json(array(
                'ErrorCode' => 500,
                'ErrorMsg' => 'Error',
                'Message' => 'AgentCode Cannot Be Null'
            ), 500);
        } elseif (!$request->signature) {
            return response()->json(array(
                'ErrorCode' => 500,
                'ErrorMsg' => 'Error',
                'Message' => 'Signature Cannot Be Null'
            ), 500);
        } elseif (!$request->playerid) {
            return response()->json(array(
                'ErrorCode' => 500,
                'ErrorMsg' => 'Error',
                'Message' => 'PlayerID Cannot Be Null'
            ), 500);
        } elseif (!$request->provider) {
            return response()->json(array(
                'ErrorCode' => 500,
                'ErrorMsg' => 'Error',
                'Message' => 'Provider Cannot Be Null'
            ), 500);
        } elseif (!$request->game_code) {
            return response()->json(array(
                'ErrorCode' => 500,
                'ErrorMsg' => 'Error',
                'Message' => 'GameCode Cannot Be Null'
            ), 500);
        } elseif (!$request->lobbyURL) {
            return response()->json(array(
                'ErrorCode' => 500,
                'ErrorMsg' => 'Error',
                'Message' => 'lobbyURL Cannot Be Null'
            ), 500);
        } elseif (!$request->game_type) {
            return response()->json(array(
                'ErrorCode' => 500,
                'ErrorMsg' => 'Error',
                'Message' => 'GameType Cannot Be Null'
            ), 500);
        }

        $agentapi = AgentApi::where('agentcode', $request->agentcode)->first();
        $player = UserPlayer::where('id', $request->playerid)->first();
        $game = GameList::where('GameCode',$request->game_code)->where('GameType',$request->game_type)->first();
        $apis = ApiSeamles::first();
        if (!$agentapi) {
            return response()->json(array(
                'ErrorCode' => 492,
                'ErrorMsg' => 'Error',
                'Message' => 'Invalid AgentCode'
            ), 422);
        } elseif ($request->signature !== $this->CheckSign($request->agentcode, "LaunchGame")) {
            return response()->json(array(
                'ErrorCode' => 487,
                'ErrorMsg' => 'Error',
                'Message' => 'Invalid Signature'
            ), 500);
        } elseif (!$game) {
            return response()->json(array(
                'ErrorCode' => 455,
                'ErrorMsg' => 'Error',
                'Message' => 'Games Not Available'
            ), 500);
        } elseif (!$player) {
            return response()->json(array(
                'ErrorCode' => 25,
                'ErrMessage' => 'Error',
                'Message' => 'Invalid PlayerID'
            ), 422);
        }

        if ($request->game_type === 1) {
            $response = json_decode(Http::post($apis->url.'Seamless/LaunchGame', [
                'OperatorCode' => $apis->agentcode,
                'MemberName' => $player->ext_player,
                'Password' => $player->password,
                'GameID' => $game->GameCode,
                'ProductID' => $game->ProviderCode,
                'GameType' => $request->GameType,
                'LanguageCode' => 1,
                'Platform' => 0,
                'OperatorLobbyURL' => $request->LobbyURL,
                'Sign' => $this->generateSign($apis->agentcode,date('YMdHms'),'launchgame',$apis->secretkey),
                'RequestTime' => date('YMdHms'),
            ]));

            return $response;
        } else {
            $response = json_decode(Http::post($apis->url.'Seamless/LaunchGame', [
                'OperatorCode' => $apis->agentcode,
                'MemberName' => $player->ext_player,
                'Password' => $player->password,
                'ProductID' => $game->ProviderCode,
                'GameType' => $request->GameType,
                'LanguageCode' => 1,
                'Platform' => 0,
                'OperatorLobbyURL' => $request->LobbyURL,
                'Sign' => $this->generateSign($apis->agentcode,date('YMdHms'),'launchgame',$apis->secretkey),
                'RequestTime' => date('YMdHms'),
            ]));
            return $response;
        }
    }

    function generateSign($OperatorCode, $RequestTime, $MethodName, $SecretKey)
    {
        $sign = md5($OperatorCode . $RequestTime . $MethodName . $SecretKey);
        return $sign;
    }

    function CheckSign($agentcode, $method)
    {
        $agent = AgentApi::where('agentcode', $agentcode)->first();

        $hash = md5($agent->agentcode . $method . $agent->secretkey);
        return $hash;
    }

    function insertTrans($data)
    {
        foreach ($data['Transactions'] as $dats) {
            $transaction = new GamesHistory();
            $transaction->MemberName = $data['MemberName'];
            $transaction->MemberID = $dats['MemberID'];
            $transaction->OperatorID = $dats['OperatorID'];
            $transaction->ProductID = $dats['ProductID'];
            $transaction->ProviderID = $dats['ProviderID'];
            $transaction->ProviderLineID = $dats['ProviderLineID'];
            $transaction->WagerID = $dats['WagerID'];
            $transaction->CurrencyID = $dats['CurrencyID'];
            $transaction->GameType = $dats['GameType'];
            $transaction->GameID = $dats['GameID'];
            $transaction->GameRoundID = $dats['GameRoundID'];
            $transaction->ValidBetAmount = $dats['ValidBetAmount'];
            $transaction->BetAmount = $dats['BetAmount'];
            $transaction->TransactionAmount = $dats['TransactionAmount'];
            $transaction->TransactionID = $dats['TransactionID'];
            $transaction->PayoutAmount = $dats['PayoutAmount'];
            $transaction->PayoutDetail = $dats['PayoutDetail'];
            $transaction->CommissionAmount = $dats['CommissionAmount'];
            $transaction->JackpotAmount = $dats['JackpotAmount'];
            $transaction->SettlementDate = $dats['SettlementDate'];
            $transaction->JPBet = $dats['JPBet'];
            $transaction->Status = $dats['Status'];
            $transaction->save();
        }
    }

    function insertTransBuy($data,$membername)
    {
        $trans = new BuyGames();
        $trans->MemberName = $membername;
        $trans->MemberID = 0;
        $trans->OperatorID = $data['OperatorID'];
        $trans->ProductID = $data['ProductID'];
        $trans->ProviderID = $data['ProviderID'];
        $trans->ProviderLineID = $data['ProviderLineID'];
        $trans->WagerID = $data['WagerID'];
        $trans->CurrencyID = $data['CurrencyID'];
        $trans->GameType = $data['GameType'];
        $trans->GameID = $data['GameID'];
        $trans->GameRoundID = $data['GameRoundID'];
        $trans->ValidBetAmount = $data['ValidBetAmount'];
        $trans->BetAmount = $data['BetAmount'];
        $trans->TransactionAmount = $data['TransactionAmount'];
        $trans->TransactionID = $data['TransactionID'];
        $trans->PayoutAmount = $data['PayoutAmount'];
        $trans->PayoutDetail = $data['PayoutDetail'];
        $trans->CommissionAmount = $data['CommissionAmount'];
        $trans->JackpotAmount = $data['JackpotAmount'];
        $trans->SettlementDate = $data['SettlementDate'];
        $trans->JPBet = $data['JPBet'];
        $trans->Status = $data['Status'];
        $trans->save();
    }



    // WS AND FIVERS CALLBACK

    public function getUserBalance()
    {
        $request_json = file_get_contents('php://input');
        $request_data = json_decode($request_json, true);

        $user_code = $request_data["user_code"];

        $user = UserPlayer::where('ext_player', $user_code)->first();
        if (!$user) {
            return json_encode([
                "status" => 0,
                "msg" => "INVALID_USER"
            ]);
        }

        if ($user->balance <= 0) {
            return json_encode([
                "status" => 0,
                "msg" => "INSUFFICIENT_USER_FUNDS",
                "user_balance" => 0
            ]);
        }
        return json_encode([
            "status" => 1,
            "user_balance" => $user->balance
        ]);
    }

    public function gameCallback()
    {
        $request_json = file_get_contents('php://input');
        $request_data = json_decode($request_json, true);

        $user_code = $request_data["user_code"];
        $game_type = $request_data["game_type"];

        $user = UserPlayer::where('ext_player', $user_code)->first();
        if (!$user) {
            return json_encode(["status" => 0, "msg" => "INVALID_USER"]);
        }

        if ($user->balance <= 0) {
            return json_encode([
                "status" => 0,
                "msg" => "INSUFFICIENT_USER_FUNDS",
                "user_balance" => 0
            ]);
        }

        if ($game_type == "slot") {
            $bet = $request_data["slot"]["bet"];
            $win = $request_data["slot"]["win"];
        } else {
            $bet = $request_data['casino']['bet'];
            $win = $request_data['casino']['win'];
        }

        $result_balance = $user->balance - $bet + $win;

        $user->balance = $result_balance;
        $user->save();

        return json_encode([
            "status" => 1,
            "user_balance" => $result_balance
        ]);
    }

    public function gold_api()
    {
        $request_json = file_get_contents('php://input');
        $request_data = json_decode($request_json, true);

        if ($request_data["method"] == "user_balance") {
            $user_code = $request_data["user_code"];

            $user = UserPlayer::where('ext_player', $user_code)->first();
            if (!$user) {
                return json_encode([
                    "status" => 0,
                    "msg" => "INVALID_USER"
                ]);
            }

            if ($user->balance <= 0) {
                return json_encode([
                    "status" => 0,
                    "msg" => "INSUFFICIENT_USER_FUNDS",
                    "user_balance" => 0
                ]);
            }
            return json_encode([
                "status" => 1,
                "user_balance" => $user->balance
            ]);
        } else {
            $user_code = $request_data["user_code"];
            $game_type = $request_data["game_type"];

            $user = UserPlayer::where('ext_player', $user_code)->first();
            if (!$user) {
                return json_encode(["status" => 0, "msg" => "INVALID_USER"]);
            }

            if ($user->balance <= 0) {
                return json_encode([
                    "status" => 0,
                    "msg" => "INSUFFICIENT_USER_FUNDS",
                    "user_balance" => 0
                ]);
            }

            if ($game_type == "slot") {
                $bet = $request_data["slot"]["bet_money"];
                $win = $request_data["slot"]["win_money"];
                $provider_code = $request_data["slot"]["provider_code"];
                $game_code = $request_data["slot"]["game_code"];
            } else {
                $bet = $request_data['casino']['bet_money'];
                $win = $request_data['casino']['win_money'];
                $provider_code = $request_data['casino']['provider_code'];
                $game_code = $request_data['casino']['game_code'];
            }

            $result_balance = $user->balance - $bet + $win;

            $user->balance = $result_balance;
            $user->save();

            DB::table('fivers_trans')->insert([
                'username' => $user_code,
                'provider' => $provider_code,
                'game_code' => $game_code,
                'type' => $game_type,
                'bet' => $bet,
                'win' => $win,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            return json_encode([
                "status" => 1,
                "user_balance" => $result_balance
            ]);
        }
    }

    // END WS AND FIVERS CALLBACK
}
