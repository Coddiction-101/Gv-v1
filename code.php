<?php
include('../../connection/db_connect.php');


// =========================
// CHECK REQUEST METHOD
// =========================

if($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../all_pages.php");
    exit;
}


// =========================
// GET FORM DATA
// =========================

$id                 = (int) $_POST['id'];
$title              = trim($_POST['title']);
$slug               = trim($_POST['slug']);
$content            = $_POST['content'];
$status             = $_POST['status'];
$meta_title         = trim($_POST['meta_title']);
$meta_description   = trim($_POST['meta_description']);
$meta_keywords      = trim($_POST['meta_keywords']);


// =========================
// VALIDATION
// =========================

if(empty($id) || empty($title) || empty($slug))
{
    $_SESSION['error'] = "Required fields are missing.";
    header("Location: ../edit_page.php?id=".$id);
    exit;
}


// =========================
// CHECK SLUG DUPLICATE (IMPORTANT)
// =========================

$check = $conn->prepare("SELECT id FROM pages WHERE slug = ? AND id != ?");
$check->bind_param("si", $slug, $id);
$check->execute();

$result = $check->get_result();

if($result->num_rows > 0)
{
    $_SESSION['error'] = "Slug already exists. Please use another.";
    header("Location: ../edit_page.php?id=".$id);
    exit;
}


// =========================
// UPDATE QUERY
// =========================

$stmt = $conn->prepare("
    UPDATE pages 
    SET 
        title = ?,
        slug = ?,
        content = ?,
        status = ?,
        meta_title = ?,
        meta_description = ?,
        meta_keywords = ?
    WHERE id = ?
");

if(!$stmt)
{
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param(
    "sssssssi",
    $title,
    $slug,
    $content,
    $status,
    $meta_title,
    $meta_description,
    $meta_keywords,
    $id
);


// =========================
// EXECUTE
// =========================

if($stmt->execute())
{
    $_SESSION['success'] = "Page updated successfully.";
}
else
{
    $_SESSION['error'] = "Failed to update page.";
}


// =========================
// REDIRECT
// =========================

header("Location: ../edit_page.php?id=".$id);
exit;

?>
