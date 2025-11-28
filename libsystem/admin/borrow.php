<?php 
include 'includes/session.php'; 
include 'includes/header.php'; 
include 'includes/conn.php';

// Handle Borrow Submission
if(isset($_POST['borrow'])){
    $borrower_id = $_POST['borrower_id'];
    $borrower_type = $_POST['borrower_type_hidden']; // 'student' or 'faculty'
    $book_id = $_POST['book_id'];
    $borrow_date = date('Y-m-d'); // today
    $due_date = date('Y-m-d', strtotime('+7 days')); // example: 7 days borrow

    // Check if book exists and has available quantity
    $book_check = $conn->prepare("SELECT quantity FROM books WHERE id = ? LIMIT 1");
    $book_check->bind_param("i", $book_id);
    $book_check->execute();
    $result = $book_check->get_result();

    if($result->num_rows == 0){
        $_SESSION['error'] = "Selected book does not exist.";
        header("Location: transactions.php");
        exit();
    }

    $book = $result->fetch_assoc();
    if($book['quantity'] <= 0){
        $_SESSION['error'] = "Sorry, this book is currently borrowed by someone else.";
        header("Location: transactions.php");
        exit();
    }

    // Insert borrow transaction
    $stmt = $conn->prepare("INSERT INTO borrow_transactions (borrower_id, borrower_type, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, ?, 'borrowed')");
    $stmt->bind_param("isiss", $borrower_id, $borrower_type, $book_id, $borrow_date, $due_date);

    if($stmt->execute()){
        // Reduce book quantity
        $conn->query("UPDATE books SET quantity = quantity - 1 WHERE id = $book_id");
        $_SESSION['success'] = "Book borrowed successfully!";
    } else {
        $_SESSION['error'] = "Failed to borrow book. Please try again.";
    }

    header("Location: transactions.php");
    exit();
}

