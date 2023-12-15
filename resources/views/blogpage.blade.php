<!DOCTYPE html>
<html lang="en">
<body>
    <form method="GET" action="/" enctype="multipart/form-data">
    @csrf
        <center><div class="" col="12">
        @foreach ($blog as $blog)
            <div>
                <img src="{{asset('public/image'.$blog->image)}}" height="200px" width="350px">
                <h2>{{$blog->title}}</h2>
                <a href="{{url('/blogdetail/'.$blog->id)}}"><button>View Blog</button></a>
            </div><br><br>
        @endforeach
    </div></center>   
    </form>
</body>
</html>

{{-- 'storage/app/public/image/' --}}