<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="pull-right">
                        <a class="btn btn-success" href="{{ route('users.create') }}"> Create New User</a>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="pull-right">

                        <a class="btn btn-primary" href="{{ route('users.index') }}"> Back</a>

                    </div>

                </div>

            </div>


            @if (count($errors) > 0)

                <div class="alert alert-danger">

                    <strong>Whoops!</strong> There were some problems with your input.<br><br>

                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {::open(array('route' => 'users.store','method'=>'POST')) !!}

            <div class="row">

                <div class="col-xs-12 col-sm-12 col-md-12">

                    <div class="form-group">

                        <strong>Name:</strong>

                        {!::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}

                    </div>

                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">

                    <div class="form-group">

                        <strong>Email:</strong>

                        {::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}

                    </div>

                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">

                    <div class="form-group">

                        <strong>Password:</strong>

                        {::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}

                    </div>

                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">

                    <div class="form-group">

                        <strong>Confirm Password:</strong>

                        { :password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control'))
                        !!}

                    </div>

                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">

                    <div class="form-group">

                        <strong>Role:</strong>

                        {::select('roles[]', $roles,[], array('class' => 'form-control','multiple')) !!}

                    </div>

                </div>

                <div class="sm:w-full pr-4 pl-4 sm:w-full pr-4 pl-4 md:w-full pr-4 pl-4 text-center">

                    <button type="submit" class="btn btn-primary">Submit</button>

                </div>

            </div>


        </div>
    </div>
</x-app-layout>
