<?php
include 'includes/session.php';
include 'includes/conn.php';

echo "<h2>Book-Subject Assignments Debug</h2>";

// Check if book_subject_map table exists
$check_table = $conn->query("SHOW TABLES LIKE 'book_subject_map'");
if($check_table->num_rows == 0) {
    echo "<p style='color: red;'>❌ ERROR: book_subject_map table does NOT exist!</p>";
    echo "<p>You need to create this table. Run this SQL:</p>";
    echo "<pre>CREATE TABLE IF NOT EXISTS book_subject_map (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    subject_id INT NOT NULL,
    UNIQUE KEY unique_book_subject (book_id, subject_id),
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subject(id) ON DELETE CASCADE
) ENGINE=InnoDB;</pre>";
} else {
    echo "<p style='color: green;'>✅ book_subject_map table exists</p>";
    
    // Show all assignments
    $sql = "SELECT bsm.*, b.title AS book_title, s.name AS subject_name 
            FROM book_subject_map bsm
            LEFT JOIN books b ON b.id = bsm.book_id
            LEFT JOIN subject s ON s.id = bsm.subject_id
            ORDER BY bsm.id DESC
            LIMIT 20";
    $result = $conn->query($sql);
    
    echo "<h3>Recent Assignments (Last 20):</h3>";
    if($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Book ID</th><th>Book Title</th><th>Subject ID</th><th>Subject Name</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>".$row['book_id']."</td>";
            echo "<td>".htmlspecialchars($row['book_title'] ?? 'NULL')."</td>";
            echo "<td>".$row['subject_id']."</td>";
            echo "<td>".htmlspecialchars($row['subject_name'] ?? 'NULL')."</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No assignments found in book_subject_map table</p>";
    }
}

// Check subjects
echo "<h3>Available Subjects:</h3>";
$subjects = $conn->query("SELECT * FROM subject ORDER BY name ASC");
if($subjects->num_rows > 0) {
    echo "<ul>";
    while($s = $subjects->fetch_assoc()) {
        echo "<li>ID: {$s['id']} - Name: ".htmlspecialchars($s['name'])."</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No subjects found</p>";
}

// Check sample books
echo "<h3>Sample Books (First 5):</h3>";
$books = $conn->query("SELECT id, title, call_no FROM books LIMIT 5");
if($books->num_rows > 0) {
    echo "<ul>";
    while($b = $books->fetch_assoc()) {
        echo "<li>ID: {$b['id']} - Title: ".htmlspecialchars($b['title'])." - Call No: ".htmlspecialchars($b['call_no'])."</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No books found</p>";
}
?>
