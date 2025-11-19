<?php 
include 'includes/session.php';
include 'includes/conn.php';

// 🔸 Handle Add / Edit / Delete before any HTML output
if (isset($_POST['save'])) {
    $id = $_POST['id'] ?? '';
    $identifiers = trim($_POST['identifiers']);
    $author = trim($_POST['author']);
    $title = trim($_POST['title']);
    $published_date = $_POST['published_date'];
    $format = $_POST['format'];
    $tags = $_POST['tags'];
    $external_link = $_POST['external_link'];

    $upload_dir = '../e-books/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $file_path = '';
    if (!empty($_FILES['book_file']['name'])) {
        $file_name = time() . '_' . basename($_FILES['book_file']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['book_file']['tmp_name'], $target_file)) {
            $file_path = $target_file;
        }
    }

    // 🔹 Check for duplicates
    $duplicateQuery = "SELECT * FROM calibre_books WHERE (identifiers=? OR title=?)";
    if ($id != '') $duplicateQuery .= " AND id<>?";
    $stmtCheck = $conn->prepare($duplicateQuery);
    if ($id != '') $stmtCheck->bind_param("ssi", $identifiers, $title, $id);
    else $stmtCheck->bind_param("ss", $identifiers, $title);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    if ($resultCheck->num_rows > 0) {
        $_SESSION['error'] = "A book with the same Identifier or Title already exists!";
        header("Location: calibre_books.php");
        exit();
    }

    if ($id == '') {
        $stmt = $conn->prepare("INSERT INTO calibre_books (identifiers, author, title, published_date, format, tags, file_path, external_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $identifiers, $author, $title, $published_date, $format, $tags, $file_path, $external_link);
        $stmt->execute();
        $_SESSION['success'] = "E-Book added successfully!";
    } else {
        if ($file_path != '') {
            $get = $conn->query("SELECT file_path FROM calibre_books WHERE id=$id")->fetch_assoc();
            if ($get && file_exists($get['file_path'])) unlink($get['file_path']);
            $stmt = $conn->prepare("UPDATE calibre_books SET identifiers=?, author=?, title=?, published_date=?, format=?, tags=?, file_path=?, external_link=? WHERE id=?");
            $stmt->bind_param("ssssssssi", $identifiers, $author, $title, $published_date, $format, $tags, $file_path, $external_link, $id);
        } else {
            $stmt = $conn->prepare("UPDATE calibre_books SET identifiers=?, author=?, title=?, published_date=?, format=?, tags=?, external_link=? WHERE id=?");
            $stmt->bind_param("sssssssi", $identifiers, $author, $title, $published_date, $format, $tags, $external_link, $id);
        }
        $stmt->execute();
        $_SESSION['success'] = "E-Book updated successfully!";
    }

    header("Location: calibre_books.php");
    exit();
}

// 🗂️ Soft Delete: Move to Archive
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $book = $conn->query("SELECT * FROM calibre_books WHERE id=$id")->fetch_assoc();
    
    if ($book) {
        // Insert into archive table
        $stmtArchive = $conn->prepare("INSERT INTO calibre_books_archive (id, identifiers, author, title, published_date, format, tags, file_path, external_link, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtArchive->bind_param("isssssssss", 
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
        $stmtArchive->execute();

        // Delete from main table
        $conn->query("DELETE FROM calibre_books WHERE id=$id");

        $_SESSION['success'] = "E-Book moved to archive successfully!";
    } else {
        $_SESSION['error'] = "E-Book not found!";
    }

    header("Location: calibre_books.php");
    exit();
}

// ✏️ Edit record
$editRow = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editRow = $conn->query("SELECT * FROM calibre_books WHERE id=$editId")->fetch_assoc();
}

// ✅ PAGINATION + SEARCH
$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $searchSafe = $conn->real_escape_string($search);
    $where = "WHERE identifiers LIKE '%$searchSafe%' OR author LIKE '%$searchSafe%' OR title LIKE '%$searchSafe%' OR tags LIKE '%$searchSafe%'";
}

$total_result = $conn->query("SELECT COUNT(*) AS total FROM calibre_books $where");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = $conn->query("SELECT * FROM calibre_books $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");

