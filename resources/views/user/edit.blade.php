<!DOCTYPE html>
<html lang="en">
<body>
    <form method="POST" action="{{url('/user/update/'.$user->id)}}">
    @csrf
    <h1>Update Your Profile</h1>
    Name : <input type="text" name="name" value="{{$user->name}}"><br>
    Email : <input type="email" name="email" value="{{$user->email}}"><br>
    Password : <input type="password" name="password" value="{{$user->password}}"><br><br>
    <input type="submit" value="UPDATE">
    <button><a href="/user/dashboard">CANCLE</a></button>
    </form>
</body>
</html>