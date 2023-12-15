<!DOCTYPE html>
<html lang="en">
<body>
    <form method="POST" action="/login">
        @csrf
        <h1>Enter Login Details</h1>
        Enter email : <input type="email" name="email"><br>
        @error('email')
            {{$message}}
        @enderror
        Enter password : <input type="password" name="password"><br><br>
        @error('password')
            {{$message}}
        @enderror
        <input type="submit" value="LOGIN"><br>
        New Registration <a href="{{url('signup')}}">CLICK HERE</a> 
    </form>
</body>
</html>