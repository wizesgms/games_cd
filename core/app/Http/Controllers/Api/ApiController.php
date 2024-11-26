<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\GamesHistory;
use Illuminate\Support\Facades\DB;
use App\Models\UserPlayer;
use App\Models\AgentApi;
use App\Models\GameList;
use App\Models\ProviderList;
use Illuminate\Support\Str;

use App\Models\ApiActive;
use App\Models\ApiProvider;

use Yajra\DataTables\Facades\DataTables;

class ApiController extends Controller
{

    public function methode(Request $request)
    {

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_METHOD'
            ], 200);
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $method = $data['method'];

        if (!$data['agent_code']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETER'
            ], 200);
        } elseif (!$data['agent_token']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETER'
            ], 200);
        }
        $agentapi = DB::table('agents')->where('agentCode', $data['agent_code'])->first();
        if (!$agentapi) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_AGENT'
            ], 200);
        } elseif ($data['agent_token'] !== $agentapi->token) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_AGENT_TOKEN'
            ], 200);
        } elseif ($agentapi->status !== 1) {
            return response()->json([
                'status' => 0,
                'msg' => 'BLOCKED_AGENT'
            ], 200);
        }

        switch ($method) {
            case 'user_create':
                return $this->PlayerAccountCreate($data);
                break;
            case 'money_info':
                return $this->getBalance($data);
                break;
            case 'user_deposit':
                return $this->balanceTopup($data);
                break;
            case 'user_withdraw':
                return $this->balanceWithdraw($data);
                break;
            case 'get_game_log':
                return $this->getHistory($data);
                break;
            case 'get_history':
                return $this->getHistory2($data);
                break;
            case 'game_launch':
                return $this->launch_game($data);
                break;
            case 'provider_list':
                return $this->provider_list($data);
                break;
            case 'user_withdraw_reset':
                return $this->balanceWithdrawAll($data);
                break;
            case 'game_list':
                return $this->game_list($data);
                break;
            case 'control_rtp':
                return $this->control_rtp($data);
                break;
            default:
                abort(404);
        }
    }

    public function callbacktb()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $method = $data['cmd'];

        switch ($method) {
            case 'getBalance':
                return $this->getBalancetb($data);
                break;
            case 'writeBet':
                return $this->writeBet($data);
                break;
            default:
                abort(404);
        }
    }

    function getBalancetb($data)
    {
        $player = DB::table('users')->where('userCode', $data['login'])->first();

        $agents = DB::table('agents')->where('agentCode', $player->agentCode)->first();

        if ($player->apiType != $agents->apiType) {
            return response()->json([
                'status' => 'fail',
                'error' => 'user_not_found'
            ], 200);
        }

        if ($agents->apiType == 0) {
            $postArray = [
                'method' => 'user_balance',
                'agent_code' => $agents->agentCode,
                'agent_secret' => $agents->secretKey,
                'user_code' => $player->userCode
            ];
            $jsonData = json_encode($postArray);


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $agents->siteEndPoint . '/gold_api',
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

            return response()->json([
                'status' => 'success',
                'error' => '',
                'login' => $data['login'],
                'balance' => $result->user_balance,
                'currency' => 'THB'
            ], 200);
        }

        if (!$player) {
            return response()->json([
                'status' => 'fail',
                'error' => 'user_not_found'
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'error' => '',
            'login' => $data['login'],
            'balance' => $player->balance,
            'currency' => 'THB'
        ], 200);
    }

    function writeBet($data)
    {
        $player = DB::table('users')->where('userCode', $data['login'])->first();

        if (!$player) {
            return response()->json([
                'status' => 'fail',
                'error' => 'user_not_found'
            ], 200);
        }

        $agents = DB::table('agents')->where('agentCode', $player->agentCode)->first();

        if ($agents->apiType == 0) {
            $postArray = [
                'method' => 'transaction',
                'agent_code' => $agents->agentCode,
                'agent_secret' => $agents->secretKey,
                'agent_balance' => $agents->balance,
                'user_code' => $player->userCode,
                'user_balance' => $player->balance,
                'data' => [
                    'game_code' => $data['gameId'],
                    'type' => 'BASE',
                    'bet_money' => $data['bet'],
                    'win_money' => $data['win'],
                    'txn_id' => $data['tradeId'],
                    'txn_type' => 'debit_credit'
                ],
            ];
            $jsonData = json_encode($postArray);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $agents->siteEndPoint . '/gold_api',
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

            if ($result->user_balance < $data['bet']) {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'fail_balance'
                ], 200);
            }

            $before_balance = $player->balance;

            $result_balance = $player->balance - $data['bet'] + $data['win'];

            $agent_balance = $agents->balance - $data['bet'] + $data['win'];

            DB::table('agents')->where('agentCode', $player->agentCode)->where('apiType', 0)->update([
                'balance' => $agent_balance,
            ]);

            if ($player->balance > $result_balance) {
                $type = 'bet';
            } else {
                $type = 'win';
            }


            DB::table('users')->where('userCode', $data['login'])->update([
                'balance' => $result->user_balance,
            ]);


            DB::table('trans_bet')->insert([
                'history_id' => $data['sessionId'],
                'agent_code' => $player->agentCode,
                'user_code' => $data['login'],
                'game_code' => $data['gameId'],
                'type' => $type,
                'bet_money' => $data['bet'],
                'win_money' => $data['win'],
                'txn_id' => $data['tradeId'],
                'txn_type' => 'debit_credit',
                'user_start_balance' => $before_balance,
                'user_end_balance' =>  $result_balance,
                'agent_start_balance' => $agents->balance,
                'agent_end_balance' => $agents->balance,
                'created_at' => date("Y-m-d H:i:s")
            ]);

            return response()->json([
                'status' => 'success',
                'error' => '',
                'login' => $data['login'],
                'balance' => $result_balance,
                'currency' => 'THB',
                'operationId' => rand(3, 10)
            ], 200);
        } else {

            if ($player->balance < $data['bet']) {
                return response()->json([
                    'status' => 'fail',
                    'error' => 'fail_balance'
                ], 200);
            }

            $before_balance = $player->balance;

            $result_balance = $player->balance - $data['bet'] + $data['win'];

            if ($player->balance > $result_balance) {
                $type = 'bet';
            } else {
                $type = 'win';
            }

            DB::table('users')->where('userCode', $data['login'])->where('apiType', 1)->update([
                'balance' => $result_balance,
            ]);


            DB::table('trans_bet')->insert([
                'history_id' => $data['sessionId'],
                'agent_code' => $player->agentCode,
                'user_code' => $data['login'],
                'game_code' => $data['gameId'],
                'type' => $type,
                'bet_money' => $data['bet'],
                'win_money' => $data['win'],
                'txn_id' => $data['tradeId'],
                'txn_type' => 'debit_credit',
                'user_start_balance' => $before_balance,
                'user_end_balance' =>  $result_balance,
                'agent_start_balance' => $agents->balance,
                'agent_end_balance' => $agents->balance,
                'created_at' => date("Y-m-d H:i:s")
            ]);

            return response()->json([
                'status' => 'success',
                'error' => '',
                'login' => $data['login'],
                'balance' => $result_balance,
                'currency' => 'THB',
                'operationId' => rand(3, 10)
            ], 200);
        }
    }

    function control_rtp($data)
    {
        if (!$data['user_code']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETER'
            ], 200);
        }

        $player = DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->first();

        if (!$player) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_USER'
            ], 200);
        }

        DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->update([
            'targetRtp' => $data['rtp'],
            'realRtp' => $data['rtp']
        ]);

        return response()->json([
            'status' => 1,
            'changed_rtp' => $data['rtp']
        ], 200);
    }

    public function callback_mb($method, Request $request)
    {
        $params = $request->except(['sign', 'meta']);

        switch ($method) {
            case 'trx.cancel':
                return $this->trxCancel($request);
                break;

            case 'trx.complete':
                return $this->trxComplete($request);
                break;

            case 'check.session':
                return $this->checkSession($request);
                break;

            case 'check.balance':
                return $this->checkBalance($request);
                break;

            case 'withdraw.bet':
                return $this->userBet($request);
                break;

            case 'deposit.win':
                return $this->userWin($request);
                break;

            default:
                throw new \Exception("Unknown method");
        }
    }

    private function generateSign($params, $partner, $secret, $method)
    {

        $joined = array_filter(array_keys($params), function ($name) {
            return !str_starts_with($name, "partner.");
        });
        sort($joined);
        $joined = array_map(function ($name) use ($params) {
            return $name . "=" . $params[$name];
        }, $joined);
        $joined = implode("&", $joined);

        return md5($joined . "&" . $method . "&" . $partner . "&" . $secret);
    }

    private function trxCancel($data)
    {
        return response()->json(['status' => 200]);
    }

    private function trxComplete($data)
    {
        return response()->json(['status' => 200]);
    }

    private function checkSession($data)
    {
        if (!$data->session) return response()->json(['status' => 404, 'method' => 'check.session', 'message' => 'Unknown session']);
        $user = DB::table('users')->where('userCode', $data->session)->first();
        if (!$user) return response()->json(['status' => 404, 'method' => 'check.session', 'message' => 'Unknown user']);

        return response()->json(['status' => 200, 'method' => 'check.session', 'response' => ['id_player' => $user->id, 'id_group' => 'default', 'balance' => preg_replace("/[^0-9]/","",$user->balance) ]]);
    }

    private function checkBalance($data)
    {
        if (!$data->session) return response()->json(['status' => 404, 'method' => 'check.balance', 'message' => 'Unknown session']);
        $user = DB::table('users')->where('userCode', $data->session)->first();
        if (!$user) return response()->json(['status' => 404, 'method' => 'check.balance', 'message' => 'Unknown user']);

        return response()->json(['status' => 200, 'method' => 'check.balance', 'response' => ['currency' => 'THB', 'balance' => preg_replace("/[^0-9]/","",$user->balance) ]]);
    }

    public function userBet($data)
    {
        if (!$data->session) return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Unknown session']);
        $user = DB::table('users')->where('userCode', $data->session)->first();
        if (!$user) return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Unknown user']);

        $amount = $data->amount / 100;

        if ($user->balance < ($amount)) return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Fail balance']);

        $result_balance = $user->balance - $amount;

        DB::table('users')->where('userCode', $data->session)->update([
            'balance' => $result_balance,
        ]);

        return response()->json(['status' => 200, 'method' => 'withdraw.bet', 'response' => ['currency' => 'THB', 'balance' => preg_replace("/[^0-9]/","",$user->balance) ]]);
    }

    public function userWin($data)
    {
        if (!$data->session) return response()->json(['status' => 404, 'method' => 'deposit.win', 'message' => 'Unknown session']);
        $user = DB::table('users')->where('userCode', $data->session)->first();
        if (!$user) return response()->json(['status' => 404, 'method' => 'deposit.win', 'message' => 'Unknown user']);

        $amount = $data->amount / 100;

        $result_balance = $user->balance + $amount;

        DB::table('users')->where('userCode', $data->session)->update([
            'balance' => $result_balance,
        ]);

        return response()->json(['status' => 200, 'method' => 'deposit.win', 'response' => ['currency' => 'THB', 'balance' => preg_replace("/[^0-9]/","",$user->balance) ]]);
    }

    function PlayerAccountCreate($data)
    {
        $agentscs = DB::table('agents')->where('agentCode', $data['agent_code'])->first();

        if ($agentscs->apiType != 1) {
            return response()->json([
                'status' => 0,
                'msg' => 'AGENT_SEAMLESS'
            ], 200);
        }

        if (!$data['user_code']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETER'
            ], 200);
        }

        $player = DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->first();

        if (!empty($player)) {
            return response()->json([
                'status' => 0,
                'msg' => 'DUPLICATED_USER'
            ], 200);
        }

        DB::table('users')->insert([
            'agentCode' => $data['agent_code'],
            'userCode' => $data['user_code'],
            'targetRtp' => 80,
            'realRtp' => 80,
            'balance' => 0,
            'aasUserCode' => $data['user_code'],
            'status' => 1,
            'parentPath' => 1,
            'totalDebit' => 0,
            'totalCredit' => 0,
            'apiType' => 1,
            'updatedAt' => date("Y-m-d H:i:s"),
            'createdAt' => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            'status' => 1,
            'msg' => 'SUCCESS',
            'user_code' => $data['user_code'],
            'user_balance' => 0
        ], 200);
    }

    function getHistory($data)
    {
        $postArray = [
            'method' => 'get_history',
            'agent_code' => $data['agent_code'],
            'agent_token' => $data['agent_token'],
            'user_code' => $data['user_code'],
            'start' => $data['start'],
            'end' => $data['end'],
            'page' => $data['page'],
            'perPage' => $data['perPage'],
        ];
        $jsonData = json_encode($postArray);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://1api.isomatslot.com/',
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

        return response()->json([
            'status' => 1,
            'total_count' => $result->total_count,
            'current_page' => $result->data->current_page,
            'total_page' => $result->data->last_page,
            'per_page' => $result->data->per_page,
            'data' => $result->data->data
        ], 200);
    }

    function getHistory2($data)
    {
        $games = DB::table('trans_bet')->where('agent_code', $data['agent_code'])->where('user_code', $data['user_code'])->select(['history_id', 'agent_code', 'user_code', 'game_code', 'type', 'bet_money', 'win_money', 'txn_id', 'txn_type', 'user_start_balance', 'user_end_balance', 'agent_start_balance', 'agent_end_balance', 'created_at'])->paginate($data['perPage']);
        $count = DB::table('trans_bet')->where('agent_code', $data['agent_code'])->where('user_code', $data['user_code'])->count();
        return response()->json([
            'status' => 1,
            'total_count' => $count,
            'data' => $games
        ], 200);
    }

    function getBalance($data)
    {

        $agents = DB::table('agents')->where('agentCode', $data['agent_code'])->first();

        if (!isset($data['user_code'])) {
            return response()->json([
                'status' => 1,
                'msg' => 'SUCCESS',
                'agent' => [
                    'agent_code' => $data['agent_code'],
                    'balance' => $agents->balance
                ]
            ], 200);
        }

        $player = DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->first();

        if (!$player) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_USER'
            ], 200);
        }

        $agentscs = DB::table('agents')->where('agentCode', $data['agent_code'])->first();

        if ($agentscs->apiType != 1) {
            return response()->json([
                'status' => 1,
                'msg' => 'SUCCESS',
                'agent' => [
                    'agent_code' => $data['agent_code'],
                    'balance' => $agents->balance
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 1,
                'msg' => 'SUCCESS',
                'agent' => [
                    'agent_code' => $data['agent_code'],
                    'balance' => $agents->balance
                ],
                'user' => [
                    'user_code' => $data['user_code'],
                    'balance' => $player->balance
                ]
            ], 200);
        }
    }

    function balanceTopup($data)
    {
        $agentscs = DB::table('agents')->where('agentCode', $data['agent_code'])->first();

        if ($agentscs->apiType != 1) {
            return response()->json([
                'status' => 0,
                'msg' => 'AGENT_SEAMLESS'
            ], 200);
        }

        if (!$data['user_code']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETER'
            ], 200);
        }

        $player = DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->first();

        if (!$player) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_USER'
            ], 200);
        }

        $agents = DB::table('agents')->where('agentCode', $data['agent_code'])->first();

        if ($agents->balance < $data['amount']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INSUFFICIENT_AGENT_FUNDS'
            ], 200);
        }

        $agent_balance = $agents->balance - $data['amount'];

        DB::table('agents')->where('agentCode', $data['agent_code'])->update([
            'balance' => $agent_balance,
        ]);

        $player_balance = $player->balance + $data['amount'];

        DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->update([
            'balance' => $player_balance,
        ]);

        return response()->json([
            'status' => 1,
            'msg' => 'SUCCESS',
            'agent_balance' => $agents->balance,
            'user_balance' => $player_balance
        ], 200);
    }

    function balanceWithdraw($data)
    {
        $agentscs = DB::table('agents')->where('agentCode', $data['agent_code'])->first();

        if ($agentscs->apiType != 1) {
            return response()->json([
                'status' => 0,
                'msg' => 'AGENT_SEAMLESS'
            ], 200);
        }

        if (!$data['user_code']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETER'
            ], 200);
        }

        $player = DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->first();

        if (!$player) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_USER'
            ], 200);
        }

        if ($player->balance < $data['amount']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INSUFFICIENT_USER_FUNDS'
            ], 200);
        }

        $agents = DB::table('agents')->where('agentCode', $data['agent_code'])->first();
        $agent_balance = $agents->balance + $data['amount'];

        DB::table('agents')->where('agentCode', $data['agent_code'])->update([
            'balance' => $agent_balance,
        ]);

        $player_balance = $player->balance - $data['amount'];

        DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->update([
            'balance' => $player_balance,
        ]);

        return response()->json([
            'status' => 1,
            'msg' => 'SUCCESS',
            'agent_balance' => $agents->balance,
            'user_balance' => $player_balance
        ], 200);
    }

    function balanceWithdrawAll($data)
    {
        $agentscs = DB::table('agents')->where('agentCode', $data['agent_code'])->first();

        if ($agentscs->apiType != 1) {
            return response()->json([
                'status' => 0,
                'msg' => 'AGENT_SEAMLESS'
            ], 200);
        }

        if (isset($data['user_code'])) {
            $player = DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->first();

            if (!$player) {
                return response()->json([
                    'status' => 0,
                    'msg' => 'INVALID_USER'
                ], 200);
            }

            $ball_bef = $player->balance;

            $agents = DB::table('agents')->where('agentCode', $data['agent_code'])->first();
            $agent_balance = $agents->balance + $player->balance;

            DB::table('agents')->where('agentCode', $data['agent_code'])->update([
                'balance' => $agent_balance,
            ]);

            $player_balance = $player->balance - $player->balance;

            DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->update([
                'balance' => $player_balance,
            ]);

            return response()->json([
                'status' => 1,
                'msg' => 'SUCCESS',
                'agent' => [
                    'agent_code' => $data['agent_code'],
                    'balance' => $agents->balance
                ],
                'user' => [
                    'user_code' => $player->userCode,
                    'withdraw_amount' => $ball_bef,
                    'balance' => $player_balance
                ]
            ], 200);
        } else {
            $players = DB::table('users')->where('balance', '>', 0)->where('agentCode', $data['agent_code'])->where('apiType', 1)->get();

            foreach ($players as $player) {
                $ball_bef = $player->balance;
                $player_balance = $player->balance - $player->balance;

                $agents = DB::table('agents')->where('agentCode', $data['agent_code'])->first();
                $agent_balance = $agents->balance + $player->balance;

                DB::table('agents')->where('agentCode', $data['agent_code'])->update([
                    'balance' => $agent_balance,
                ]);

                DB::table('users')->where('balance', '>', 0)->where('agentCode', $data['agent_code'])->where('apiType', 1)->update([
                    'balance' => $player_balance,
                ]);
            }

            return response()->json([
                'status' => 1,
                'msg' => 'SUCCESS',
                'agent' => [
                    'agent_code' => $data['agent_code'],
                    'balance' => $agents->balance
                ],
                'user_list' => [
                    'user_code' => $player->userCode,
                    'withdraw_amount' => $ball_bef,
                    'balance' => $player_balance
                ]
            ], 200);
        }
    }

    function launch_game($data)
    {
        if (!$data['user_code']) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETER'
            ], 200);
        }

        $agentapi = DB::table('agents')->where('agentCode', $data['agent_code'])->first();

        $apis = ApiProvider::first();
        $player = DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 1)->first();

        if ($agentapi->apiType == 0) {

            $postArray = [
                'method' => 'user_balance',
                'agent_code' => $agentapi->agentCode,
                'agent_secret' => $agentapi->secretKey,
                'user_code' => $data['user_code']
            ];
            $jsonData = json_encode($postArray);


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $agentapi->siteEndPoint . '/gold_api',
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

            if (!isset($result->status)) {
                return response()->json([
                    'status' => 0,
                    'msg' => 'AGENT_SEAMLESS'
                ], 200);
            }

            if ($result->status == 0) {
                return response()->json([
                    'status' => 0,
                    'msg' => 'INSUFFICIENT_USER_FUNDS'
                ], 200);
            }

            $player_check = DB::table('users')->where('userCode', $data['user_code'])->where('agentCode', $data['agent_code'])->where('apiType', 0)->first();

            if (!$player_check) {
                DB::table('users')->insert([
                    'agentCode' => $data['agent_code'],
                    'userCode' => $data['user_code'],
                    'targetRtp' => 80,
                    'realRtp' => 80,
                    'balance' => 0,
                    'aasUserCode' => $data['user_code'],
                    'status' => 1,
                    'parentPath' => 1,
                    'totalDebit' => 0,
                    'totalCredit' => 0,
                    'apiType' => $agentapi->apiType,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
            }
        } else {
            if (!$player) {
                return response()->json([
                    'status' => 0,
                    'msg' => 'INVALID_USER'
                ], 200);
            }
        }

        $games_check = DB::table('game_lists')->where('game_code', $data['game_code'])->first();

        if (!$games_check){
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_GAME'
            ], 200);
        }

        if (is_numeric($games_check->game_id)) {
            $postArray = [
                'hall' => '3206004',
                'key' => '3206004',
                'login' => $data['user_code'],
                'gameId' => $games_check->game_id,
                'cmd' => 'openGame',
                'demo' => '0',
                'domain' => 'https://domain.com/',
                'cdnUrl' => '',
                'exitUrl' => 'https://panel.isomatslot.com/',
                'language' => 'en'
            ];
            $jsonData = json_encode($postArray);


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://tbs2api.aslot.net/API/openGame/',
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

            if ($result->status == 'success') {
                $games = $result->content->game;

                $hash = Str::uuid();
                DB::table('games_play')->insert([
                    'game_name' => $games_check->game_name,
                    'hash' => $hash,
                    'url' => $games->url,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);

                $urls = 'https://'.$games_check->provider.'.isomatslot.com/GamesLobby?session='.$hash;

                return response()->json([
                    'status' => 1,
                    'msg' => 'SUCCESS',
                    'launch_url' => $urls
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'msg' => 'INTERNAL_ERROR'
                ], 200);
            }

        } else {
            $partner = "test123";
		$currency = $agentapi->currency;
        function isMobile() {
            return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
        }
        // If the user is on a mobile device, redirect them
        if(isMobile()){
            $mobile = "true";
        } else {
            $mobile = "false";
        }
		$lang = "en";
		$lobbyurl ="https://google.com";
        $url = "https://test.partners.casinomobule.com/games.start?partner.alias=".$partner."&partner.session={$data['user_code']}&game.provider=pgsoft&game.alias={$games_check->game_id}&lang={$lang}&lobby_url={$lobbyurl}&currency={$currency}&mobile={$mobile}";
        $hash = Str::uuid();
                DB::table('games_play')->insert([
                    'game_name' => $games_check->game_name,
                    'hash' => $hash,
                    'url' => $url,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'created_at' => date("Y-m-d H:i:s")
                ]);

                $urls = 'https://'.$games_check->provider.'.isomatslot.com/GamesLobby?session='.$hash;
        return response()->json([
            'status' => 1,
            'msg' => 'SUCCESS',
            'launch_url' => $urls
        ], 200);
        }
    }

        function provider_list($data)
    {
        $datas = json_decode(file_get_contents('https://3xplay.co/provider_list.json'));
        return $datas;
    }

    function game_list($data)
    {
        if (!isset($data['provider_code'])) {
            return response()->json([
                'status' => 0,
                'msg' => 'INVALID_PARAMETER'
            ], 200);
        }

        $games = DB::table('game_lists')->where('provider_code', $data['provider_code'])->select('id','game_code','game_name','banner','status')->get();
        return response()->json([
            'status' => 1,
            'msg' => 'SUCCESS',
            'games' => $games
        ], 200);
    }

    function generateSigns($OperatorCode, $RequestTime, $MethodName, $SecretKey)
    {
        $sign = md5($OperatorCode . $RequestTime . $MethodName . $SecretKey);
        return $sign;
    }

    function api_create($username)
    {
        $url = "https://api.88xgames.com/v2/CreateMember.aspx?agent_token=c3b52b25c5f6d7f036fb636816813506&agent_code=xwgv59Xj&username={$username}";
        return $this->curl_get($url);
    }

    function api_balance($username)
    {
        $url = "https://api.88xgames.com/v2/GetBalance.ashx?agent_token=c3b52b25c5f6d7f036fb636816813506&agent_code=xwgv59Xj&username={$username}";
        return $this->curl_get($url);
    }

    function api_provider()
    {
        $url = "https://api.88xgames.com/v2/GetProviderList.aspx?agent_token=c3b52b25c5f6d7f036fb636816813506&agent_code=xwgv59Xj";
        return $this->curl_get($url);
    }

    function api_game()
    {
        $url = "https://api.88xgames.com/v2/GetGameList.aspx?agent_token=c3b52b25c5f6d7f036fb636816813506&agent_code=xwgv59Xj";
        return $this->curl_get($url);
    }

    function api_transaksi($username, $amount, $type)
    {
        $txid = $this->generateRandomString();
        $url = "https://api.88xgames.com/v2/MakeTransfer.aspx?agent_token=c3b52b25c5f6d7f036fb636816813506&agent_code=xwgv59Xj&username={$username}&amount={$amount}&type={$type}&txid={$txid}";
        return $this->curl_get($url);
    }

    function api_launch($username, $game_code, $game_provider)
    {
        $url = "https://api.88xgames.com/v2/LaunchGame.aspx?agent_token=c3b52b25c5f6d7f036fb636816813506&agent_code=xwgv59Xj&username={$username}&game_type=SeamlessGame&game_code={$game_code}&game_provider={$game_provider}&lang=en";
        return $this->curl_get($url);
    }


    function curl_get($endpoint)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function games_lobby(Request $request)
    {
        $games = DB::table('games_play')->where('hash', $request->session)->first();

        if (!$games) {
            abort(404);
        }
        return view('iframe', compact('games'));
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function provider_save(Request $request)
    {
        $postArray = [
            'hall' => '3206004',
            'key' => '3206004',
            'cmd' => 'gamesList',
            'cdnUrl' => '',
            'img' => 'game_img_2'
        ];
        $jsonData = json_encode($postArray);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://tbs2api.aslot.net/API/',
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

        $provider = $result->content->gameList;

        $data = json_decode(file_get_contents('https://partners.casinomobule.com/games.list'));

        $provider = $data->response;

        // return  $provider;

        foreach ($provider as $providers) {
            if ($providers->provider == "pgsoft") {
                DB::table('game_lists')->insert([
                    'provider' => $providers->provider,
                    'game_id' => $providers->alias,
                    'game_name' => $providers->title,
                    'game_code' => Str::random(6),
                    'game_type' => $providers->group_alias,
                    'provider_code' => strtoupper($providers->provider),
                    'banner' => "https://cdn.effective-solution.com/CasinoMobule/pgsoft/220-350/$providers->alias.png",
                    'status' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]);
            }
        }

        return "success";
    }
}
