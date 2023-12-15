<!DOCTYPE html>
<html lang="en">
<body>
    <table border="5">
        <thead>
            <th>Sr.no.</th>
            <th>TITLE</th>
            <th>CONTENT</th>
            <th>IMAGE</th>
        </thead>
        @foreach ($blog as $blog)
            <tr>
                <td>{{$loop->index+1}}</td>
                <td>{{$blog->title}}</td>
                <td>{{$blog->content}}</td>
                <td>
                    <img src="{{asset('/public/image/'.$blog->image)}}" height="40px" width="80px">
                </td>
                <td><button><a href="{{url('/publisher/blogdata/'.$blog->id)}}">VIEW</a></button></td>
                <td><button><a href="{{url('/publisher/edit-blog/'.$blog->id)}}">EDIT</a></button></td>
                <td><button><a href="{{url('/publisher/delete-blog/'.$blog->id)}}">DELETE</a></button></td>
            </tr>
        @endforeach
    </table>
</body>
</html>