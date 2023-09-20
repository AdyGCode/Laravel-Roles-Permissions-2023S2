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


                    <table class="table">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Roles</th>
                            <th>Action</th>

                        </tr>

                        @foreach ($data as $key => $user)

                            <tr>

                                <td>{{ ++$i }}</td>

                                <td>{{ $user->name }}</td>

                                <td>{{ $user->email }}</td>

                                <td>

                                    @if(!empty($user->getRoleNames()))

                                        @foreach($user->getRoleNames() as $v)

                                            <label class="badge badge-success">{{ $v }}</label>

                                        @endforeach

                                    @endif

                                </td>

                                <td>

                                    <a class="rounded bg-gray-500 text-white"
                                       href="{{ route('users.show',$user->id) }}">Show</a>

                                    <a class="rounded bg-gray-500 text-white"
                                       href="{{ route('users.edit',$user->id) }}">Edit</a>

                                    Delete
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
