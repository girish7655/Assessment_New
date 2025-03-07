@component('mail::message')
# Welcome to the Library

Dear {{ $user->name }},

Thank you for registering with our library system. We're excited to have you on board!

@if($user->isLibrarian())
You have been registered as a librarian. You can now manage books in the system.
@else
You can now browse our collection of books and check them out.
@endif

@component('mail::button', ['url' => route('login')])
Login to Your Account
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent