<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$id])) {
        echo "تم حذف المنشور بنجاح";
    } else {
        echo "حدث خطأ أثناء حذف المنشور";
    }
} else {
    echo "طلب غير صالح";
}
?>