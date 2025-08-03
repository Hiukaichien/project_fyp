@component('mail::message')
# Tetapkan Semula Kata Laluan

Assalamualaikum dan salam sejahtera,

Anda menerima emel ini kerana kami telah menerima permintaan untuk menetapkan semula kata laluan bagi akaun anda di {{ config('app.name') }}.

@component('mail::button', ['url' => $url])
Tetapkan Semula Kata Laluan
@endcomponent

Pautan tetapan semula kata laluan ini akan tamat tempoh dalam {{ $count }} minit.

Jika anda tidak meminta tetapan semula kata laluan ini, sila abaikan emel ini. Tiada tindakan lanjut diperlukan dan kata laluan anda akan kekal tidak berubah.

Untuk keselamatan akaun anda, sila jangan kongsi pautan ini dengan sesiapa.

Terima kasih,<br>
{{ config('app.name') }}

@slot('subcopy')
Jika anda menghadapi masalah untuk klik butang "Tetapkan Semula Kata Laluan", sila salin dan tampal URL berikut ke dalam pelayar web anda:
<span class="break-all">[{{ $url }}]({{ $url }})</span>
@endslot
@endcomponent
