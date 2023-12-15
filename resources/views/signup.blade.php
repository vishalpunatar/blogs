<!DOCTYPE html>
<html lang="en">
<body>
    <form method="POST" action="/store">
        @csrf
        <h1>Enter Your Details</h1>
        Enter Name : <input type="text" name="name">
        @error('name')
            {{$message}}
        @enderror<br>
        Enter email : <input type="email" name="email">
        @error('email')
            {{$message}}
        @enderror<br>
        Enter password : <input type="password" name="password">
        @error('password')
            {{$message}}
        @enderror<br><br>
        <input type="submit" value="SIGN-UP">
    </form>
    Already Have an<a href="{{url('/login')}}"> Account</a>
</body>
</html>