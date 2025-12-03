<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);

    // Get book info
    $stmt = $conn->prepare("SELECT title, author, publish_date, publisher FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($book) {
        $title = $book['title'];
        $author = $book['author'];
        $publish_date = $book['publish_date'];
        $publisher = $book['publisher'];

        // Get all matching books (same title, author, publisher, and publish_date)
        $stmt = $conn->prepare("SELECT * FROM books WHERE title = ? AND author = ? AND publisher = ? AND publish_date = ?");
        $stmt->bind_param("ssss", $title, $author, $publisher, $publish_date);
        $stmt->execute();
        $books = $stmt->get_result();
        
        // Archive each matching book
        while ($b = $books->fetch_assoc()) {
            $book_id = $b['id'];

            // Insert into archived_books
            $archive = $conn->prepare("
                INSERT INTO archived_books (
                    book_id, isbn, call_no, location, title, author, publisher, publish_date,
                    copy_number, date_added, status, category_id, subject_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $archive->bind_param(
                "issssssssiiii",
                $b['id'],
                $b['isbn'],
                $b['call_no'],
                $b['location'],
                $b['title'],
                $b['author'],
                $b['publisher'],
                $b['publish_date'],
                $b['copy_number'],
                $b['date_added'],
                $b['status'],
                $b['category_id'],
                $b['subject_id']
            );
            $archive->execute();

            // Get the archive_id of the inserted record
            $archive_id = $archive->insert_id;

            // Copy category mapping (if any)
            $cat_query = $conn->prepare("SELECT category_id FROM book_category_map WHERE book_id = ?");
            $cat_query->bind_param("i", $book_id);
            $cat_query->execute();
            $cat_result = $cat_query->get_result();

            while ($cat = $cat_result->fetch_assoc()) {
                $map = $conn->prepare("
                    INSERT INTO archived_book_category_map (archive_id, category_id)
                    VALUES (?, ?)
                ");
                $map->bind_param("ii", $archive_id, $cat['category_id']);
                $map->execute();
                $map->close();
            }
            $cat_query->close();

            // Delete related records
            $conn->query("DELETE FROM borrow WHERE book_id = $book_id");
            $conn->query("DELETE FROM returns WHERE book_id = $book_id");
            $conn->query("DELETE FROM book_category_map WHERE book_id = $book_id");

            // Finally delete the book
            $del = $conn->prepare("DELETE FROM books WHERE id = ?");
            $del->bind_param("i", $book_id);
            $del->execute();
            $del->close();

            $archive->close();
        }

        $_SESSION['success'] = 'All copies of the book have been archived and deleted successfully.';
    } else {
        $_SESSION['error'] = 'Book not found.';
    }
} else {
    $_SESSION['error'] = 'Select a book to delete.';
}

header('location: book.php');
exit();
?>
