@extends('layouts.dashboard')
@section('content')
<div>
     <!-- Button Start -->
     <div class="container-fluid pt-4 px-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="m-n2">
                    <button type="button" class="btn btn-primary m-2">
                        <a href="{{url()->previous()}}" style="color: #fff;">
                            <i class="fa fa-arrow-left me-2"></i>Go Back
                        </a>
                    </button>
                    <button type="button" class="btn btn-primary m-2">
                        <a href="{{route('feesetup.create')}}" style="color: #fff;">
                            <i class="fa fa-plus me-2"></i>Add
                        </a>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Button End -->

    <!-- Table Start -->
    <div class="container-fluid pt-4 px-4">
        <div class="row g-4">
            <div class="col-12">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">All</h6>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Name</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($fees as $fee)
                                <tr>
                                    <th scope="row">{{ $loop->index + 1 }}</th>
                                    <td>{{ $fee->name }}</td>
                                    <td>{{ $fee->amount }}</td>
                                    <td>
                                        <div class="nav-item dropdown">
                                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Update</a>
                                            <div class="dropdown-menu">
                                                <a href="{{route('feesetup.edit', $fee)}}" class="dropdown-item">Edit</a>
                                                <form id="deleteForm" action="{{ route('feesetup.destroy', $fee) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" class="dropdown-item btn btn-danger" onclick="deleteFeeSetup()">Delete</a>
                                                </form>
                                                
                                                
                                                
                                               
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                
                            @empty
                            <tr>
                                <td class="text-center" colspan="4">No Fee Setup</td>
                            </tr>
                            @endforelse
                            {{-- <tr>
                                <th scope="row">1</th>
                                <td>Azua Kator</td>
                                <td>Admin</td>
                                <td>
                                    <div class="nav-item dropdown">
                                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Update</a>
                                        <div class="dropdown-menu">
                                            <a href="edit_user.html" class="dropdown-item">Edit</a>
                                            <a href="delete_user.html" class="dropdown-item">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Table End -->
</div>
<script>
    function deleteFeeSetup() {
        if (confirm('Are you sure you want to delete this fee setup?')) {
            toastr.warning('Deleting fee setup...');
            document.getElementById('deleteForm').submit();
        } else {
            toastr.info('Deletion cancelled.');
        }
    }
</script>
@endsection