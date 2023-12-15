<!DOCTYPE html>
<html lang="en"><br><br>
@include('super-admin.layout.header')
<br>
<body>
    <table border="5">
        <thead>
            <th>ID</th>
            <th>NAME</th>
            <th>EMAIL</th>
            <th>ROLE</th>
            <th>STATUS</th>
        </thead>
        @foreach ($user as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->role}}</td>
                <td>{{$user->status}}</td>
                <td><button><a href="/userupdate">EDIT</a></button></td>
                <td><button><a href="#">DELETE</a></button></td>
            </tr>
        @endforeach
    </table>
</body>
</html>