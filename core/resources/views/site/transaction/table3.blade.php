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
                    <th style="text-align: center;">Member Name</th>
                    <th style="text-align: center;">Product</th>
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
                </tr>
            </thead>
            <tbody>
                @foreach ($user as $item)
                <tr>
                    <td style="text-align: center;"><a href="#" onclick="viewDirectMember('{{ $item->ext_player }}')"> {{
                            $item->ext_player }}</a></td>
                    <td style="text-align: left; max-width: 700px; word-wrap: break-word;">
                        @if(!$item->transaction)
                        NULL
                        @else
                        Product: {{ $item->transaction->ProductID }}
                        GameType: {{ $item->transaction->GameType }}
                        Currency: IDR
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if(!$item->transaction)
                        0.00
                        @else
                        {{ $item->transaction->count() }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if(!$item->transaction)
                        0.00
                        @else
                        {{ $item->transaction->count() }}
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if(!$item->transaction)
                        0.00
                        @else
                        {{ $item->transaction->sum('ValidBetAmount') }}.00
                        @endif

                    </td>
                    <td style="text-align: right;">
                        @if(!$item->transaction)
                        0.00
                        @else
                        {{ $item->transaction->sum('BetAmount') }}.00
                        @endif
                        </td>
                    <td style="text-align: right;">@if(!$item->transaction)
                        0.00
                        @else
                        {{ $item->transaction->sum('JPBet') }}.00
                        @endif
                        </td>
                    <td style="text-align: right;">@if(!$item->transaction)
                        0.00
                        @else
                        {{ $item->transaction->sum('PayoutAmount') }}.00
                        @endif
                    </td>
                    <td style="text-align: right;">
                        @if(!$item->transaction)
                        0.00
                        @else
                        {{ $item->transaction->sum('TransactionAmount') }}.00
                        @endif
                    </td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">0.00</td>
                    <td style="text-align: right;">
                        @if(!$item->transaction)
                        0.00
                        @else
                        {{ $item->transaction->sum('TransactionAmount') }}.00
                        @endif
                    </td>
                    <td style="text-align: right;">0.00</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
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
