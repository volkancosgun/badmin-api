@component('mail::message')
# Şifre Sıfırlama İsteği

Merhaba, <strong>{{ $name }}</strong>

Az önce hesabınızdan şifre sıfırlama talebi aldık.

Eğer bu talebi siz yapmadıysanız, bu mesajı dikkate almayınız.

Şifrenizi sıfırlamak için lütfen aşağıdaki bağlantıya tıklayınız.

@component('mail::button', ['url' => 'http://localhost:4200/auth/resetPassword?token='.$token])
ŞİFREYİ SIFIRLA
@endcomponent

İyi  Çalışmalar,<br>
{{ config('app.name') }}
@endcomponent
