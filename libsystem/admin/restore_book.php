<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // 1️⃣ Fetch the archived book
    $sql = "SELECT * FROM archived_books WHERE archive_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    $stmt->close();

    if ($book) {
        // 2️⃣ Restore the book record to 'books' (align columns with current schema)
        $isbn = $book['isbn'] ?? '';
        $call_no = $book['call_no'] ?? '';
        $location = $book['location'] ?? 'Library';
        $title = $book['title'] ?? '';
        $subject = $book['subject'] ?? '';
        $author = $book['author'] ?? '';
        $publisher = $book['publisher'] ?? '';
        $publish_date = $book['publish_date'] ?? '';
        $copy_number = isset($book['copy_number']) ? intval($book['copy_number']) : 1;
        $num_copies = isset($book['num_copies']) ? intval($book['num_copies']) : 1;
        $status = 0;

        $stmt = $conn->prepare("
            INSERT INTO books 
            (isbn, call_no, title, author, publisher, publish_date, subject, location, copy_number, num_copies, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssssssiii",
            $isbn, $call_no, $title, $author, $publisher, $publish_date, $subject, $location, $copy_number, $num_copies, $status
        );
        $stmt->execute();
        $restored_book_id = $conn->insert_id; // newly inserted book ID
        $stmt->close();

        // 3️⃣ Restore category mappings from archived_book_category_map
        $catSql = "SELECT category_id FROM archived_book_category_map WHERE archive_id = ?";
        $catStmt = $conn->prepare($catSql);
        $catStmt->bind_param("i", $id);
        $catStmt->execute();
        $catRes = $catStmt->get_result();

        if ($catRes->num_rows > 0) {
            $insertCat = $conn->prepare("INSERT INTO book_category_map (book_id, category_id) VALUES (?, ?)");
            while ($cat = $catRes->fetch_assoc()) {
                $insertCat->bind_param("ii", $restored_book_id, $cat['category_id']);
                $insertCat->execute();
            }
            $insertCat->close();
        }
        $catStmt->close();

        // 4️⃣ Delete from archived category mapping and archived books
        $delMap = $conn->prepare("DELETE FROM archived_book_category_map WHERE archive_id = ?");
        $delMap->bind_param("i", $id);
        $delMap->execute();
        $delMap->close();

        $stmt = $conn->prepare("DELETE FROM archived_books WHERE archive_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Book and category restored successfully';
    } else {
        $_SESSION['error'] = 'Book not found';
    }
} else {
    $_SESSION['error'] = 'No book selected';
}

header('Location: archived_book.php');
exit;
?>
