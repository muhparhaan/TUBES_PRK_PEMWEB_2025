<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POSMA</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1B3C53 0%, #2F5C83 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            background: white;
        }
        
        .login-header {
            background: linear-gradient(135deg, #1B3C53, #2F5C83);
            padding: 40px 25px;
            text-align: center;
            color: white;
        }
        
        .login-header .logo {
            font-size: 3.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .login-header h1 {
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .login-header p {
            font-size: 0.9rem;
            opacity: 0.85;
            margin: 0;
            font-weight: 500;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-floating > .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px 15px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        
        .form-floating > .form-control:focus {
            border-color: #2F5C83;
            box-shadow: 0 0 0 0.2rem rgba(47, 92, 131, 0.15);
        }
        
        .form-floating > label {
            color: #666;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #1B3C53, #2F5C83);
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 700;
            font-size: 1rem;
            margin-top: 10px;
            transition: all 0.3s;
            letter-spacing: 0.5px;
            color: white;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #2F5C83, #1B3C53);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(31, 60, 83, 0.3);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            border-top: 1px solid #f0f0f0;
            background: #f9f9f9;
        }
        
        .login-footer a {
            color: #2F5C83;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        
        .login-footer a:hover {
            color: #1B3C53;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-cash-register"></i>
            </div>
            <h1>POSMA</h1>
            <p>Sistem Point of Sales Mahasiswa</p>
        </div>
        
        <div class="login-body">
            
            <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> 
                    Login Gagal! Username atau Password salah.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="proses/login_proses.php" method="POST">
                <div class="form-floating">
                    <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Username" required autofocus>
                    <label for="inputUsername"><i class="fas fa-user me-2"></i>Username</label>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Password" required>
                    <label for="inputPassword"><i class="fas fa-lock me-2"></i>Password</label>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> Masuk
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
