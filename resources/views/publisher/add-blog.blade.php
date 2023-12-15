<!DOCTYPE html>
<html lang="en">
<body>
    <form method="POST" action="" enctype="multipart/form-data">
    @csrf
    <h1>Create Your Blog</h1>
    Enter Blog Title <br>
    <input type="text" name="title">
    @error('title')
        {{$message}}
    @enderror<br><br>
    Content of Your Blog <br>
    <textarea name="content"></textarea>
    @error('content')
        {{$message}}
    @enderror<br><br>
    Pick Image For Your Blog <br>
    <input type="file" name="image">
    @error('image')
        {{$message}}
    @enderror<br><br>
    <input type="submit" value="SUBMIT">
    <button><a href="/publisher/dashboard">CANCLE</a></button>
    </form>
</body>
</html>