<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Backend API - Almuhsin Universe</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Mendaftarkan Color Palette dari Logo Almuhsin Universe */
        :root {
            --color-primary: #4562E5;
            /* Biru terang sesuai logo */
            --color-bg: #F4F7FF;
            /* Biru sangat muda/putih untuk background */
            --color-accent: #E1E8FF;
            /* Biru muda untuk aksen badge */
            --text-dark: #1E293B;
            /* Abu-abu gelap untuk teks utama */
            --text-muted: #64748B;
            /* Abu-abu kalem untuk teks deskripsi */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--color-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* Pola grid modern yang samar di background */
            background-image: linear-gradient(var(--color-accent) 1px, transparent 1px),
                linear-gradient(90deg, var(--color-accent) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .card {
            background: #ffffff;
            padding: 3.5rem 2.5rem;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(69, 98, 229, 0.08);
            text-align: center;
            max-width: 480px;
            width: 90%;
            border-top: 6px solid var(--color-primary);
            position: relative;
        }

        .logo-wrapper img {
            width: 100px;
            height: auto;
            margin-bottom: 1.5rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            background-color: var(--color-accent);
            color: var(--color-primary);
            padding: 0.5rem 1.2rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
        }

        /* Animasi titik berkedip */
        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--color-primary);
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        h1 {
            color: var(--text-dark);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            text-decoration: none;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            cursor: default;
        }

        .btn-primary {
            background-color: var(--color-primary);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(69, 98, 229, 0.25);
        }

        .btn-primary:hover {
            background-color: #344ec7;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(69, 98, 229, 0.35);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--color-primary);
            border: 2px solid var(--color-accent);
        }

        .btn-secondary:hover {
            background-color: var(--color-accent);
            border-color: var(--color-primary);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(69, 98, 229, 0.5);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(69, 98, 229, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(69, 98, 229, 0);
            }
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="logo-wrapper">
            <img src="{{ asset('logo.png') }}" alt="Almuhsin Universe Logo">
        </div>

        <div class="status-badge">
            <span class="status-dot"></span>
            Service Online & Ready
        </div>

        <h1>Almuhsin Universe</h1>
        <p>Ini adalah halaman resmi Core Backend API untuk aplikasi berbasis web. Server ini berfungsi sebagai pusat kendali data, keamanan enkripsi, dan penyedia layanan Web Service yang berjalan secara optimal.</p>

        <!-- <div class="btn-group">
            <div class="btn btn-primary">Core API Gateway Active</div>
            <div class="btn btn-secondary">Laravel 11.x Engine</div>
        </div> -->
    </div>

</body>

</html>