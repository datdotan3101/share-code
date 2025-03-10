include '../includes/config.php';

if (isset($_GET['id'])) {
$post_id = $_GET['id'];
$query = "UPDATE posts SET status = 1 WHERE id = :id";
$statement = $conn->prepare($query);
$statement->execute([':id' => $post_id]);
}
header("Location: posts.php");
exit();