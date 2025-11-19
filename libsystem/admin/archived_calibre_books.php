<?php
include 'includes/session.php';
include 'includes/conn.php';

// 🔹 Restore from archive
if (isset($_GET['restore'])) {
    $id = intval($_GET['restore']);
    $book = $conn->query("SELECT * FROM calibre_books_archive WHERE id=$id")->fetch_assoc();
    if ($book) {
        $stmtRestore = $conn->prepare("INSERT INTO calibre_books (id, identifiers, author, title, published_date, format, tags, file_path, external_link, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtRestore->bind_param(
            "isssssssss",
            $book['id'],
            $book['identifiers'],
            $book['author'],
            $book['title'],
            $book['published_date'],
            $book['format'],
            $book['tags'],
            $book['file_path'],
            $book['external_link'],
            $book['created_at']
        );
        $stmtRestore->execute();
        $conn->query("DELETE FROM calibre_books_archive WHERE id=$id");
        $_SESSION['success'] = "E-Book restored successfully!";
    } else {
        $_SESSION['error'] = "E-Book not found!";
    }
    header("Location: archived_calibre_books.php");
    exit();
}

// 🔹 Permanently delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $book = $conn->query("SELECT * FROM calibre_books_archive WHERE id=$id")->fetch_assoc();
    if ($book) {
        if ($book['file_path'] && file_exists($book['file_path'])) unlink($book['file_path']);
        $conn->query("DELETE FROM calibre_books_archive WHERE id=$id");
        $_SESSION['success'] = "E-Book permanently deleted!";
    } else {
        $_SESSION['error'] = "E-Book not found!";
    }
    header("Location: archived_calibre_books.php");
    exit();
}

// ✅ Pagination + search
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $searchSafe = $conn->real_escape_string($search);
    $where = "WHERE identifiers LIKE '%$searchSafe%' OR author LIKE '%$searchSafe%' OR title LIKE '%$searchSafe%' OR tags LIKE '%$searchSafe%'";
}

$total_result = $conn->query("SELECT COUNT(*) AS total FROM calibre_books_archive $where");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = $conn->query("SELECT * FROM calibre_books_archive $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

include 'includes/header.php';
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">
  <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
    <h1 style="font-weight: 800; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
      <i class="fa fa-archive" style="margin-right: 10px;"></i> Archived E-Books
    </h1>
    <ol class="breadcrumb" style="background: transparent; margin: 10px 0 0 0; padding: 0; font-weight: 600;">
      <li style="color: #84ffceff;">HOME</li>
      <li><a href="home.php" style="color: #FFD700;"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="active" style="color: #ffffff;">Archived E-Books</li>
    </ol>
  </section>

  <section class="content" style="background: linear-gradient(135deg, #f8fff0 0%, #e8f5e8 100%); padding: 20px; border-radius: 0 0 10px 10px; min-height: 80vh;">

    <!-- Alerts -->
    <?php
    if(isset($_SESSION['error'])){
        echo "<div class='alert alert-danger alert-dismissible' style='background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                <i class='icon fa fa-warning'></i> ".$_SESSION['error']."
              </div>";
        unset($_SESSION['error']);
    }
    if(isset($_SESSION['success'])){
        echo "<div class='alert alert-success alert-dismissible' style='background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: #003300; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                <button type='button' class='close' data-dismiss='alert'>&times;</button>
                <i class='icon fa fa-check'></i> ".$_SESSION['success']."
              </div>";
        unset($_SESSION['success']);
    }
    ?>

    <!-- Box -->
    <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">

      <!-- Search -->
      <div class="box-body" style="background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%); padding: 20px 25px;">
        <form method="GET" style="display: flex; justify-content: flex-end; gap: 10px;">
          <input type="text" name="search" class="form-control" placeholder="Search e-books..." value="<?= htmlspecialchars($search) ?>" style="border-radius: 25px; border: 1px solid #006400; padding: 8px 20px;">
          <button type="submit" class="btn btn-success btn-flat" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border-radius: 25px; font-weight: 600; padding: 8px 20px;">
            <i class="fa fa-search"></i> Search
          </button>
        </form>
      </div>

      <!-- Table -->
      <div class="box-body table-responsive" style="background-color: #FFFFFF;">
        <table class="table table-bordered table-striped table-hover" style="margin: 0;">
          <thead style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; font-weight: 700;">
            <tr>
              <th>Identifiers</th>
              <th>Author</th>
              <th>Title</th>
              <th>Published</th>
              <th>Format</th>
              <th>Tags</th>
              <th>Access</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><code><?= htmlspecialchars($row['identifiers']) ?></code></td>
              <td><?= htmlspecialchars($row['author']) ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['published_date']) ?></td>
              <td><?= htmlspecialchars($row['format']) ?></td>
              <td><?= $row['tags'] ? "<span class='label label-success'>".htmlspecialchars($row['tags'])."</span>" : '-' ?></td>
              <td>
                <?php if($row['file_path']): ?>
                  <a href="<?= $row['file_path'] ?>" target="_blank">Download</a>
                <?php elseif($row['external_link']): ?>
                  <a href="<?= $row['external_link'] ?>" target="_blank">Visit</a>
                <?php else: ?>
                  Calibre access
                <?php endif; ?>
              </td>
              <td>
                <a href="?restore=<?= $row['id'] ?>" class="btn btn-warning btn-xs" style="margin-right:5px;">Restore</a>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Permanently delete this e-book?')" class="btn btn-danger btn-xs">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="box-footer text-center" style="margin-top: 15px;">
        Showing <?= ($offset + 1) ?> – <?= min($offset + $limit, $total_records) ?> of <?= $total_records ?>
        <nav aria-label="Page navigation">
          <ul class="pagination pagination-sm">
            <?php if ($page > 1): ?>
              <li><a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">&laquo; Prev</a></li>
            <?php endif; ?>
            <?php for ($i=1;$i<=$total_pages;$i++): ?>
              <li class="<?= $i==$page?'active':'' ?>"><a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li>
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
<script>
$(function(){
  $('tbody tr').hover(
    function() { $(this).css('background-color','#f8fff8'); },
    function() { $(this).css('background-color',''); }
  );
});
</script>
</body>
</html>
