<x-mail::message>
<p>Hello {{$user['name']}}</p>

<p>Admin has credited your wallet with the sum of  N {{$amount}}</p>



Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
