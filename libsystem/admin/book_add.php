<?php
include 'includes/session.php';
include 'includes/conn.php';

if (isset($_POST['add'])) {

    // ===== Get input fields =====
    $isbn = trim($_POST['isbn']);
    $call_no = trim($_POST['call_no']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $publisher = trim($_POST['publisher']);
    $pub_date = trim($_POST['pub_date']);
    $copies = isset($_POST['num_copies']) ? intval($_POST['num_copies']) : 1;
    $categories = isset($_POST['category']) ? $_POST['category'] : [];

   // $subjects = isset($_POST['subject']) ? $_POST['subject'] : [];

    // ===== Validation =====
    if (empty($categories)) {
        $_SESSION['error'] = 'Please select at least one category.';
        header('location: book.php');
        exit();
    }

   // if (empty($subjects)) {
     //   $_SESSION['error'] = 'Please select at least one subject.';
       // header('location: book.php');
        //exit();
    //}

    if (!preg_match("/^\d{4}(-\d{2}-\d{2})?$/", $pub_date)) {
        $_SESSION['error'] = "Publish Date must be YYYY or YYYY-MM-DD";
        header('location: book.php');
        exit();
    }

    

    // ===== Prepare main insert =====
    $stmt = $conn->prepare("
        INSERT INTO books (isbn, call_no, title, author, publisher, publish_date, copy_number, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)
    ");
    $stmt->bind_param("ssssssi", $isbn, $call_no, $title, $author, $publisher, $pub_date, $copy_number);

    $cat_stmt = $conn->prepare("INSERT INTO book_category_map (book_id, category_id) VALUES (?, ?)");
   // $subj_stmt = $conn->prepare("INSERT INTO book_subject_map (subject_id, book_id) VALUES (?, ?)"); // ✅ fixed order

    $countInserted = 0;

    for ($i = 1; $i <= $copies; $i++) {
        $copy_number = $i;

        if ($stmt->execute()) {
            $book_id = $stmt->insert_id;

            // Map categories (book → category)
            foreach ($categories as $cat_id) {
                $cat_stmt->bind_param("ii", $book_id, $cat_id);
                $cat_stmt->execute();
            }

            // Map subjects (subject → book)
         //   foreach ($subjects as $sub_id) {
        //        $subj_stmt->bind_param("ii", $sub_id, $book_id);
        //        $subj_stmt->execute();
        //    }

            $countInserted++;
        }
    }

    $stmt->close();
    $cat_stmt->close();

    if ($countInserted > 0) {
        $_SESSION['success'] = "Successfully added $countInserted copy/copies of the book.";
    } else {
        $_SESSION['error'] = "Failed to add the book.";
    }

    header('location: book.php');
    exit();

} else {
    $_SESSION['error'] = 'Fill up add form first.';
    header('location: book.php');
    exit();
}
?>
