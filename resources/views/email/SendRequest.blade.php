<h2>Hello</h2>
<p>a new user has requested to become a publisher following are the user's details</p>
<p>ID: <b>{{$user->id}}</b></p>
<p>Name: <b>{{$publisher->name}}</b></p>
<p>Email: <b>{{$publisher->email}}</b></p>
<p>Description: {{$publisher->description}}</p>
<p>To approve the request click on Accept Button</p>
<button><a href={{ url('/super-admin/pub/req/approval/id/'.$publisher->id.'/token/'.$token) }}>Accept</a></button> 