include 'includes/header.php';
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h1 style="font-weight: 800; margin: 0; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        <i class="fa fa-tablet-alt" style="margin-right: 10px;"></i> E-Book Collection
      </h1>
      <ol class="breadcrumb" style="background-color: transparent; margin: 10px 0 0 0; padding: 0; font-weight: 600;">
        <li style="color: #84ffceff;">HOME</li>
        <li><a href="home.php" style="color: #FFD700;"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li style="color: #84ffceff;">MANAGE</li>
        <li class="active" style="color: #ffffff;">E-Book List</li>
      </ol>
    </section>

    <section class="content" style="background: linear-gradient(135deg, #f8fff0 0%, #e8f5e8 100%); padding: 20px; border-radius: 0 0 10px 10px; min-height: 80vh;">
      
      <!-- Alert -->
      <section id="ebookAlertContainer" style="margin-bottom: 20px;">
        <?php
          if(isset($_SESSION['error'])){
            echo "
            <div class='alert alert-danger alert-dismissible' style='background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true' style='color: white; opacity: 0.8;'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Alert!</h4>".$_SESSION['error']."
            </div>";
            unset($_SESSION['error']);
          }
          if(isset($_SESSION['success'])){
            echo "
            <div class='alert alert-success alert-dismissible' style='background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: #003300; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true' style='color: #003300; opacity: 0.8;'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>".$_SESSION['success']."
            </div>";
            unset($_SESSION['success']);
          }
        ?>
      </section>

      <!-- Box -->
      <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">
        
        <!-- Box Header -->
        <div class="box-header with-border" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px; border-bottom: 2px solid #006400;">
          <div class="row">
            <div class="col-md-6">
              <h3 style="font-weight: 700; color: #006400; margin: 0; font-size: 22px;">
                <i class="fa fa-digital-tachograph" style="margin-right: 10px;"></i>Digital Library
              </h3>
              <small style="color: #006400; font-weight: 500;">Manage your digital e-book collection</small>
            </div>
            <div class="col-md-6 text-right">
              <button class="btn btn-success btn-flat" type="button" data-toggle="collapse" data-target="#ebookForm" 
                      style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px; box-shadow: 0 2px 4px rgba(0,100,0,0.2);">
                <i class="fa fa-plus-circle"></i> Add E-Book
              </button>
            </div>
          </div>
        </div>

        <!-- Form Section -->
        <div class="collapse <?= $editRow ? 'show' : '' ?>" id="ebookForm">
          <div class="box-body" style="background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%); padding: 25px; border-bottom: 1px solid #e0e0e0;">
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="id" value="<?= $editRow['id'] ?? '' ?>">
              <!-- Form fields same as your original code -->
              <div class="form-group">
                <label>Identifier</label>
                <input type="text" name="identifiers" class="form-control" value="<?= $editRow['identifiers'] ?? '' ?>" required>
              </div>
              <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" class="form-control" value="<?= $editRow['author'] ?? '' ?>" required>
              </div>
              <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="<?= $editRow['title'] ?? '' ?>" required>
              </div>
              <div class="form-group">
                <label>Published Date</label>
                <input type="date" name="published_date" class="form-control" value="<?= $editRow['published_date'] ?? '' ?>">
              </div>
              <div class="form-group">
                <label>Format</label>
                <input type="text" name="format" class="form-control" value="<?= $editRow['format'] ?? '' ?>">
              </div>
              <div class="form-group">
                <label>Tags</label>
                <input type="text" name="tags" class="form-control" value="<?= $editRow['tags'] ?? '' ?>">
              </div>
              <div class="form-group">
                <label>File Upload</label>
                <input type="file" name="book_file" class="form-control">
              </div>
              <div class="form-group">
                <label>External Link</label>
                <input type="url" name="external_link" class="form-control" value="<?= $editRow['external_link'] ?? '' ?>">
              </div>
              <div class="text-right" style="margin-top: 20px;">
                <button type="submit" name="save" class="btn btn-success btn-flat" 
                        style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px; margin-right: 10px;">
                  <i class="fa fa-save"></i> <?= $editRow ? 'Update' : 'Save' ?>
                </button>
                <?php if ($editRow): ?>
                  <a href="calibre_books.php" class="btn btn-default btn-flat" 
                     style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
                    <i class="fa fa-close"></i> Cancel
                  </a>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>

        <!-- Search -->
        <div class="box-body" style="background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%); padding: 20px 25px;">
          <form method="GET" style="display: flex; justify-content: flex-end; align-items: center; gap: 10px;">
            <div style="position: relative; width: 300px;">
              <input type="text" name="search" class="form-control" placeholder="Search e-books..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="border-radius: 25px; border: 1px solid #006400; padding: 10px 20px; padding-right: 40px;">
              <i class="fa fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #006400;"></i>
            </div>
            <button type="submit" class="btn btn-success btn-flat" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border: none; border-radius: 25px; font-weight: 600; padding: 8px 20px;">
              <i class="fa fa-search"></i> Search
            </button>
          </form>
        </div>

        <!-- Table Section -->
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
                    <a href="?edit=<?= $row['id'] ?>">Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Move to archive?')">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Optimized Pagination -->
        <div class="box-footer text-center" style="margin-top: 20px;">
          Showing <?= ($offset + 1) ?> – <?= min($offset + $limit, $total_records) ?> of <?= $total_records ?>
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm">

              <?php if ($page > 1): ?>
                <li><a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">&laquo; Prev</a></li>
              <?php endif; ?>

              <?php
                $range = 2;
                $start = max(1, $page - $range);
                $end = min($total_pages, $page + $range);

                if ($start > 1) echo '<li><a href="?page=1&search='.urlencode($search).'">1</a></li>';
                if ($start > 2) echo '<li><span>...</span></li>';

                for ($i = $start; $i <= $end; $i++):
              ?>
                <li class="<?= $i == $page ? 'active' : '' ?>"><a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li>
              <?php endfor; ?>

              <?php
                if ($end < $total_pages - 1) echo '<li><span>...</span></li>';
                if ($end < $total_pages) echo '<li><a href="?page='.$total_pages.'&search='.urlencode($search).'">'.$total_pages.'</a></li>';
              ?>

              <?php if ($page < $total_pages): ?>
                <li><a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Next &raquo;</a></li>
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
<style>
/* Custom CSS here if needed */
</style>
</body>
</html>
