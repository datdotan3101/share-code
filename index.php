<?php
session_start();
include 'includes/config.php';

// Check login
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;
$username = $isLoggedIn ? $_SESSION['username'] : null;

$query = "SELECT posts.*, subjects.name AS subject_name, users.username 
          FROM posts 
          LEFT JOIN subjects ON posts.subject_id = subjects.id 
          LEFT JOIN users ON posts.user_id = users.id
          ORDER BY posts.created_at DESC";
$statement = $conn->prepare($query);
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);


foreach ($posts as &$post) {
    $likeQuery = "SELECT COUNT(*) as like_count FROM likes WHERE post_id = ?";
    $likeStmt = $conn->prepare($likeQuery);
    $likeStmt->execute([$post['id']]);
    $post['like_count'] = $likeStmt->fetch(PDO::FETCH_ASSOC)['like_count'];

    //Check user like?
    $userLiked = false;
    if ($isLoggedIn) {
        $checkLikeQuery = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
        $checkLikeStmt = $conn->prepare($checkLikeQuery);
        $checkLikeStmt->execute([$post['id'], $user_id]);
        $userLiked = $checkLikeStmt->rowCount() > 0;
    }
    $post['user_liked'] = $userLiked;

    // Fetch list comment
    $commentQuery = "SELECT comments.comment, users.username 
                     FROM comments 
                     JOIN users ON comments.user_id = users.id 
                     WHERE post_id = ? ORDER BY comments.created_at DESC";
    $commentStmt = $conn->prepare($commentQuery);
    $commentStmt->execute([$post['id']]);
    $post['comments'] = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Q&A</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script defer src="assets/script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'includes/header.html.php'; ?>

    <div class="container mt-4 d-flex flex-column align-items-center text-center">
        <?php if ($isLoggedIn): ?>
            <a href="posts/create.html.php" class="btn btn-primary mb-3">Write question here</a>
        <?php endif; ?>

        <?php if (!empty($posts)): ?>
            <ul class="list-group w-75">
                <?php foreach ($posts as &$post): ?>
                    <li class="list-group-item mb-3 p-4 rounded shadow-sm">
                        <h3 class="fw-bold"> <?= htmlspecialchars($post['title']) ?> </h3>
                        <p class="text-muted"><strong>Author:</strong> <?= htmlspecialchars($post['username'] ?? 'Anonymous') ?></p>

                        <p><strong>Modules:</strong>
                            <?= !empty($post['subject_name']) ? htmlspecialchars($post['subject_name']) : '<span class="text-danger">Chưa có môn học</span>' ?>
                        </p>

                        <p class="text-justify"> <?= nl2br(htmlspecialchars($post['content'])) ?> </p>

                        <?php if (!empty($post['image']) && file_exists($post['image'])): ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Images" class="img-fluid rounded mb-3">
                        <?php else: ?>
                            <p class="text-warning">No Image</p>
                        <?php endif; ?>


                        <?php if ($isLoggedIn): ?>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="posts/edit.html.php?id=<?= $post['id'] ?>" class="btn btn-warning">Edit</a>
                                <a href="posts/delete.php?id=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Xóa bài này?')">Delete</a>
                            </div>
                        <?php endif; ?>

                        <div class="mt-3">
                            <button class="btn btn-outline-primary like-button" data-post-id="<?= $post['id'] ?>">
                                <?= $post['user_liked'] ? 'Unlike' : 'Like  ' ?> (<span id="like-count-<?= $post['id'] ?>"><?= $post['like_count'] ?></span>)
                            </button>
                        </div>
                        <div class="comments-section mt-4">
                            <h4>💬 Comment</h4>
                            <ul class="list-group">
                                <?php foreach ($post['comments'] as $comment): ?>
                                    <li class="list-group-item"> <strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= htmlspecialchars($comment['comment']) ?> </li>
                                <?php endforeach; ?>
                            </ul>

                            <?php if ($isLoggedIn): ?>
                                <form action="posts/comment.php" method="POST" class="mt-3">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <textarea name="comment" class="form-control mb-2" placeholder="Write comment..." required></textarea>
                                    <button type="submit" class="btn btn-success">Post</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php unset($post); ?>
            </ul>
        <?php else: ?>
            <p class="alert alert-info">No post</p>
        <?php endif; ?>

        <p class="mt-4"><a href="contact/contact.php" class="btn btn-link">📩 Contact to admin</a></p>
    </div>

    <script defer src="assets/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>




</html>