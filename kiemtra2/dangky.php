<?php
// Kết nối CSDL
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

// Lấy danh sách sinh viên
$stmt = $conn->query("SELECT MaSV, HoTen FROM SinhVien");
$sinhVienList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách học phần
$stmt = $conn->query("SELECT MaHP, TenHP, SoTinChi FROM HocPhan");
$hocPhanList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Biến để lưu thông báo
$message = "";

// Xử lý đăng ký học phần
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maSV = $_POST['MaSV'];
    $hocPhanSelected = isset($_POST['HocPhan']) ? $_POST['HocPhan'] : [];

    if (empty($maSV)) {
        $message = "Vui lòng chọn mã sinh viên.";
    } elseif (empty($hocPhanSelected)) {
        $message = "Vui lòng chọn ít nhất một học phần.";
    } else {
        try {
            // Thêm vào bảng DangKy
            $stmt = $conn->prepare("INSERT INTO DangKy (NgayDK, MaSV) VALUES (CURDATE(), ?)");
            $stmt->execute([$maSV]);
            $maDK = $conn->lastInsertId(); // Lấy MaDK vừa tạo

            // Thêm vào bảng ChiTietDangKy
            $stmt = $conn->prepare("INSERT INTO ChiTietDangKy (MaDK, MaHP) VALUES (?, ?)");
            foreach ($hocPhanSelected as $maHP) {
                $stmt->execute([$maDK, $maHP]);
            }

            $message = "Đăng ký học phần thành công!";
        } catch (PDOException $e) {
            $message = "Lỗi: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký học phần</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4">Đăng ký học phần</h1>
        <div class="card p-4">
            <?php if (!empty($message)): ?>
                <div class="alert <?= strpos($message, 'thành công') !== false ? 'alert-success' : 'alert-danger' ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Chọn sinh viên:</label>
                    <select name="MaSV" class="form-select" required>
                        <option value="">-- Chọn sinh viên --</option>
                        <?php foreach ($sinhVienList as $sv): ?>
                            <option value="<?= htmlspecialchars($sv['MaSV']) ?>">
                                <?= htmlspecialchars($sv['MaSV']) . " - " . htmlspecialchars($sv['HoTen']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Chọn học phần:</label>
                    <?php foreach ($hocPhanList as $hp): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="HocPhan[]" value="<?= htmlspecialchars($hp['MaHP']) ?>" id="hp_<?= htmlspecialchars($hp['MaHP']) ?>">
                            <label class="form-check-label" for="hp_<?= htmlspecialchars($hp['MaHP']) ?>">
                                <?= htmlspecialchars($hp['MaHP']) . " - " . htmlspecialchars($hp['TenHP']) . " (" . $hp['SoTinChi'] . " tín chỉ)" ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary">Đăng ký</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>