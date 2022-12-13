<x-mail::message>
Hi {{$user['name']}}

<p>Below is your verification token .</p>
{{$token}}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
