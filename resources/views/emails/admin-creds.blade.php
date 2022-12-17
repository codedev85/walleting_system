<x-mail::message>
<p>Hello {{$user['name']}}</p>

<p>Below is your login credentials to the portal</p>

<p>Email : {{$user['email']}}</p>
<p>Password : {{$pass}}</p>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
