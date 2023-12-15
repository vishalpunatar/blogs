<!DOCTYPE html>
<html lang="en">
<body>
    <form method="POST" action="{{url('/user/req-stored')}}">
        Your Name : <input type="text" name="name" value="{{$user->name}}"><br>
        Your Email : <input type="email" name="email" value="{{$user->email}}"><br>
        Description : <textarea name="description" placeholder="Enter Brief Description"></textarea><br><br>
        <input type="submit" value="SUBMIT">
        <a href="{{url('/user/dashboard')}}"><button>CANCLE</button></a>
    </form>
</body>
</html>