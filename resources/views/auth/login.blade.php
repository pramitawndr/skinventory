<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SKINVENTORY</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f4f6f3;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        }

        .brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand h1 {
            color: #365b46;
            font-size: 28px;
            letter-spacing: 2px;
        }

        .brand p {
            color: #777;
            font-size: 13px;
            margin-top: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #444;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #527b61;
        }

        .error {
            color: #d33;
            font-size: 13px;
            margin-top: 6px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #365b46;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #294735;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 28px;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="brand">
            <h1>SKINVENTORY</h1>
            <p>Skincare Inventory Management System</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email"
                    required
                >

                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="footer">
            SKINVENTORY &copy; 2026
        </div>
    </div>
</body>
</html>