// Handle Return Submission
if(isset($_POST['return'])){
    $borrow_id = $_POST['borrow_id'];
    $return_date = date('Y-m-d');

    // Get book_id
    $res = $conn->query("SELECT book_id FROM borrow_transactions WHERE id = $borrow_id LIMIT 1");
    $row = $res->fetch_assoc();
    $book_id = $row['book_id'];

    // Update borrow transaction
    $conn->query("UPDATE borrow_transactions SET status='returned', return_date='$return_date' WHERE id=$borrow_id");

    // Increase book quantity
    $conn->query("UPDATE books SET quantity = quantity + 1 WHERE id = $book_id");

    $_SESSION['success'] = "Book returned successfully!";
    header("Location: transactions.php");
    exit();
}
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">

    <!-- HEADER -->
    <section class="content-header" style="background-color: #006400; color: #FFD700; padding: 15px;">
      <h1><b>📚 Book Transactions</b></h1>
      <ol class="breadcrumb" style="background:transparent;">
        <li><a href="#" style="color:#FFD700;"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active" style="color:#FFF;">Transactions</li>
      </ol>
    </section>

    <!-- CONTENT -->
    <section class="content" style="background-color:#F8FFF0; padding:15px;">

      <!-- ALERTS -->
      <?php if(isset($_SESSION['error'])){ ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <h4><i class="fa fa-warning"></i> Error!</h4>
          <?php 
            if(is_array($_SESSION['error'])){
              echo '<ul>';
              foreach($_SESSION['error'] as $error){ echo "<li>".$error."</li>"; }
              echo '</ul>';
            } else {
              echo $_SESSION['error'];
            }
            unset($_SESSION['error']);
          ?>
        </div>
      <?php } ?>

      <?php if(isset($_SESSION['success'])){ ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <h4><i class="fa fa-check"></i> Success!</h4>
          <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php } ?>

      <!-- TABS -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#borrow" data-toggle="tab"><b>Borrow Books</b></a></li>
          <li><a href="#return" data-toggle="tab"><b>Return Books</b></a></li>
        </ul>

        <div class="tab-content">
          <!-- ========== BORROW TAB ========== -->
          <div class="tab-pane active" id="borrow">
            <div class="box">
              <div class="box-header with-border">
                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#addBorrow">
                  <i class="fa fa-plus"></i> Borrow Book
                </button>
              </div>
              <div class="box-body">
                <table id="borrowTable" class="table table-bordered table-striped">
                  <thead style="background-color:#006400; color:#FFD700;">
                    <tr>
                      <th>Borrower Type</th>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Department/Course</th>
                      <th>Book Call No.</th>
                      <th>Book Title</th>
                      <th>Date Published</th>
                      <th>Date Borrowed</th>
                      <th>Due Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $today = date('Y-m-d');
                      $sql = "SELECT bt.*, 
                                     b.call_no, b.title, b.publish_date,
                                     s.student_id, s.firstname AS s_fname, s.lastname AS s_lname, s.course,
                                     f.faculty_id, f.firstname AS f_fname, f.lastname AS f_lname, f.department
                              FROM borrow_transactions bt
                              LEFT JOIN books b ON bt.book_id = b.id
                              LEFT JOIN students s ON (bt.borrower_type='student' AND bt.borrower_id=s.id)
                              LEFT JOIN faculty f ON (bt.borrower_type='faculty' AND bt.borrower_id=f.id)
                              ORDER BY bt.borrow_date DESC";
                      $query = $conn->query($sql);
                      while($row = $query->fetch_assoc()){
                        if($row['borrower_type'] == 'student'){
                          $borrowerID = $row['student_id'];
                          $borrowerName = $row['s_fname'].' '.$row['s_lname'];
                          $deptCourse = $row['course'];
                        } else {
                          $borrowerID = $row['faculty_id'];
                          $borrowerName = $row['f_fname'].' '.$row['f_lname'];
                          $deptCourse = $row['department'];
                        }

                        if($row['status'] == 'returned'){
                          $status = '<span class="label label-success">Returned</span>';
                        } elseif($today > $row['due_date']){
                          $status = '<span class="label label-danger">Overdue</span>';
                        } else {
                          $status = '<span class="label label-warning">Borrowed</span>';
                        }

                        echo "
                          <tr>
                            <td>".ucfirst($row['borrower_type'])."</td>
                            <td>".$borrowerID."</td>
                            <td>".$borrowerName."</td>
                            <td>".$deptCourse."</td>
                            <td>".$row['call_no']."</td>
                            <td>".$row['title']."</td>
                            <td>".date('M d, Y', strtotime($row['publish_date']))."</td>
                            <td>".date('M d, Y', strtotime($row['borrow_date']))."</td>
                            <td>".date('M d, Y', strtotime($row['due_date']))."</td>
                            <td>".$status."</td>
                          </tr>
                        ";
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ========== RETURN TAB ========== -->
          <div class="tab-pane" id="return">
            <div class="box">
              <div class="box-header with-border">
                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#returnModal">
                  <i class="fa fa-plus"></i> Return Book
                </button>
              </div>
              <div class="box-body">
                <table id="returnTable" class="table table-bordered table-striped">
                  <thead style="background-color:#006400; color:#FFD700;">
                    <tr>
                      <th>Borrower Type</th>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Book Call No.</th>
                      <th>Book Title</th>
                      <th>Date Borrowed</th>
                      <th>Date Returned</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sql = "SELECT bt.*, b.call_no, b.title, 
                                     s.student_id, s.firstname AS s_fname, s.lastname AS s_lname,
                                     f.faculty_id, f.firstname AS f_fname, f.lastname AS f_lname
                              FROM borrow_transactions bt
                              LEFT JOIN books b ON bt.book_id = b.id
                              LEFT JOIN students s ON (bt.borrower_type='student' AND bt.borrower_id=s.id)
                              LEFT JOIN faculty f ON (bt.borrower_type='faculty' AND bt.borrower_id=f.id)
                              WHERE bt.status = 'returned'
                              ORDER BY bt.return_date DESC";
                      $query = $conn->query($sql);
                      while($row = $query->fetch_assoc()){
                        if($row['borrower_type'] == 'student'){
                          $borrowerID = $row['student_id'];
                          $borrowerName = $row['s_fname'].' '.$row['s_lname'];
                        } else {
                          $borrowerID = $row['faculty_id'];
                          $borrowerName = $row['f_fname'].' '.$row['f_lname'];
                        }
                        echo "
                          <tr>
                            <td>".ucfirst($row['borrower_type'])."</td>
                            <td>".$borrowerID."</td>
                            <td>".$borrowerName."</td>
                            <td>".$row['call_no']."</td>
                            <td>".$row['title']."</td>
                            <td>".date('M d, Y', strtotime($row['borrow_date']))."</td>
                            <td>".date('M d, Y', strtotime($row['return_date']))."</td>
                          </tr>
                        ";
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
      </div>

    </section>
  </div>

  <!-- Include Borrow Modal -->
  <?php include 'includes/borrow_modal.php'; ?>

  <!-- Include Return Modal -->
  <?php include 'includes/return_modal.php'; ?>

</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  $('#borrowTable').DataTable();
  $('#returnTable').DataTable();
});
</script>

</body>
</html>
