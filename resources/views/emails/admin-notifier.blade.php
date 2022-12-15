<x-mail::message>
<p>Hello Admin,</p>

<p>A user with the email {{$user['email']}} has just withdrawn a sum of N {{$amount}} to his bank.<br> Account details below</p>

<p>Bank Name: {{$bank['bank']['bank_name']}}</p>
<p>Account Name: {{$bank['account_name']}}</p>
<p>Account Number :{{$bank['account_number']}}</p>


Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
