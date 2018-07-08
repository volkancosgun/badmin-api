@component('mail::message')
# Şifreniz Değiştirildi

Merhaba, <strong>{{ $name }}</strong>

Az önce hesabınızın şifresi değiştirildi.

Eğer bu işlemi siz yapmadıysanız, sistem yöneticinizle irtibat kurunuz.

İyi çalışmalar,<br>
{{ config('app.name') }}
@endcomponent
