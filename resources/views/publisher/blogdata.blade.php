<!DOCTYPE html>
<html lang="en">
<body>
    <form method="GET" action="/">
        @csrf
        <h1>{{$blog->title}}</h1>
        <img src="{{asset('/public/image/'.$blog->image)}}">
        <p>{{$blog->content}}</p>
    </form>
</body>
</html>