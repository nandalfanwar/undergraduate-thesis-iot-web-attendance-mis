<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIM Kehadiran SDIT Daarul Fataa</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body{
            margin:0;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:#f2f4f7;
            padding:24px;
        }

        .login-wrapper{
            width:min(1000px, 95vw);
            height:min(650px, 90vh);
            background:white;
            display:flex;
            border-radius:16px;
            overflow:hidden;
            box-shadow:0 15px 35px rgba(0,0,0,.12);
        }

        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ==========================
           LEFT
        ========================== */

        .login-left {
            width: 50%;
            position: relative;
            background: url("{{ asset('images/sdit.jpg') }}") center center/cover;
        }

        .login-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(34, 73, 32, .55);
        }

        .overlay-content {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px;
            color: white;
        }

        .overlay-content h2 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .overlay-content p {
            line-height: 1.8;
            font-size: 15px;
        }

        /* ==========================
           RIGHT
        ========================== */

        .login-right {
            width: 50%;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 50px;
        }

        .logo {
            width: 90px;
            margin: 0 auto 20px;
        }

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        .title h1 {
            color: #2E7D32;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .title p {
            color: #777;
            font-size: 14px;
        }

        .alert {
            background: #fdecea;
            color: #b42318;
            border: 1px solid #f5c2c7;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #d7d7d7;
            border-radius: 8px;
            font-size: 14px;
            transition: .25s;
        }

        input:focus {
            outline: none;
            border-color: #2E7D32;
            box-shadow: 0 0 0 4px rgba(46, 125, 50, .15);
        }

        .password-group {
            position: relative;
        }

        .password-group input {
            padding-right: 45px;
        }

        .toggle-password{
            position:absolute;
            right:15px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            color:#777;
            font-size:16px;
            transition:.2s;
        }

        .toggle-password:hover{
            color:#2E7D32;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #2E7D32;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: .25s;
        }

        button:hover {
            background: #1B5E20;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, .15);
        }

        button:disabled {
            cursor: not-allowed;
            opacity: .8;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            color: #999;
            font-size: 13px;
        }

        @media(max-width:768px) {

            .login-wrapper {
                flex-direction: column;
            }

            .login-left,
            .login-right {
                width: 100%;
            }

            .login-left {
                height: 220px;
            }

            .login-right {
                padding: 35px;
            }

        }
    </style>

</head>

<body>

    <div class="login-wrapper">

        <!-- LEFT -->
        <div class="login-left">

            <div class="overlay-content">
                <h2>SDIT Daarul Fataa</h2>

                <p>
                    <p style="text-align: justify;>">
                       Sistem Informasi Manajemen (SIM) Kehadiran berbasis IoT dan Web
                        yang dikembangkan sebagai solusi digital untuk mendukung
                        pencatatan, pemantauan, dan pelaporan kehadiran guru serta
                        tenaga kependidikan.</p>
                </p>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="login-right">

            {{-- Ganti dengan logo sekolah --}}
            <img src="{{ asset('images/logo.png') }}" class="logo" alt="Logo">

            <div class="title">
                <h1>SIM Kehadiran</h1>
                <p>Silakan masuk menggunakan akun Anda</p>
            </div>

            {{-- Alert Login Gagal --}}
            @if (session('error'))
                <div class="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert">
                    Email atau password yang Anda masukkan salah.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">

                @csrf

                <div class="form-group">
                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email"
                        required>
                </div>

                <div class="form-group">

                    <label>Password</label>

                    <div class="password-group">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required>

                    <i class="fa-regular fa-eye toggle-password" id="togglePassword"></i>

                </div>

                </div>

                <button
                    type="submit"
                    id="loginBtn">

                    Login

                </button>

            </form>

            <p class="footer">
                © {{ date('Y') }} SDIT Daarul Fataa
            </p>

        </div>

    </div>

    <script>
        // Show / Hide Password
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function () {

            if (password.type === "password") {
                password.type = "text";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                password.type = "password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }

        });

        // Loading Button
        document.getElementById('loginForm').addEventListener('submit', function() {

            const btn = document.getElementById('loginBtn');

            btn.disabled = true;
            btn.innerHTML = 'Memproses...';

        });
    </script>

</body>

</html>