@component('mail::message')
# Book Checkout Confirmation

Dear {{ $user->name }},

This email confirms that you have successfully checked out the following book:

**Book Title:** {{ $book->title }}  
**Checkout Date:** {{ $checkout->checkout_date->format('F j, Y') }}  
**Due Date:** {{ $checkout->due_date->format('F j, Y') }}

Please ensure to return the book by the due date to avoid any late fees.

@component('mail::button', ['url' => route('books.show', $book->id)])
View Book Details
@endcomponent

Thank you for using our library services!

Best regards,<br>
{{ config('app.name') }}
@endcomponent