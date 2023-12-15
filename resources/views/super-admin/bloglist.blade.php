<!DOCTYPE html>
<html lang="en"><br><br>
@include('super-admin.layout.header')
<br>
<body>
    <table border="5">
        <thead>
            <th>ID</th>
            <th>USER_ID</th>
            <th>TITLE</th>
            <th>STATUS</th>
        </thead>
        @foreach ($blog as $blog)
            <tr>
                <td>{{$blog->id}}</td>
                <td>{{$blog->user_id}}</td>
                <td>{{$blog->title}}</td>
                <td>{{$blog->status}}</td>
                <td><button><a href="#">EDIT</a></button></td>
                <td><button><a href="#">DELETE</a></button></td>
            </tr>
        @endforeach
    </table>
</body>
</html>