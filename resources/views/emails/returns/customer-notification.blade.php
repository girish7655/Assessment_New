@component('mail::message')
# Book Return Confirmation

Dear {{ $user->name }},

This email confirms that you have successfully returned the following book:

**Book Title:** {{ $book->title }}  
**Return Date:** {{ $checkout->return_date->format('F j, Y') }}  

Thank you for using our library services!

@component('mail::button', ['url' => route('books.show', $book->id)])
View Book Details
@endcomponent

Best regards,<br>
{{ config('app.name') }}
@endcomponent