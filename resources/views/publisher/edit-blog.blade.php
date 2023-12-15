<!DOCTYPE html>
<html lang="en">
<body>
    <form method="POST" action="{{url('/publisher/update-blog/'.$blog->id)}}">  {{--publisher/blog-update/{{$blog->id}}--}}
    @csrf
    <h1>Modify Your Blog</h1>
    <img src="{{asset('storage/app/public/image/'.$blog->image)}}"><br>
    Blog Title : <input type="text" name="title" value="{{$blog->title}}"><br>
    Content : <textarea name="content" >{{$blog->content}}</textarea><br><br>
    <input type="submit" value="UPDATE">
    <button><a href="/publisher/myblogs">CANCLE</a></button>
    </form>
</body>
</html>