<x-mail::message>
<p>Hello {{$user}} ,</p>

<p>Below is your login credentials tot eh platform</p>
<p>Email : {{$email}}</p>
<p>Password : {{$password}}</p>

<p>{{config('app.frontend_url')}}</p>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
