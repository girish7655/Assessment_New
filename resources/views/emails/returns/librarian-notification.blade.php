@component('mail::message')
# Book Return Alert

A book has been returned to the library.

**Book Details:**
- Title: {{ $book->title }}
- ISBN: {{ $book->isbn }}

**User Details:**
- Name: {{ $user->name }}
- Email: {{ $user->email }}

**Return Information:**
- Return Date: {{ $checkout->return_date->format('F j, Y') }}

@component('mail::button', ['url' => route('books.show', $book->id)])
View Book Details
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent