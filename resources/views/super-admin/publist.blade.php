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
        @foreach ($publisher as $publisher)
            <tr>
                <td>{{$publisher->id}}</td>
                <td>{{$publisher->name}}</td>
                <td>{{$publisher->email}}</td>
                <td>{{$publisher->role}}</td>
                <td>{{$publisher->status}}</td>
                <td><button><a href="/userupdate">EDIT</a></button></td>
                <td><button><a href="#">DELETE</a></button></td>
            </tr>
        @endforeach
    </table>
</body>
</html>