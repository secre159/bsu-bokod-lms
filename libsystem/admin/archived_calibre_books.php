<?php
include 'includes/session.php';
include 'includes/conn.php';

// -----------------------------
// Restore archived book
// -----------------------------
if (isset($_GET['restore'])) {
    $id = intval($_GET['restore']);

    // Ensure record exists in archive
    $check = $conn->query("SELECT * FROM calibre_books_archive WHERE id = $id");
    if ($check && $check->num_rows > 0) {

        // Insert into main table from archive (avoid referencing unknown columns)
        // Adjust target columns if your calibre_books table has different column names.
        $insert_sql = "INSERT INTO calibre_books (title, author, isbn, subject, status)
                       SELECT title, author, isbn, subject, 'active' FROM calibre_books_archive WHERE id = ?";
        $stmt = $conn->prepare($insert_sql);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                // Delete from archive after successful insert
                $del = $conn->prepare("DELETE FROM calibre_books_archive WHERE id = ?");
                if ($del) {
                    $del->bind_param("i", $id);
                    $del->execute();
                    $del->close();
                }
                $_SESSION['success'] = "E-Book restored successfully!";
            } else {
                $_SESSION['error'] = "Failed to restore e-book (insert failed).";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Failed to prepare restore statement.";
        }

    } else {
        $_SESSION['error'] = "E-Book not found in archive.";
    }

    header("Location: archived_calibre_books.php");
    exit();
}

// -----------------------------
// Permanently delete archived book
// -----------------------------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $check = $conn->query("SELECT * FROM calibre_books_archive WHERE id = $id");
    if ($check && $check->num_rows > 0) {
        // If your archive table stores a file path and you want to delete file, add file_path column logic here.
        $del = $conn->prepare("DELETE FROM calibre_books_archive WHERE id = ?");
        if ($del) {
            $del->bind_param("i", $id);
            if ($del->execute()) {
                $_SESSION['success'] = "E-Book permanently deleted!";
            } else {
                $_SESSION['error'] = "Failed to permanently delete e-book.";
            }
            $del->close();
        } else {
            $_SESSION['error'] = "Failed to prepare delete statement.";
        }
    } else {
        $_SESSION['error'] = "E-Book not found in archive.";
    }

    header("Location: archived_calibre_books.php");
    exit();
}

// -----------------------------
// Pagination + Search (safe)
// -----------------------------
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clauses = [];
$params = [];

if ($search !== '') {
    $searchSafe = $conn->real_escape_string($search);
    $where_clauses[] = "(title LIKE '%$searchSafe%' OR author LIKE '%$searchSafe%' OR isbn LIKE '%$searchSafe%' OR subject LIKE '%$searchSafe%')";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Count total
$total_query = "SELECT COUNT(*) AS total FROM calibre_books_archive $where_sql";
$total_result = $conn->query($total_query);
if ($total_result) {
    $total_row = $total_result->fetch_assoc();
    $total_records = intval($total_row['total']);
} else {
    // Query failed — avoid undefined variables and show zero results
    $total_records = 0;
    $_SESSION['error'] = "Failed to retrieve archive count: " . $conn->error;
}

$total_pages = ($total_records > 0) ? ceil($total_records / $limit) : 1;

// Fetch page rows — order by date_archived (exists in your table)
$data_query = "SELECT * FROM calibre_books_archive $where_sql ORDER BY date_archived DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($data_query);
if ($result === false) {
    $_SESSION['error'] = "Failed to retrieve archived e-books: " . $conn->error;
    // Create an empty mysqli_result-like fallback: we'll handle by checking $result before fetch
}

// -----------------------------
// Render page
// -----------------------------
include 'includes/header.php';
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">
  <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px;">
    <h1><i class="fa fa-archive"></i> Archived E-Books</h1>
  </section>

  <section class="content" style="padding: 20px;">

    <!-- Alerts -->
    <?php
    if(isset($_SESSION['error'])){
        echo "<div class='alert alert-danger'>".htmlspecialchars($_SESSION['error'])."</div>";
        unset($_SESSION['error']);
    }
    if(isset($_SESSION['success'])){
        echo "<div class='alert alert-success'>".htmlspecialchars($_SESSION['success'])."</div>";
        unset($_SESSION['success']);
    }
    ?>

    <div class="box">

      <!-- Search -->
      <div class="box-body">
        <form method="GET" class="form-inline pull-right" style="display:flex; gap:8px; justify-content:flex-end;">
          <input type="text" name="search" class="form-control" placeholder="Search e-books..." value="<?= htmlspecialchars($search) ?>">
          <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
        </form>
      </div>

      <!-- Table -->
      <div class="box-body table-responsive">
        <table class="table table-bordered table-hover">
          <thead style="background:#006400; color:#FFD700;">
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Author</th>
              <th>ISBN</th>
              <th>Subject</th>
              <th>Status</th>
              <th>Date Archived</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['author']) ?></td>
              <td><?= htmlspecialchars($row['isbn']) ?></td>
              <td><?= htmlspecialchars($row['subject']) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
              <td><?= htmlspecialchars($row['date_archived']) ?></td>
              <td>
                <a href="?restore=<?= $row['id'] ?>" class="btn btn-warning btn-xs" style="margin-right:5px;">Restore</a>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Permanently delete this e-book?')" class="btn btn-danger btn-xs">Delete</a>
              </td>
            </tr>
            <?php
                endwhile;
            else:
            ?>
            <tr>
              <td colspan="8" class="text-center">No archived e-books found.</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="box-footer text-center">
        <?php
        $start = ($total_records == 0) ? 0 : ($offset + 1);
        $end = min($offset + $limit, $total_records);
        ?>
        Showing <?= $start ?> – <?= $end ?> of <?= $total_records ?>
        <nav style="margin-top:10px;">
          <ul class="pagination pagination-sm">
            <?php if ($page > 1): ?>
              <li><a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">&laquo; Prev</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <li class="<?= ($i == $page) ? 'active' : '' ?>"><a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
              <li><a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next &raquo;</a></li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>

    </div>
  </section>
</div>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
