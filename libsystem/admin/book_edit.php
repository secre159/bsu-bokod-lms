<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $isbn = trim($_POST['isbn']);
    $call_no = trim($_POST['call_no']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $publisher = trim($_POST['publisher']);
    $pub_date = trim($_POST['pub_date']);
    $categories = isset($_POST['category']) ? $_POST['category'] : [];

    // ✅ Validation
    if (empty($categories)) {
        $_SESSION['error'] = 'Please select at least one category.';
        header('location: book.php');
        exit();
    }

    if (!preg_match("/^\d{4}(-\d{2}-\d{2})?$/", $pub_date)) {
        $_SESSION['error'] = "Publish Date must be YYYY or YYYY-MM-DD";
        header('location: book.php');
        exit();
    }

    // ✅ Fetch group criteria from selected book
    $book_query = $conn->prepare("SELECT title, author, publish_date FROM books WHERE id = ?");
    $book_query->bind_param("i", $id);
    $book_query->execute();
    $book = $book_query->get_result()->fetch_assoc();
    $book_query->close();

    if (!$book) {
        $_SESSION['error'] = 'Book not found.';
        header('location: book.php');
        exit();
    }

    $group_title = $book['title'];
    $group_author = $book['author'];
    $group_pubdate = $book['publish_date'];

    // ✅ Update all books that share same title, author, and publish date
    $stmt = $conn->prepare("
        UPDATE books 
        SET isbn = ?, call_no = ?, title = ?, author = ?, publisher = ?, publish_date = ?
        WHERE title = ? AND author = ? AND publish_date = ?
    ");
    $stmt->bind_param("sssssssss", $isbn, $call_no, $title, $author, $publisher, $pub_date, $group_title, $group_author, $group_pubdate);

    if ($stmt->execute()) {
        $stmt->close();

        // ✅ Get affected book IDs
        $affected_books = [];
        $get_books = $conn->prepare("SELECT id FROM books WHERE title = ? AND author = ? AND publish_date = ?");
        $get_books->bind_param("sss", $title, $author, $pub_date);
        $get_books->execute();
        $result = $get_books->get_result();
        while ($row = $result->fetch_assoc()) {
            $affected_books[] = $row['id'];
        }
        $get_books->close();

        // ✅ Update category mappings for each affected copy
        foreach ($affected_books as $book_id) {
            $conn->query("DELETE FROM book_category_map WHERE book_id = $book_id");

            $cat_stmt = $conn->prepare("INSERT INTO book_category_map (book_id, category_id) VALUES (?, ?)");
            foreach ($categories as $cat_id) {
                $cat_stmt->bind_param("ii", $book_id, $cat_id);
                $cat_stmt->execute();
            }
            $cat_stmt->close();
        }

        $_SESSION['success'] = 'All copies of this book were updated successfully.';
    } else {
        $_SESSION['error'] = 'Failed to update books.';
    }

    header('location: book.php');
    exit();
} else {
    $_SESSION['error'] = 'Select a book to edit first.';
    header('location: book.php');
    exit();
}
?>
