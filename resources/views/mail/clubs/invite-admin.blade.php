{{-- @formatter:off --}}

<x-mail::message>
# Great news!

{{ $inviterName }} has invited to become an admin of the club **{{ $club->name }}**. This will allow you manage this club's rewards, members, and more.

<x-mail::button :url="$url">
    Accept Invitation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
