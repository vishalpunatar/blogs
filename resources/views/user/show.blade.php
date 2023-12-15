<!DOCTYPE html>
<html lang="en">
<body>
    Name : {{$user->name}} <br>
    Email : {{$user->email}} <br>
    <a href="{{url('/user/edit/')}}"><button>EDIT</button></a>
    <a href="{{url('user/dashboard')}}"><button>BACK</button></a>
</body>
</html>