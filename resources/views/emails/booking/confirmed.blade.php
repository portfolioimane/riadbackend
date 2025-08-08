@component('mail::message')
# Booking Confirmed

Dear {{ $booking->name }},

Thank you for your booking. Here are your details:

- **Booking ID:** {{ $booking->id }}
- **Room:** {{ $booking->room->room_name ?? 'N/A' }}
- **Check-in:** {{ $booking->check_in }}
- **Check-out:** {{ $booking->check_out }}
- **Total Paid:** {{ $booking->paid_amount }} MAD

If you have any questions, feel free to contact us.

@component('mail::button', ['url' => url('/')])
Visit Website
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
