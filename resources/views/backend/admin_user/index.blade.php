@extends('backend.layouts.app')

@section('title', 'Admin Users')
@section('admin-user-active', 'mm-active')

@section('content')

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="metismenu-icon pe-7s-users"></i>
            </div>
            <div>
                Admin Users
            </div>
        </div>
    </div>
</div> 

<div class="pt-3">
    <a href="{{ route('admin.admin-user.create') }}" class="btn btn-primary content-fm"><i class="fa-solid fa-circle-plus"></i> Create Admin User</a>
</div>
<div class="content pt-3">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive w-100">
                <table class="table table-bordered content-fm w-100" id="datatable">
                    <thead>
                        <tr class="bg-light">
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Ip</th>
                            <th class="no-sort">User_agent</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Action</th>
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
            processing: true,
            serverSide: true,
            ajax: '/admin/admin-user/datatable/ssd',
            columns: [
                {
                    data: "name",
                    name: "name"
                },
                {
                    data: "email",
                    name: "email"
                },
                {
                    data: "phone",
                    name: "phone"
                },
                {
                    data: "ip",
                    name: "ip"
                },
                {
                    data: "user_agent",
                    name: "user_agent"
                },
                {
                    data: "created_at",
                    name: "created_at"
                },
                {
                    data: "updated_at",
                    name: "updated_at"
                },
                {
                    data: "action",
                    name: "action"
                }
            ], 
            order: [
                [6, 'desc']
            ],
            columnDefs: [{
                targets: "no-sort",
                sortable: false
            }]
        });

        $(document).on('click', '.delete', function (e){
            e.preventDefault();
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure, you want to delete?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                denyButtonText: `Don't save`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/admin/admin-user/" + id,
                        type: "DELETE",
                        success: function () {
                            table.ajax.reload()
                        }
                    })
                }
            })
            
        })
    });
</script>
@endsection