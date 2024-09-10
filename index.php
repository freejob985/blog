<?php
require_once 'db_connection.php';

// Fetch all posts
$stmt = $pdo->query("SELECT id, Title, Explanation, tag, Section FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();

// Handle search
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search_query)) {
    $search_stmt = $pdo->prepare("SELECT id, Title, Explanation, tag, Section FROM posts WHERE Title LIKE ? OR Explanation LIKE ? OR tag LIKE ? ORDER BY created_at DESC");
    $search_stmt->execute(["%$search_query%", "%$search_query%", "%$search_query%"]);
    $posts = $search_stmt->fetchAll();
}

// Fetch titles for autocomplete
$titles_stmt = $pdo->query("SELECT Title FROM posts");
$titles = $titles_stmt->fetchAll(PDO::FETCH_COLUMN);

// Function to generate color based on section
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
    <title>مدونة البرمجة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-img-top {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5em;
            text-align: center;
            padding: 10px;
        }
        .tag-btn {
            margin-right: 5px;
            margin-bottom: 5px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">مدونة البرمجة</h1>
        
        <div class="mb-4">
            <form class="d-flex" action="" method="GET">
                <input class="form-control me-2" type="search" placeholder="ابحث عن منشور" aria-label="Search" name="search" id="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-outline-success" type="submit">بحث</button>
            </form>
        </div>

        <a href="add_post.php" class="btn btn-primary mb-4">إضافة منشور جديد</a>

        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-img-top" style="background-color: <?= getSectionColor($post['Section']) ?>;">
                            <?= htmlspecialchars($post['Title']) ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($post['Title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($post['Explanation'], 0, 100)) ?>...</p>
                            <div class="mb-2">
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
                            <a href="post_details.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-info">التفاصيل</a>
                            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger delete-post" data-id="<?= $post['id'] ?>"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Autocomplete
            var availableTitles = <?php echo json_encode($titles); ?>;
            $("#search").autocomplete({
                source: availableTitles
            });

            // Delete post
            $('.delete-post').click(function() {
                var postId = $(this).data('id');
                if (confirm('هل أنت متأكد من حذف هذا المنشور؟')) {
                    $.ajax({
                        url: 'delete_post.php',
                        method: 'POST',
                        data: { id: postId },
                        success: function(response) {
                            toastr.success('تم حذف المنشور بنجاح');
                            location.reload();
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