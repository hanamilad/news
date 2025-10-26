<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>رسالة تواصل جديدة</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            direction: rtl;
            text-align: right;
        }
        .container {
            background: #f7f7f7;
            padding: 20px;
            border-radius: 10px;
        }
        .info {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #2b6e44;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📩 رسالة تواصل جديدة من موقعك</h2>

        <div class="info"><span class="label">الاسم:</span> {{ $messageData->name }}</div>
        <div class="info"><span class="label">البريد الإلكتروني:</span> {{ $messageData->email }}</div>
        <div class="info"><span class="label">رقم الهاتف:</span> {{ $messageData->phone ?? 'غير متوفر' }}</div>
        <div class="info"><span class="label">الموضوع:</span> {{ $messageData->subject ?? 'غير متوفر' }}</div>

        <hr>

        <div class="info">
            <span class="label">الرسالة:</span><br>
            {{ $messageData->message }}
        </div>

        <hr>
        <small>تم إرسال هذه الرسالة تلقائيًا من نموذج التواصل في موقعك.</small>
    </div>
</body>
</html>
