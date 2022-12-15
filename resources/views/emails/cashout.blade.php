<x-mail::message>
<p>hi {{$user['name']}}</p>

<p>You have successfully withdrawn an amount of N {{$amount}} from your wallet to your bank</p>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
