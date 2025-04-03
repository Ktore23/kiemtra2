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

// Xóa sinh viên
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM SinhVien WHERE MaSV = ?");
    $stmt->execute([$id]);
}

// Chuyển hướng về trang chính
header("Location: index.php");
exit;
?>
