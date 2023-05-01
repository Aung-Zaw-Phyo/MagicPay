@extends('backend.layouts.app')

@section('title', 'Wallets')
@section('wallet-active', 'mm-active')

@section('content')

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="metismenu-icon pe-7s-users"></i>
            </div>
            <div>
                Wallets
            </div>
        </div>
    </div>
</div> 

<div class="pt-3">
    <a href="{{ url('admin/wallet/add/amount') }}" class="btn btn-success btn-theme content-fm me-3"><i class="fas fa-plus-circle"></i> Add Amount</a>
    <a href="{{ url('admin/wallet/reduce/amount') }}" class="btn btn-primary btn-theme content-fm me-3"><i class="fas fa-minus-circle"></i> Reduce Amount</a>
    <a href="{{ url('') }}" class="btn btn-dark btn-theme content-fm"><i class="fas fa-file-pdf"></i> Download</a>
</div>

<div class="content pt-3">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive w-100">
                <table class="table table-bordered content-fm w-100" id="datatable">
                    <thead>
                        <tr class="bg-light">
                            <th>Account Number</th>
                            <th>Account Person</th>
                            <th>Amount</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
    
@endsection

@section('script')
<script>
    $(document).ready(function () {
        let table = $('#datatable').DataTable({
            lengthMenu: [
                [ 5, 10, 15, 50, 100],
                [ '5 rows', '10 rows','15 rows', '50 rows', '100 rows']
            ],
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    orientation: 'A4',
                    pageSize: 'LEGAL',
                    className: 'btn btn-dark btn-lg mb-2',
                    title: 'user_wallets',
                    exportOptions: {
                        columns: [0, 1, 2]
                    },
                    customize: function (doc) {
                        //Remove the title created by datatTables
                        doc.content.splice(0,1);
                        var now = new Date();
                        var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();
                        var datetime = now.getDate() + "/"
                                                + (now.getMonth()+1)  + "/" 
                                                + now.getFullYear() + " @ "  
                                                + now.getHours() + ":"  
                                                + now.getMinutes() + ":" 
                                                + now.getSeconds();
                        doc.pageMargins = [20,60,20,40];
                        doc.defaultStyle.fontSize = 8;
                        doc.styles.tableHeader.fontSize = 8;
                        doc.styles.tableBodyEven.alignment = 'start';
                        doc.styles.tableBodyOdd.alignment = 'start';

                        doc['header']=(function() {
                            return {
                                columns: [

                                    {
                                        alignment: 'left',
                                        text: 'Users Wallets ',
                                        fontSize: 16,
                                    },
                                    {
                                        alignment: 'right',
                                        fontSize: 14,
                                        text: 'Report Time: ' + datetime
                                    }
                                ],
                                margin: [20, 20, 20 , 0]
                            }
                        });
 
                        doc['footer']=(function(page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'left',
                                        text: ''
                                    },
                                    {
                                        alignment: 'right',
                                        text: ['page ', { text: page.toString() },  ' of ', { text: pages.toString() }]
                                    }
                                ],
                                margin: 20
                            }
                        });
 
                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) { return .5; };
                        objLayout['vLineWidth'] = function(i) { return .5; };
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        objLayout['paddingLeft'] = function(i) { return 4; };
                        objLayout['paddingRight'] = function(i) { return 4; };
                        doc.content[0].layout = objLayout;
                        doc.content[0].table.widths = ['25%', '55%', '20%'];
                    }
                },
                {
                    text: "<i class='fas fa-sync'></i> Refresh",
                    className: 'btn bn-theme btn-lg mb-2',
                    action: function (e, dt, node, config) {
                        dt.ajax.reload(null, false);
                    }
                },
                {
                    extend: 'pageLength',
                    className: 'btn btn-danger btn-lg mb-2',
                }
            ],





            processing: true,
            serverSide: true,
            ajax: '/admin/wallet/datatable/ssd',
            columns: [
                {
                    data: "account_number",
                    name: "account_number"
                },
                {
                    data: "account_person",
                    name: "account_person"
                },
                {
                    data: "amount",
                    name: "amount"
                },
                
                {
                    data: "created_at",
                    name: "created_at"
                },
                {
                    data: "updated_at",
                    name: "updated_at"
                },
                
            ], 
            order: [
                [4, 'desc']
            ],
            columnDefs: [{
                targets: "no-sort",
                sortable: false
            }],
        });

    });
</script>
@endsection