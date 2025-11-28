<?php
include 'includes/session.php';
include 'includes/conn.php';
include 'includes/header.php';

// Handle Add Report
if(isset($_POST['report'])){
    $book_id = $_POST['book_id'];
    $status = $_POST['status']; // 'Damaged' or 'Lost'
    $notes = $_POST['notes'];
    $date_reported = date('Y-m-d');

    // Check book quantity
    $res = $conn->query("SELECT quantity FROM books WHERE id=$book_id LIMIT 1");
    $book = $res->fetch_assoc();

    if($book['quantity'] <= 0){
        $_SESSION['error'] = "Cannot report this book as damaged/lost. No available copies.";
    } else {
        $stmt = $conn->prepare("INSERT INTO book_damage_lost (book_id, status, notes, date_reported) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $book_id, $status, $notes, $date_reported);
        if($stmt->execute()){
            $conn->query("UPDATE books SET quantity = quantity - 1 WHERE id = $book_id");
            $_SESSION['success'] = "Book reported as $status successfully.";
        } else {
            $_SESSION['error'] = "Failed to report book. Try again.";
        }
    }
    header("Location: damage_lost.php");
    exit();
}

// Handle Edit Report
if(isset($_POST['edit_report'])){
    $id = $_POST['id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("UPDATE book_damage_lost SET status=?, notes=? WHERE id=?");
    $stmt->bind_param("ssi", $status, $notes, $id);
    if($stmt->execute()){
        $_SESSION['success'] = "Report updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update report.";
    }
    header("Location: damage_lost.php");
    exit();
}

// Handle Delete Report
if(isset($_POST['delete_report'])){
    $id = $_POST['id'];

    // Get book ID for quantity restoration
    $res = $conn->query("SELECT book_id FROM book_damage_lost WHERE id=$id LIMIT 1");
    $book = $res->fetch_assoc();

    if($conn->query("DELETE FROM book_damage_lost WHERE id=$id")){
        // Restore book quantity
        $conn->query("UPDATE books SET quantity = quantity + 1 WHERE id=".$book['book_id']);
        $_SESSION['success'] = "Report deleted successfully and quantity restored.";
    } else {
        $_SESSION['error'] = "Failed to delete report.";
    }
    header("Location: damage_lost.php");
    exit();
}

// Fetch damaged/lost books
$damage_lost_sql = "SELECT dl.*, b.title, b.call_no, b.isbn 
                    FROM book_damage_lost dl 
                    LEFT JOIN books b ON dl.book_id = b.id
                    ORDER BY dl.date_reported DESC";
$damage_lost_query = $conn->query($damage_lost_sql);
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

<?php include 'includes/navbar.php'; ?>
<?php include 'includes/menubar.php'; ?>

<div class="content-wrapper">
  <section class="content-header" style="background-color: #006400; color: #FFD700; padding: 15px;">
    <h1><i class="fa fa-exclamation-triangle"></i> Damaged / Lost Books</h1>
    <ol class="breadcrumb" style="background:transparent;">
      <li><a href="home.php" style="color:#FFD700;"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active" style="color:#FFF;">Damaged / Lost Books</li>
    </ol>
  </section>

  <section class="content" style="padding:15px; background-color:#F8FFF0;">
    <?php if(isset($_SESSION['error'])){ ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><i class="fa fa-warning"></i> Error!</h4>
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
      </div>
    <?php } ?>
    <?php if(isset($_SESSION['success'])){ ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h4><i class="fa fa-check"></i> Success!</h4>
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php } ?>

    <!-- Buttons -->
    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reportModal">
      <i class="fa fa-plus"></i> Report Damaged/Lost
    </button>

    <br><br>

    <!-- Table -->
    <div class="box">
      <div class="box-body table-responsive">
        <table id="damageTable" class="table table-bordered table-striped">
          <thead style="background-color:#006400; color:#FFD700;">
            <tr>
              <th>Book Title</th>
              <th>Call No.</th>
              <th>ISBN</th>
              <th>Status</th>
              <th>Notes</th>
              <th>Date Reported</th>
              <th>Tools</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $damage_lost_query->fetch_assoc()){ ?>
              <tr>
                <td><?= $row['title'] ?></td>
                <td><?= $row['call_no'] ?></td>
                <td><?= $row['isbn'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['notes'] ?></td>
                <td><?= date('M d, Y', strtotime($row['date_reported'])) ?></td>
                <td>
                  <button class="btn btn-success btn-xs edit" data-id="<?= $row['id'] ?>"><i class="fa fa-edit"></i></button>
                  <button class="btn btn-danger btn-xs delete" data-id="<?= $row['id'] ?>"><i class="fa fa-trash"></i></button>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>

  </section>
</div>

<!-- Add Modal -->
<div class="modal fade" id="reportModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header" style="background-color:#006400; color:#FFD700;">
          <h4 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Report Damaged / Lost Book</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Book</label>
            <select name="book_id" class="form-control" required>
              <option value="">Select Book</option>
              <?php
                $books = $conn->query("SELECT * FROM books WHERE quantity > 0 ORDER BY title ASC");
                while($b = $books->fetch_assoc()){
                  echo "<option value='".$b['id']."'>".$b['title']." (".$b['call_no'].")</option>";
                }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
              <option value="Damaged">Damaged</option>
              <option value="Lost">Lost</option>
            </select>
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="report" class="btn btn-danger"><i class="fa fa-check"></i> Report</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header" style="background-color:#32CD32; color:#fff;">
          <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Report</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" id="edit_status" required>
              <option value="Damaged">Damaged</option>
              <option value="Lost">Lost</option>
            </select>
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" id="edit_notes"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="edit_report" class="btn btn-success"><i class="fa fa-check"></i> Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="">
        <input type="hidden" name="id" id="delete_id">
        <div class="modal-header" style="background-color:#FF4C4C; color:#fff;">
          <h4 class="modal-title"><i class="fa fa-trash"></i> Delete Report</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this report? Book quantity will be restored.</p>
        </div>
        <div class="modal-footer">
          <button type="submit" name="delete_report" class="btn btn-danger"><i class="fa fa-check"></i> Delete</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  $('#damageTable').DataTable({
    "pageLength": 25,
    "lengthMenu": [[10,25,50,100],[10,25,50,100]],
    "order": [[5,"desc"]]
  });

  // Edit button
  $(document).on('click', '.edit', function(){
    var id = $(this).data('id');
    $.ajax({
      type: 'POST',
      url: 'get_damage_row.php',
      data: {id:id},
      dataType: 'json',
      success: function(response){
        $('#edit_id').val(response.id);
        $('#edit_status').val(response.status);
        $('#edit_notes').val(response.notes);
        $('#editModal').modal('show');
      }
    });
  });

  // Delete button
  $(document).on('click', '.delete', function(){
    var id = $(this).data('id');
    $('#delete_id').val(id);
    $('#deleteModal').modal('show');
  });
});
</script>

</body>
</html>
