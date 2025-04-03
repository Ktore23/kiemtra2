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

// Số sinh viên mỗi trang
$studentsPerPage = 4;

// Lấy trang hiện tại từ URL (nếu không có thì mặc định là trang 1)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính offset (vị trí bắt đầu lấy dữ liệu)
$offset = ($page - 1) * $studentsPerPage;

// Lấy tổng số sinh viên
$totalStmt = $conn->query("SELECT COUNT(*) FROM SinhVien");
$totalStudents = $totalStmt->fetchColumn();
$totalPages = ceil($totalStudents / $studentsPerPage);

// Lấy danh sách sinh viên cho trang hiện tại
$stmt = $conn->prepare("SELECT * FROM SinhVien LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $studentsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1 class="text-center mb-4">Danh sách sinh viên</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã SV</th>
                    <th>Họ Tên</th>
                    <th>Giới Tính</th>
                    <th>Ngày Sinh</th>
                    <th>Hình</th>
                    <th>Mã Ngành</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['MaSV']) ?></td>
                            <td><?= htmlspecialchars($student['HoTen']) ?></td>
                            <td><?= htmlspecialchars($student['GioiTinh']) ?></td>
                            <td><?= htmlspecialchars($student['NgaySinh']) ?></td>
                            <td>
                                <?php if (!empty($student['Hinh'])): ?>
                                    <img src="<?= htmlspecialchars($student['Hinh']) ?>" alt="Ảnh sinh viên" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
                                <?php else: ?>
                                    <span>Không có ảnh</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($student['MaNganh']) ?></td>
                            <td>
                                <a href="detail.php?id=<?= htmlspecialchars($student['MaSV']) ?>" class="btn btn-info btn-sm">Chi tiết</a>
                                <a href="edit.php?id=<?= htmlspecialchars($student['MaSV']) ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="delete.php?id=<?= htmlspecialchars($student['MaSV']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Không có sinh viên nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <!-- Nút Previous -->
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>

                    <!-- Các trang -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Nút Next -->
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="index.php?page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>