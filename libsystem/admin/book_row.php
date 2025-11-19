<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Fetch the selected book
    $sql = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$book) {
        echo json_encode(['error' => 'Invalid book ID']);
        exit();
    }

    // Fetch categories assigned to this book
    $selectedCats = [];
    $sql = "SELECT category_id FROM book_category_map WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $selectedCats[] = (string)$row['category_id'];
    }
    $stmt->close();

    // Return all necessary details for the edit modal
    echo json_encode([
        'id' => $book['id'],
        'isbn' => $book['isbn'],
        'call_no' => $book['call_no'],
        'title' => $book['title'],
        'author' => $book['author'],
        'publisher' => $book['publisher'],
        'publish_date' => $book['publish_date'],
        'categories' => $selectedCats
    ]);
}
?>
