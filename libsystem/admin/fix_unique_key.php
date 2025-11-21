<?php
include 'includes/session.php';
include 'includes/conn.php';

echo "<h2>Fixing book_subject_map Unique Key</h2>";

// Drop the incorrect unique key
echo "<p>Step 1: Dropping incorrect unique key...</p>";
$drop = $conn->query("ALTER TABLE book_subject_map DROP INDEX uniq_book_subject");
if($drop) {
    echo "<p style='color: green;'>✅ Dropped uniq_book_subject key</p>";
} else {
    echo "<p style='color: red;'>❌ Error: " . $conn->error . "</p>";
}

// Add correct unique key (composite on BOTH columns together)
echo "<p>Step 2: Adding correct composite unique key...</p>";
$add = $conn->query("ALTER TABLE book_subject_map ADD UNIQUE KEY uniq_book_subject (book_id, subject_id)");
if($add) {
    echo "<p style='color: green;'>✅ Added correct unique key on (book_id, subject_id)</p>";
} else {
    echo "<p style='color: red;'>❌ Error: " . $conn->error . "</p>";
}

// Check foreign keys
echo "<p>Step 3: Checking foreign key constraints...</p>";
$fks = $conn->query("SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME='book_subject_map' AND TABLE_SCHEMA=DATABASE() AND CONSTRAINT_TYPE='FOREIGN KEY'");
if($fks && $fks->num_rows > 0) {
    echo "<p>Foreign keys found:</p><ul>";
    while($fk = $fks->fetch_assoc()) {
        echo "<li>{$fk['CONSTRAINT_NAME']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No foreign keys found</p>";
}

// Test without IGNORE to see real error
echo "<p>Step 4: Testing insert WITHOUT IGNORE to see actual error...</p>";
$test_no_ignore = $conn->query("INSERT INTO book_subject_map (book_id, subject_id) VALUES (1, 19)");
if($test_no_ignore) {
    echo "<p style='color: green;'>✅ Insert successful! Affected rows: " . $conn->affected_rows . "</p>";
} else {
    echo "<p style='color: red;'>❌ Error: " . $conn->error . "</p>";
}

// Test insert with IGNORE
echo "<p>Step 5: Testing insert with IGNORE...</p>";
$test = $conn->query("INSERT IGNORE INTO book_subject_map (book_id, subject_id) VALUES (1, 19)");
if($test) {
    echo "<p style='color: green;'>✅ Insert successful! Affected rows: " . $conn->affected_rows . "</p>";
    if($conn->affected_rows > 0) {
        echo "<p style='color: green;'>🎉 Data was inserted! The fix worked!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ No rows affected (may already exist)</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Error: " . $conn->error . "</p>";
}

// Verify
echo "<p>Step 4: Verifying data...</p>";
$verify = $conn->query("SELECT * FROM book_subject_map");
echo "<p>Total rows in table: " . $verify->num_rows . "</p>";

if($verify->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Book ID</th><th>Subject ID</th></tr>";
    while($r = $verify->fetch_assoc()) {
        echo "<tr><td>{$r['id']}</td><td>{$r['book_id']}</td><td>{$r['subject_id']}</td></tr>";
    }
    echo "</table>";
}

echo "<hr><p><a href='subjects.php'>← Back to Subjects</a></p>";
?>
