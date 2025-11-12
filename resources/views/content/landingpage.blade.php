<!DOCTYPE html>
<html>
<head>
    <title>Perwalian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}

    <style>
       body {
            background: url('{{ asset('assets/img/backgrounds/TI.jpg') }}') no-repeat center center/cover;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            text-align: center;
            position: relative;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.7);
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .overlay h4 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .overlay p {
            font-size: 16px;
            max-width: 700px;
            margin: 0 auto 10px;
        }

        .overlay .lead {
            font-weight: 500;
            margin-top: 15px;
            font-size: 18px;
        }

        .overlay .btn {
            background-color: #0d6efd;
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .overlay .btn:hover {
            background-color: #084298;
            transform: scale(1.05);
    </style>
</head>
<body>
    
    <div class="overlay">
        
        <div>
            <h1>SELAMAT DATANG MAHASISWA JURUSAN TEKNOLOGI INFORMASI</h1>
            <p>Pantau perkembangan akademik mahasiswa dengan mudah, cepat, dan terintegrasi. 
                SIWALI dirancang untuk memudahkan wali, dosen, dan program studi dalam berkomunikasi.</p>
            <p class="lead">Harap Login Terlebih Dahulu Untuk Perwalian</p>
            <a href="{{ route('auth.login') }}" class="btn btn-primary mt-3">Login</a>
        </div>
    </div>
</body>
</html>
