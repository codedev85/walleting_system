<x-mail::message>
<p>Hello {{$user['name']}},</p>

<p>Your {{$type}} has been activated .</p>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
