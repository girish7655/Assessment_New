@component('mail::message')
# New Book Checkout Alert

A book has been checked out from the library.

**Book Details:**
- Title: {{ $book->title }}
- ISBN: {{ $book->isbn }}

**User Details:**
- Name: {{ $user->name }}
- Email: {{ $user->email }}

**Checkout Information:**
- Checkout Date: {{ $checkout->checkout_date->format('F j, Y') }}
- Due Date: {{ $checkout->due_date->format('F j, Y') }}

@component('mail::button', ['url' => route('books.show', $book->id)])
View Book Details
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent