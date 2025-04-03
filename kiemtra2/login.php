<?php
session_start(); // Bắt đầu session để theo dõi trạng thái đăng nhập

$serverName = "localhost";
$database = "Test1";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$serverName;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

// Xử lý đăng nhập
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maSV = $_POST['MaSV'];
    $password = $_POST['Password'];

    // Kiểm tra thông tin đăng nhập
    $stmt = $conn->prepare("SELECT * FROM SinhVien WHERE MaSV = ?");
    $stmt->execute([$maSV]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && password_verify($password, $student['Password'])) {
        // Đăng nhập thành công
        $_SESSION['MaSV'] = $student['MaSV'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Mã sinh viên hoặc mật khẩu không đúng.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Đăng nhập</h1>
        <div class="card p-4 mx-auto" style="max-width: 400px;">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Mã sinh viên:</label>
                    <input type="text" name="MaSV" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu:</label>
                    <input type="password" name="Password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>