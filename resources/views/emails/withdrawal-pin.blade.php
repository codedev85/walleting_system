<x-mail::message>
Hi {{$user['name']}}

<p>Below is your transaction pin to cash-out , <br> its only valid for 15 mins</p>
{{$pin}}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
