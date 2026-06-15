<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #4e73df;
            margin-top: 0;
            font-size: 24px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            text-decoration: none;
            padding: 14px 40px;
            border-radius: 50px;
            font-weight: 600;
            margin: 30px 0;
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
            transition: transform 0.3s;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .footer {
            background: #f8f9fc;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #858796;
            border-top: 1px solid #e3e6f0;
        }
        .info {
            background: #f8f9fc;
            border-left: 4px solid #4e73df;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning {
            color: #e74a3b;
            font-size: 14px;
            margin-top: 20px;
            padding: 10px;
            background: #fff3f3;
            border-radius: 5px;
        }
        .school-logo {
            max-width: 80px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SMK Negeri 1 Cipeundeuy</h1>
            <p>Sistem Informasi Akademik</p>
        </div>

        <div class="content">
            <h2>Halo, {{ $user->name }}!</h2>

            <p>Kami menerima permintaan untuk mereset kata sandi akun Anda di Sistem Informasi Akademik SMK Negeri 1 Cipeundeuy.</p>

            <div class="info">
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Waktu Permintaan:</strong> {{ now()->format('d F Y H:i') }} WIB</p>
            </div>

            <p>Klik tombol di bawah ini untuk mereset kata sandi Anda:</p>

            <div style="text-align: center;">
                <a href="{{ $resetLink }}" class="button">Reset Kata Sandi</a>
            </div>

            <p>Link ini akan <strong>kadaluarsa dalam 60 menit</strong>.</p>

            <p>Jika Anda tidak meminta reset kata sandi, abaikan email ini dan pastikan akun Anda tetap aman.</p>

            <div class="warning">
                <strong>⚠️ Perhatian:</strong> Jangan berikan link ini kepada siapa pun, termasuk pihak yang mengaku dari sekolah.
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} SMK Negeri 1 Cipeundeuy. All rights reserved.</p>
            <p>Jl. Raya Cipeundeuy No. 123, Cipeundeuy, Subang</p>
            <p style="margin-top: 10px;">
                <small>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</small>
            </p>
        </div>
    </div>
</body>
</html>
