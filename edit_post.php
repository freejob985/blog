
<?php
require_once 'db_connection.php';

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize_input($_POST['title']);
    $details = $_POST['details']; // Not sanitizing to allow HTML
    $explanation = sanitize_input($_POST['explanation']);
    $tag = sanitize_input($_POST['tag']);
    $links = sanitize_input($_POST['links']);
    $images = sanitize_input($_POST['images']);
    $section = sanitize_input($_POST['section']);

    $sql = "UPDATE posts SET Title = ?, Details = ?, Explanation = ?, tag = ?, Links = ?, Images = ?, Section = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$title, $details, $explanation, $tag, $links, $images, $section, $id])) {
        $message = "success|تم تحديث المنشور بنجاح!";
    } else {
        $message = "error|حدث خطأ أثناء تحديث المنشور.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المنشور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-5">تعديل المنشور</h1>
        
        <form id="postForm" method="POST" class="mb-5">
            <div class="mb-3">
                <label for="title" class="form-label">العنوان</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($post['Title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="details" class="form-label">التفاصيل</label>
                <textarea class="form-control summernote" id="details" name="details" required><?= $post['Details'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="explanation" class="form-label">الشرح</label>
                <textarea class="form-control" id="explanation" name="explanation"><?= htmlspecialchars($post['Explanation']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="tag" class="form-label">الوسوم</label>
                <input type="text" class="form-control" id="tag" name="tag" value="<?= htmlspecialchars($post['tag']) ?>">
            </div>
            <div class="mb-3">
                <label for="links" class="form-label">الروابط</label>
                <input type="text" class="form-control" id="links" name="links" value="<?= htmlspecialchars($post['Links']) ?>">
            </div>
            <div class="mb-3">
                <label for="images" class="form-label">الصور</label>
                <input type="text" class="form-control" id="images" name="images" value="<?= htmlspecialchars($post['Images']) ?>">
            </div>
            <div class="mb-3">
                <label for="section" class="form-label">القسم</label>
                <input type="text" class="form-control" id="section" name="section" value="<?= htmlspecialchars($post['Section']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">تحديث</button>
            <a href="post_details.php?id=<?= $post['id'] ?>" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            $('#tag').selectize({
                delimiter: ',',
                persist: false,
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });

            <?php
            if (isset($message)) {
                $parts = explode('|', $message);
                echo "toastr.{$parts[0]}('{$parts[1]}');";
            }
            ?>
        });
    </script>
</body>
</html>