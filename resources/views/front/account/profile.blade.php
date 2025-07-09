@extends('front.layouts.app')


@section('main')
    <section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Account Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.sidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4">
                    <form action="" method="post" id="userForm" name="userForm">
                        @csrf
                        @method('PUT')
                        <div class="card-body  p-4">
                            <h3 class="fs-4 mb-1">My Profile</h3>
                            <div class="mb-4">
                                <label for="" class="mb-2">Name*</label>
                                <input type="text" name="name" placeholder="Enter Name" class="form-control" value="{{ $user->name }}">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Email*</label>
                                <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="{{ $user->email }}">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Designation</label>
                                <input type="text" name="designation" id="designation" placeholder="Designation" class="form-control" value="{{ $user->designation }}">                                
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Mobile</label>
                                <input type="text" name="mobile" id="mobile" placeholder="Mobile" class="form-control" value="{{ $user->mobile }}">                                
                            </div>                        
                        </div>
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

                <div class="card border-0 shadow mb-4">
                    <form action="" method="POST" id="changePassworForm" name="changePassworForm">
                        <div class="card-body p-4">
                            <h3 class="fs-4 mb-1">Change Password</h3>
                            <div class="mb-4">
                                <label for="" class="mb-2">Old Password*</label>
                                <input type="password" name="old_password" id="old_password" placeholder="Old Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">New Password*</label>
                                <input type="password" name="new_password" id="new_password" placeholder="New Password" class="form-control">
                                <p></p>
                            </div>
                            <div class="mb-4">
                                <label for="" class="mb-2">Confirm Password*</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" class="form-control">
                                <p></p>
                            </div>                        
                        </div>
                        <div class="card-footer  p-4">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>   

            </div>
        </div>
    </div>
</section>

@endsection

@section('customJs')
<script type="text/javascript">
    $("#userForm").submit(function(e){
        e.preventDefault(); // Stop form from submitting normally

        $.ajax({
            url: '{{ route("account.updateProfile") }}',
            type: 'PUT',
            dataType: 'json',
            data: $(this).serialize(), // serialize current form
            success: function(response) {
                if(response.status === true){

                    $("input[name='name']").removeClass('is-invalid')   
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');

                    $("input[name='email']").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');
                        
                    window.location.href="{{ route('account.profile') }}";
                    // Optionally reload page or show a success message
                } else {
                    var errors = response.errors;

                    if(errors.name){
                        $("input[name='name']").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.name);
                    } else {
                        $("input[name='name']").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    if(errors.email){
                        $("input[name='email']").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.email);
                    } else {
                        $("input[name='email']").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }
                }
            },
            error: function(xhr) {
                console.log("Error occurred:", xhr.responseText);
                alert("An error occurred while processing the request.");
            }
        });
    });


     $("#changePassworForm").submit(function(e){
        e.preventDefault(); // Stop form from submitting normally

        $.ajax({
            url: '{{ route("account.updatePassword") }}',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(), // serialize current form
            success: function(response) {
                if(response.status === true){

                    $("input[name='name']").removeClass('is-invalid')   
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');

                    $("input[name='email']").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');
                        
                    window.location.href="{{ route('account.profile') }}";
                    // Optionally reload page or show a success message
                } else {
                    var errors = response.errors;

                    if(errors.old_password){
                        $("input[name='old_password']").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.old_password);
                    } else {
                        $("input[name='old_password']").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    if(errors.new_password){
                        $("input[name='new_password']").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.new_password);
                    } else {
                        $("input[name='new_password']").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }

                    if(errors.confirm_password){
                        $("input[name='confirm_password']").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback')
                            .html(errors.confirm_password);
                    } else {
                        $("input[name='confirm_password']").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback')
                            .html('');
                    }
                }
            },
            error: function(xhr) {
                console.log("Error occurred:", xhr.responseText);
                alert("An error occurred while processing the request.");
            }
        });
    });
</script>
@endsection
