<!DOCTYPE html>
<html lang="pl">
<head><meta charset="utf-8"><title>Potwierdź zapis do bety</title></head>
<body style="margin:0;background:#f7f6f1;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#15171c;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f7f6f1;padding:32px 16px;">
<tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #d9d6cc;border-radius:12px;padding:32px;">
<tr><td style="font-size:22px;font-weight:700;padding-bottom:16px;">Potwierdź zapis do bety</td></tr>
<tr><td style="font-size:16px;line-height:1.6;padding-bottom:24px;">Dziękujemy za zapis. Kliknij przycisk poniżej, żeby potwierdzić swój adres e-mail i zakończyć zapis na listę bety.</td></tr>
<tr><td style="padding-bottom:24px;"><a href="{{ $confirmUrl }}" style="display:inline-block;background:#0b7a55;color:#ffffff;text-decoration:none;font-weight:600;padding:12px 20px;border-radius:8px;">Potwierdzam zapis</a></td></tr>
<tr><td style="font-size:14px;line-height:1.6;color:#3d4149;padding-bottom:24px;">Jeśli przycisk nie działa, skopiuj ten link do przeglądarki:<br><a href="{{ $confirmUrl }}" style="color:#0b7a55;">{{ $confirmUrl }}</a></td></tr>
<tr><td style="font-size:12px;line-height:1.6;color:#6b7078;border-top:1px solid #d9d6cc;padding-top:16px;">Jeśli to nie Ty zapisywałeś się do bety, zignoruj tę wiadomość. Możesz też <a href="{{ $unsubscribeUrl }}" style="color:#6b7078;">wypisać się z listy</a> – wtedy usuniemy Twój adres.</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
