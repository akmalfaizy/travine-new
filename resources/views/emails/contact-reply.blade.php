<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Terima Kasih Telah Menghubungi Kami</title>
    <style>
        body {
            font-family: 'Poppins', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a202c;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #0F2A52 0%, #14B8A6 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.7;
        }
        .content p {
            margin-bottom: 20px;
            font-size: 15px;
        }
        .quote {
            background-color: #f7fafc;
            border-left: 4px solid #14B8A6;
            padding: 15px 20px;
            margin: 25px 0;
            font-style: italic;
            color: #4a5568;
            border-radius: 0 8px 8px 0;
        }
        .button-wrapper {
            text-align: center;
            margin-top: 30px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #14B8A6 0%, #0d9488 100%);
            color: #ffffff !important;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(20, 184, 166, 0.2);
            transition: all 0.3s;
        }
        .footer {
            background-color: #f7fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>travine.id</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $contact->name }}</strong>,</p>
            <p>Terima kasih telah menghubungi kami melalui formulir kontak di website travine.id. Pesan Anda telah kami terima dengan detail sebagai berikut:</p>
            
            <div class="quote">
                <strong>Subjek:</strong> {{ $contact->subject ?? '-' }}<br>
                <strong>Pesan:</strong><br>
                {{ $contact->message }}
            </div>
            
            <p>Tim admin kami sedang meninjau pertanyaan/permintaan Anda dan akan segera menghubungi Anda kembali dalam waktu maksimal 1x24 jam melalui alamat email ini atau kontak WhatsApp Anda jika terdaftar.</p>
            
            <p>Apabila Anda memerlukan respons mendesak, Anda dapat langsung menghubungi layanan pelanggan kami via WhatsApp dengan menekan tombol di bawah ini:</p>
            
            <div class="button-wrapper">
                <a href="https://wa.me/6282137925585?text=Halo%20Admin%20travine.id,%20saya%20sudah%20mengirimkan%20pesan%20melalui%20form%20kontak%20dengan%20email%20{{ urlencode($contact->email) }}" class="button" target="_blank">Chat Customer Service</a>
            </div>
        </div>
        <div class="footer">
            &copy; 2026 travine.id. Jelajahi Keindahan Nusantara.<br>
            Perum Griya Pratama Asri No. 3B, Jl. Munggur, Sidoarum, Godean, DIY | travineid@gmail.com
        </div>
    </div>
</body>
</html>
