<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POSMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow-lg border-0 rounded-lg" style="width: 100%; max-width: 400px;">
        <div class="card-header justify-content-center bg-white border-0 mt-3">
            <h3 class="font-weight-light text-center my-2 text-primary">Login POSMA</h3>
            <p class="text-center text-muted small mb-0">Sistem Point of Sales Mahasiswa</p>
        </div>
        <div class="card-body px-5 pb-5">
            
            <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Login Gagal! Username atau Password salah.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="proses/login_proses.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Username" required autofocus>
                    <label for="inputUsername">Username</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Password" required>
                    <label for="inputPassword">Password</label>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Masuk</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center py-3 bg-light border-0">
            <div class="small"><a href="#" class="text-muted">Lupa password? Hubungi Admin</a></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>