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

$stmt = $conn->query("SELECT MaNganh, TenNganh FROM NganhHoc");
$nganhList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maSV = $_POST['MaSV'];
    $hoTen = $_POST['HoTen'];
    $gioiTinh = $_POST['GioiTinh'];
    $ngaySinh = $_POST['NgaySinh'];
    $maNganh = $_POST['MaNganh'];
    $hinh = '';

    if (isset($_FILES['Hinh']) && $_FILES['Hinh']['error'] == 0) {
        $uploadDir = "Content/images/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileTmpPath = $_FILES['Hinh']['tmp_name'];
        $fileName = basename($_FILES['Hinh']['name']);
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid("img_", true) . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $hinh = $destPath;
        } else {
            $error = "Lỗi tải lên ảnh.";
        }
    }

    if (empty($error)) {
        try {
            $stmt = $conn->prepare("INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, NgaySinh, Hinh, MaNganh) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$maSV, $hoTen, $gioiTinh, $ngaySinh, $hinh, $maNganh]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $error = "Lỗi: Mã sinh viên '$maSV' đã tồn tại. Vui lòng nhập mã khác.";
            } else {
                $error = "Lỗi: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4">Thêm sinh viên</h1>
        <div class="card p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Mã SV:</label>
                    <input type="text" name="MaSV" class="form-control" value="<?php echo isset($maSV) ? htmlspecialchars($maSV) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Họ Tên:</label>
                    <input type="text" name="HoTen" class="form-control" value="<?php echo isset($hoTen) ? htmlspecialchars($hoTen) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Giới Tính:</label>
                    <select name="GioiTinh" class="form-select">
                        <option value="Nam" <?php echo (isset($gioiTinh) && $gioiTinh == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo (isset($gioiTinh) && $gioiTinh == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngày Sinh:</label>
                    <input type="date" name="NgaySinh" class="form-control" value="<?php echo isset($ngaySinh) ? htmlspecialchars($ngaySinh) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Hình ảnh:</label>
                    <input type="file" name="Hinh" class="form-control" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mã Ngành:</label>
                    <select name="MaNganh" class="form-select" required>
                        <option value="">-- Chọn ngành --</option>
                        <?php foreach ($nganhList as $nganh): ?>
                            <option value="<?php echo htmlspecialchars($nganh['MaNganh']); ?>" <?php echo (isset($maNganh) && $maNganh == $nganh['MaNganh']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($nganh['TenNganh']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Thêm</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>