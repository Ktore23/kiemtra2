<?php
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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM SinhVien WHERE MaSV = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4">Chi tiết sinh viên</h1>
        <div class="card p-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Mã SV:</label>
                <span><?= htmlspecialchars($student['MaSV']) ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Họ Tên:</label>
                <span><?= htmlspecialchars($student['HoTen']) ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Giới Tính:</label>
                <span><?= htmlspecialchars($student['GioiTinh']) ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Ngày Sinh:</label>
                <span><?= htmlspecialchars($student['NgaySinh']) ?></span>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Hình ảnh:</label><br>
                <?php if (!empty($student['Hinh'])): ?>
                    <img src="<?= htmlspecialchars($student['Hinh']) ?>" alt="Ảnh sinh viên" class="img-thumbnail" width="150">
                <?php else: ?>
                    <span>Không có ảnh</span>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Mã Ngành:</label>
                <span><?= htmlspecialchars($student['MaNganh']) ?></span>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>