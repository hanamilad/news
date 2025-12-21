<!doctype html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $purpose }}</title>
    <style>
      @font-face {font-family: system-ui;} /* fallback */
      :root {color-scheme: light dark;}
      body {margin:0; padding:0; background:#f4f6f8; font-family: system-ui, -apple-system, Segoe UI, Tahoma, Arial;}
      .wrapper {width:100%;}
      .container {max-width:600px; margin:0 auto; padding:24px;}
      .card {background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.06);} 
      .header {background:#0f172a; color:#ffffff; padding:20px 24px; text-align:center;}
      .brand {font-size:20px; font-weight:700; letter-spacing:0.2px;}
      .content {padding:28px 24px; color:#0f172a;}
      .title {font-size:18px; font-weight:700; margin:0 0 8px;}
      .subtitle {font-size:14px; margin:0 0 20px; color:#334155;}
      .otp {display:flex; gap:10px; justify-content:center; margin:18px 0 6px;}
      .otp-box {width:52px; height:56px; border-radius:12px; border:1px solid #e2e8f0; background:#f8fafc; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800; color:#0f172a;}
      .hint {text-align:center; font-size:12px; color:#64748b; margin-top:10px;}
      .footer {padding:18px 24px; text-align:center; font-size:12px; color:#64748b;}
      @media (prefers-color-scheme: dark) {
        body {background:#0b0f19;}
        .card {background:#0f172a; box-shadow:none;}
        .content {color:#e5e7eb;}
        .subtitle {color:#cbd5e1;}
        .otp-box {border-color:#334155; background:#0b1220; color:#e5e7eb;}
        .hint, .footer {color:#94a3b8;}
      }
    </style>
  </head>
  <body>
    <div class="wrapper">
      <div class="container">
        <div class="card">
          <div class="header">
            <div class="brand">{{ config('mail.from.name', config('app.name')) }}</div>
          </div>
          <div class="content">
            <h1 class="title">{{ $purpose }}</h1>
            <p class="subtitle">يرجى استخدام الرمز التالي لإتمام العملية. هذا الرمز صالح لمدة 15 دقيقة.</p>
            @php($chars = preg_split('/(?<!^)(?!$)/u', $code))
            <div class="otp">
              @foreach($chars as $ch)
                <div class="otp-box">{{ $ch }}</div>
              @endforeach
            </div>
            <p class="hint">إذا لم تطلب هذا الإجراء، يمكنك تجاهل هذه الرسالة.</p>
          </div>
          <div class="footer">{{ date('Y') }} © {{ config('mail.from.name', config('app.name')) }}</div>
        </div>
      </div>
    </div>
  </body>
</html>
