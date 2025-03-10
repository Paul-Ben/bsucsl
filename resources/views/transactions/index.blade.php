@extends('layouts.dashboard')
@section('content')
<div>
     <!-- Button Start -->
     <div class="container-fluid pt-4 px-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="m-n2 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary m-2">
                        <a href="{{url()->previous()}}" style="color: #fff;">
                            <i class="fa fa-arrow-left me-2"></i>Go Back
                        </a>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Button End -->

    <!-- Table Start -->
    {{-- <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">All</h6>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Name</th>
                                <th scope="col">Jamb No</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Faculty</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Trx_Ref</th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <th scope="row">{{ $loop->index + 1 }}</th>
                                    <td>{{ $transaction->name }}</td>
                                    <td>{{ $transaction->reg_number }}</td>
                                    <td>{{ $transaction->phone_number }}</td>
                                    <td>{{ $transaction->faculty }}</td>
                                    <td>{{ $transaction->amount }}</td>
                                    <td>{{ $transaction->tx_ref }}</td>
                                </tr>
                                
                            @empty
                            <tr>
                                <td class="text-center" colspan="4">No Transactions</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div> --}}
    <!-- Table End -->
{{--     
   <!-- Table Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">All</h6>
                <div class="d-flex justify-content-between mb-4">
                    <div class="form-group">
                        <input type="search" class="form-control" id="search-input" placeholder="Search...">
                    </div>
                    <button class="btn btn-primary" id="download-btn">Download CSV</button>
                </div>
                <table id="datatable" class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">Name</th>
                            <th scope="col">Jamb No</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Faculty</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Trx_Ref</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <th scope="row">{{ $loop->index + 1 }}</th>
                                <td>{{ $transaction->name }}</td>
                                <td>{{ $transaction->reg_number }}</td>
                                <td>{{ $transaction->phone_number }}</td>
                                <td>{{ $transaction->faculty }}</td>
                                <td>{{ $transaction->amount }}</td>
                                <td>{{ $transaction->tx_ref }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="4">No Transactions</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Table End -->

<!-- Datatables Script -->
<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "search": "Search:",
                "zeroRecords": "No records found",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records available",
                "infoFiltered": "(filtered from _MAX_ total records)",
                "lengthMenu": "Show _MENU_ records",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });

        // Download CSV button click event
        $('#download-btn').on('click', function() {
            var table = $('#datatable').DataTable();
            var data = table.buttons(0).trigger();
            var csv = table.buttons(0).data();
            var blob = new Blob([csv], { type: 'text/csv' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'transactions.csv';
            a.click();
        });
    });
</script>

<!-- Datatables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script> 
</div> --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<!-- Table Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">All</h6>
                <div class="d-flex justify-content-between mb-4">
                    <div class="form-group">
                        <input type="search" class="form-control" id="search-input" placeholder="Search...">
                    </div>
                    <button class="btn btn-primary" id="download-btn">Download CSV</button>
                </div>
                <table id="datatable" class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th scope="col">S/N</th>
                            <th scope="col">Name</th>
                            <th scope="col">Jamb No</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Faculty</th>
                            <th scope="col">Status</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Trx_Ref</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <th scope="row">{{ $loop->index + 1 }}</th>
                                <td>{{ $transaction->name }}</td>
                                <td>{{ $transaction->reg_number }}</td>
                                <td>{{ $transaction->phone_number }}</td>
                                <td>{{ $transaction->faculty }}</td>
                                <td>{{ $transaction->paymentStatus }}</td>
                                <td>{{ $transaction->amount }}</td>
                                <td>{{ $transaction->tx_ref }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="7">No Transactions</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Table End -->

<!-- DataTables CSS -->
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css"> --}}

<!-- DataTables JS and Buttons Extension -->
{{-- <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script> --}}

<!-- Datatables Script -->
{{-- <script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "search": "Search:",
                "zeroRecords": "No records found",
                "info": "Showing _START_ to _END_ of _TOTAL_ records",
                "infoEmpty": "No records available",
                "infoFiltered": "(filtered from _MAX_ total records)",
                "lengthMenu": "Show _MENU_ records",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "dom": 'Bfrtip', // Add buttons to the DOM
            "buttons": [
                {
                    extend: 'csv',
                    text: 'Download CSV',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: ':visible' // Export only visible columns
                    }
                }
            ]
        });

        // Trigger CSV download when the custom button is clicked
        $('#download-btn').on('click', function() {
            $('#datatable').DataTable().button('.buttons-csv').trigger();
        });
    });
</script> --}}
<script type="text/javascript">
    $(function () {
          
      var table = $('.data-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('transactions') }}",
          columns: [
              {data: 'id', name: 'id'},
              {data: 'name', name: 'name'},
              {data: 'email', name: 'email'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
          
    });
  </script>
@endsection