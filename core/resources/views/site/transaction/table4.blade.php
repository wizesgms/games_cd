<div class="card">
    <div class="card-datatable table-responsive">
        <table id="tbl_agentDateRange" class="table table-bordered table-sm table-hover">
            <thead>
                <tr>
                    <th style="text-align: center !important;" colspan="7"></th>
                    <th style="text-align: center !important;" colspan="3">Member</th>
                    <th style="text-align: center !important;" colspan="3">Upline</th>
                </tr>
                <tr>
                    <th style="text-align: center;">Wager ID</th>
                    <th style="text-align: center;">Member</th>
                    <th style="text-align: center;">Product</th>
                    <th style="text-align: center;">Game</th>
                    <th style="text-align: center;">Total Record</th>
                    <th style="text-align: center;">Total Bet</th>
                    <th style="text-align: center;">Total Valid Bet</th>
                    <th style="text-align: center;">Total Prog jp</th>
                    <th style="text-align: center;">Total Payout</th>
                    <th style="text-align: center;">Total Win Lose</th>
                    <th style="text-align: center !important;">W/L</th>
                    <th style="text-align: center !important;">Comm</th>
                    <th style="text-align: center !important;">Total</th>
                    <th style="text-align: center !important;">W/L</th>
                    <th style="text-align: center !important;">Comm</th>
                    <th style="text-align: center !important;">Total</th>
                    <th style="text-align: center !important;">Created On</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user as $item)
                <tr>
                    <td style="text-align: center;"><a href="#" onclick="viewWagerDetail('{{ $item->WagerID }}')"> {{
                            $item->WagerID }}</a></td>
                    <td style="text-align: left; max-width: 700px; word-wrap: break-word;">
                        @if(!$item)
                        NULL
                        @else
                        MemberName: {{ $item->MemberName }}
                        Currency: IDR
                        @endif
                    </td>
                    <td style="text-align: left; max-width: 700px; word-wrap: break-word;">
                        @if(!$item)
                        NULL
                        @else
                        MemberName: {{ $item->ProductID }}
                        GameType: {{ $item->GameType }}
                        Currency: IDR
                        @endif
                    </td>
                    <td style="text-align: left; max-width: 700px; word-wrap: break-word;">
                        @if(!$item)
                        NULL
                        @else
                        GameID: {{ $item->GameID }}
                        GameRoundID: {{ $item->GameRoundID }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if(!$item)
                        0.00
                        @else
                        {{ $item->count() }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if(!$item)
                        0.00
                        @else
                        {{ $item->count() }}
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if(!$item)
                        0.00
                        @else
                        {{ $item->sum('ValidBetAmount') }}.00
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if(!$item)
                        0.00
                        @else
                        {{ $item->sum('BetAmount') }}.00
                        @endif
                        </td>
                    <td style="text-align: right;">@if(!$item)
                        0.00
                        @else
                        {{ $item->sum('JPBet') }}.00
                        @endif
                        </td>
                    <td style="text-align: right;">@if(!$item)
                        0.00
                        @else
                        {{ $item->sum('PayoutAmount') }}.00
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if(!$item)
                        0.00
                        @else
                        {{ $item->sum('TransactionAmount') }}.00
                        @endif
                    </td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">
                        @if(!$item)
                        0.00
                        @else
                        {{ $item->sum('TransactionAmount') }}.00
                        @endif
                    </td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">
                        @if(!$item)
                        null
                        @else
                        {{ $item->created_at }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: center;">{{ $transaction->count() }}</td>
                    <td style="text-align: right;">{{ $transaction->sum('ValidBetAmount') }}.00</td>
                    <td style="text-align: right;">{{ $transaction->sum('BetAmount') }}.00</td>
                    <td style="text-align: right;">{{ $transaction->sum('JPBet') }}.00</td>
                    <td style="text-align: right;">{{ $transaction->sum('PayoutAmount') }}.00</td>
                    <td style="text-align: right;">{{ $transaction->sum('TransactionAmount') }}.00</td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">{{ $transaction->sum('TransactionAmount') }}.00</td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">{{ $transaction->sum('TransactionAmount') }}.00</td>
                    <td style="text-align: center;"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#tbl_agentDateRange').DataTable({
                searching: false,
                paging: true,
                lengthMenu: [[25, 50.00, 10.00, -1], [25, 50.00, 10.00, "All"]],
                Language: {
                    LengthMenu: 'Show _MENU_ entries',
                    Info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    InfoEmpty: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    ZeroRecords: 'No data available in table',
                    Paginate: {
                        sFirst: "First",
                        sPrevious: "Previous",
                        sNext: "Next Page",
                        sLast: "Last"
                }
            }
        });
    });


    function viewWagerDetail(wagerID) {

$.ajax({
    type: "POST",
    url: "/WinLoseReport/GetWinLoseDetail",
    data: {
        "WagerID": wagerID
    },
    datatype: "json",
    success: function (data) {

        if (data.ErrorCode == 0) {
            window.open(data.Url, 'popUpWindow', 'height=720,width=480,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no, status=yes');
        }
    }
});
}


    function viewProduct(productID) {

        $("#Provider").val(productID);

        $.ajax({
            type: "POST",
            url: "/WinLoseReport/SearchWinLoseReport_V2",
            data: $("#form_search").serialize(),
            datatype: "json",
            success: function (data) {
                $("#div_table").html(data);
            }
        });
    }

    function viewAgent(agentCode) {

        //$("#ParentAgentCode").val(agentCode);
        $("#AgentCode").val(agentCode);

        $.ajax({
            type: "POST",
            url: "/WinLoseReport/SearchWinLoseReport_V2",
            data: $("#form_search").serialize(),
            datatype: "json",
            success: function (data) {
                $("#div_table").html(data);

                $("#ParentAgentCode").val('');

            }
        });
    }

    function viewDirectMember(memberName) {

        $("#MemberName").val(memberName);

        $.ajax({
            type: "POST",
            url: "/WinLoseReport/SearchWinLoseReport_V2",
            data: $("#form_search").serialize(),
            datatype: "json",
            success: function (data) {
                $("#div_table").html(data);
            }
        });
    }

    function goToAgent(agentCode) {

        $("#ParentAgentCode").val(agentCode);

        $.ajax({
            type: "POST",
            url: "/WinLoseReport/SearchWinLoseReport_V2",
            data: $("#form_search").serialize(),
            datatype: "json",
            success: function (data) {
                $("#div_table").html(data);

                $("#ParentAgentCode").val('');
            }
        });
    }
</script>
