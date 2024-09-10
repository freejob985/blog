<?php
require_once 'db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if (!$post) {
        die("المنشور غير موجود");
    }
} else {
    die("لم يتم تحديد معرف المنشور");
}

function getSectionColor($section) {
    $colors = [
        'PHP' => '#8892BF',
        'JavaScript' => '#F0DB4F',
        'Python' => '#4B8BBE',
        'Java' => '#5382A1',
        'C#' => '#68217A',
        'Ruby' => '#CC342D',
        'default' => '#6c757d'
    ];
    return isset($colors[$section]) ? $colors[$section] : $colors['default'];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['Title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .title-image {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 2em;
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            background-color: <?= getSectionColor($post['Section']) ?>;
        }
        .tag-btn {
            margin-right: 5px;
            margin-bottom: 5px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="title-image">
            <?= htmlspecialchars($post['Title']) ?>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">التفاصيل</h5>
                <div class="card-text"><?= $post['Details'] ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">الشرح</h5>
                <p class="card-text"><?= htmlspecialchars($post['Explanation']) ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">الوسوم</h5>
                <div>
                    <?php 
                    $tags = explode(',', $post['tag']);
                    foreach ($tags as $tag): 
                        $tag = trim($tag);
                        if (!empty($tag)):
                    ?>
                        <span class="badge bg-primary tag-btn"><?= htmlspecialchars($tag) ?></span>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>

        <?php if (!empty($post['Links'])): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">الروابط</h5>
                <p class="card-text"><?= htmlspecialchars($post['Links']) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($post['Images'])): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">الصور</h5>
                <p class="card-text"><?= htmlspecialchars($post['Images']) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($post['Section'])): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">القسم</h5>
                <p class="card-text"><?= htmlspecialchars($post['Section']) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> تعديل</a>
            <button class="btn btn-danger delete-post" data-id="<?= $post['id'] ?>"><i class="fas fa-trash"></i> حذف</button>
            <a href="index.php" class="btn btn-secondary">العودة إلى الصفحة الرئيسية</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.delete-post').click(function() {
                var postId = $(this).data('id');
                if (confirm('هل أنت متأكد من حذف هذا المنشور؟')) {
                    $.ajax({
                        url: 'delete_post.php',
                        method: 'POST',
                        data: { id: postId },
                        success: function(response) {
                            toastr.success('تم حذف المنشور بنجاح');
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 2000);
                        },
                        error: function() {
                            toastr.error('حدث خطأ أثناء حذف المنشور');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>