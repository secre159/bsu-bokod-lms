<?php 
include 'includes/session.php';
include 'includes/conn.php';
include 'includes/header.php'; 

$catid = 0;
$where = '';
if(isset($_GET['category'])){
  $catid = intval($_GET['category']);
  if($catid > 0){
    $where .= " AND m.category_id = $catid";
  }
}

$subjid = 0;
$subject_where = '';
if(isset($_GET['subject'])){
  $subjid = intval($_GET['subject']);
  if($subjid > 0){
    $subject_where .= " AND bsm.subject_id = $subjid";
  }
}

// Get counts for statistics
$total_books_sql = "
    SELECT COUNT(DISTINCT books.id) AS total 
    FROM books
    LEFT JOIN book_category_map m ON books.id = m.book_id
    LEFT JOIN book_subject_map bsm ON books.id = bsm.book_id
    WHERE 1=1 $where $subject_where
";
$total_books_query = $conn->query($total_books_sql);
$total_books = $total_books_query->fetch_assoc()['total'];

$available_books_sql = "
    SELECT SUM(books.num_copies - IFNULL(bt.borrowed_count,0)) AS available
    FROM books
    LEFT JOIN (
        SELECT book_id, COUNT(*) AS borrowed_count
        FROM borrow_transactions
        WHERE status='borrowed'
        GROUP BY book_id
    ) bt ON books.id = bt.book_id
    LEFT JOIN book_category_map m ON books.id = m.book_id
    LEFT JOIN book_subject_map bsm ON books.id = bsm.book_id
    WHERE 1=1 $where $subject_where
";
$available_books_query = $conn->query($available_books_sql);
$available_books = $available_books_query->fetch_assoc()['available'];

?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <!-- Header Section -->
    <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h1 style="font-weight: 800; margin: 0; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        <i class="fa fa-book" style="margin-right: 10px;"></i>Book Collection
      </h1>
      <ol class="breadcrumb" style="background-color: transparent; margin: 10px 0 0 0; padding: 0; font-weight: 600;">
        <li style="color: #84ffceff;">HOME</li>
        <li><a href="home.php" style="color: #FFD700;"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li style="color: #84ffceff;">MANAGE</li>
        <li style="color: #FFD700;">Books</li>
        <li class="active" style="color: #ffffffff;">Book List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content" style="background: linear-gradient(135deg, #f8fff0 0%, #e8f5e8 100%); padding: 20px; border-radius: 0 0 10px 10px; min-height: 80vh;">
      <?php
        if(isset($_SESSION['error'])){
          echo "
          <div class='alert alert-danger alert-dismissible' style='background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
            <button type='button' class='close' data-dismiss='alert' aria-hidden='true' style='color: white; opacity: 0.8;'>&times;</button>
            <h4><i class='icon fa fa-warning'></i> Alert!</h4>
            ".$_SESSION['error']."
          </div>";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
          <div class='alert alert-success alert-dismissible' style='background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: #003300; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
            <button type='button' class='close' data-dismiss='alert' aria-hidden='true' style='color: #003300; opacity: 0.8;'>&times;</button>
            <h4><i class='icon fa fa-check'></i> Success!</h4>
            ".$_SESSION['success']."
          </div>";
          unset($_SESSION['success']);
        }
      ?>

      <!-- Statistics Cards -->
      <div class="row" style="margin-bottom: 20px;">
        <!-- Total Books -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); border: 1px solid #006400; border-radius: 8px; padding: 15px; min-height: 90px;">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border-radius: 6px; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; float: left;">
              <i class="fa fa-book" style="font-size: 24px;"></i>
            </span>
            <div class="info-box-content" style="margin-left: 80px; padding-top: 5px;">
              <span class="info-box-text" style="font-weight: 600; color: #006400; font-size: 14px; display: block;">Total Books</span>
              <span class="info-box-number" style="color: #006400; font-weight: 700; font-size: 24px; display: block;"><?= $total_books ?></span>
            </div>
          </div>
        </div>
        <!-- Available Books -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); border: 1px solid #006400; border-radius: 8px; padding: 15px; min-height: 90px;">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); color: white; border-radius: 6px; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; float: left;">
              <i class="fa fa-check-circle" style="font-size: 24px;"></i>
            </span>
            <div class="info-box-content" style="margin-left: 80px; padding-top: 5px;">
              <span class="info-box-text" style="font-weight: 600; color: #006400; font-size: 14px; display: block;">Available</span>
              <span class="info-box-number" style="color: #006400; font-weight: 700; font-size: 24px; display: block;"><?= $available_books ?></span>
            </div>
          </div>
        </div>
        <!-- Borrowed Books -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); border: 1px solid #006400; border-radius: 8px; padding: 15px; min-height: 90px;">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border-radius: 6px; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; float: left;">
              <i class="fa fa-users" style="font-size: 24px;"></i>
            </span>
            <div class="info-box-content" style="margin-left: 80px; padding-top: 5px;">
              <span class="info-box-text" style="font-weight: 600; color: #006400; font-size: 14px; display: block;">Borrowed</span>
              <span class="info-box-number" style="color: #006400; font-weight: 700; font-size: 24px; display: block;">
                <?= $total_books - $available_books ?>
              </span>
            </div>
          </div>
        </div>
        <!-- Categories -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); border: 1px solid #006400; border-radius: 8px; padding: 15px; min-height: 90px;">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #1E90FF 0%, #1C86EE 100%); color: white; border-radius: 6px; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; float: left;">
              <i class="fa fa-layer-group" style="font-size: 24px;"></i>
            </span>
            <div class="info-box-content" style="margin-left: 80px; padding-top: 5px;">
              <span class="info-box-text" style="font-weight: 600; color: #006400; font-size: 14px; display: block;">Categories</span>
              <span class="info-box-number" style="color: #006400; font-weight: 700; font-size: 24px; display: block;">
                <?php 
                  $cat_sql = "SELECT COUNT(*) as count FROM category";
                  $cat_query = $conn->query($cat_sql);
                  echo $cat_query->fetch_assoc()['count'];
                ?>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Book Table -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">
            <div class="box-body table-responsive" style="background-color: #FFFFFF;">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; font-weight: 700;">
                  <tr>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>ISBN</th>
                    <th>Call No.</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Publisher</th>
                    <th>Publish Date</th>
                    <th>Date Added</th>
                    <th>Copy No.</th>
                    <th>No. of Copies</th>
                    <th>Status</th>
                    <th>Tools</th>
                  </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "
                        SELECT 
                            books.id AS bookid,
                            books.isbn,
                            books.call_no,
                            books.title,
                            books.author,
                            books.publisher,
                            books.publish_date,
                            books.date_created,
                            books.copy_number,
                            books.num_copies,
                            GROUP_CONCAT(DISTINCT subject.name ORDER BY subject.name SEPARATOR ', ') AS subject_list,
                            GROUP_CONCAT(DISTINCT category.name ORDER BY category.name SEPARATOR ', ') AS category_list,
                            IFNULL(bt.borrowed_count,0) AS borrowed_count,
                            IFNULL(books.status, 'available') AS status
                        FROM books
                        LEFT JOIN book_category_map bcm ON books.id = bcm.book_id
                        LEFT JOIN category ON bcm.category_id = category.id
                        LEFT JOIN book_subject_map bsm ON books.id = bsm.book_id
                        LEFT JOIN subject ON bsm.subject_id = subject.id
                        LEFT JOIN (
                            SELECT book_id, COUNT(*) AS borrowed_count
                            FROM borrow_transactions
                            WHERE status='borrowed'
                            GROUP BY book_id
                        ) bt ON books.id = bt.book_id
                        GROUP BY books.id
                        ORDER BY books.id DESC
                    ";
                    $query = $conn->query($sql);

                    while ($row = $query->fetch_assoc()) {
                        $available_copies = $row['num_copies'] - $row['borrowed_count'];

                        // Determine status label based on current status
                        if($row['status'] == 'lost'){
                            $status_display = '<span class="label" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Lost</span>';
                        } elseif($row['status'] == 'damaged'){
                            $status_display = '<span class="label" style="background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Damaged</span>';
                        } else {
                            // default to available
                            if ($available_copies <= 0) {
                                $status_display = '<span class="label" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Unavailable</span>';
                            } elseif($available_copies < $row['num_copies']){
                                $status_display = '<span class="label" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Partially Available</span>';
                            } else {
                                $status_display = '<span class="label" style="background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Available</span>';
                            }
                        }

                        echo "
                        <tr>
                            <td>".htmlspecialchars($row['category_list'] ?: 'Uncategorized')."</td>
                            <td>".htmlspecialchars($row['subject_list'] ?: 'Unassigned')."</td>
                            <td>".htmlspecialchars($row['isbn'])."</td>
                            <td>".htmlspecialchars($row['call_no'])."</td>
                            <td>".htmlspecialchars($row['title'])."</td>
                            <td>".htmlspecialchars($row['author'])."</td>
                            <td>".htmlspecialchars($row['publisher'])."</td>
                            <td>".htmlspecialchars($row['publish_date'])."</td>
                            <td>".htmlspecialchars(date('F d, Y', strtotime($row['date_created'])))."</td>
                            <td>".htmlspecialchars($row['copy_number'])."</td>
                            <td>".htmlspecialchars($row['num_copies'])."</td>
                            <td id='status-".$row['bookid']."'>".$status_display."</td>
                            <td class='text-center'>
                                <div class='btn-group btn-group-sm' role='group'>
                                    <button class='btn btn-warning edit' data-id='".$row['bookid']."'><i class='fa fa-edit'></i></button>
                                    <button class='btn btn-danger delete' data-id='".$row['bookid']."'><i class='fa fa-trash'></i></button>
                                    <button class='btn btn-secondary mark-lost' data-id='".$row['bookid']."'><i class='fa fa-times-circle'></i> Lost</button>
                                    <button class='btn btn-secondary mark-damaged' data-id='".$row['bookid']."'><i class='fa fa-exclamation-triangle'></i> Damaged</button>
                                    <!-- NEW: Available Button -->
                                    <button class='btn btn-primary mark-available' data-id='".$row['bookid']."'><i class='fa fa-check-circle'></i> Available</button>
                                </div>
                            </td>
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
    </section>
  </div>

  <?php include 'includes/book_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<!-- JavaScript for status update buttons -->
<script>
$(function () {
  $('#example1').DataTable({
    responsive: true,
    "language": {
      "search": "🔍 Search books:",
      "lengthMenu": "Show _MENU_ books per page",
      "info": "Showing _START_ to _END_ of _TOTAL_ books",
      "paginate": {
        "previous": "← Previous",
        "next": "Next →"
      }
    }
  });

  // Handle "Lost" button click
  $('.mark-lost').on('click', function() {
    var bookId = $(this).data('id');
    if(confirm('Are you sure you want to mark this book as lost?')) {
      $.post('mark_status.php', {id: bookId, status: 'lost'}, function(response){
        if(response == 'success'){
          $('#status-'+bookId).html('<span class="label" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Lost</span>');
          alert('Book marked as lost.');
        } else {
          alert('Failed to update status.');
        }
      });
    }
  });

  // Handle "Damaged" button click
  $('.mark-damaged').on('click', function() {
    var bookId = $(this).data('id');
    if(confirm('Are you sure you want to mark this book as damaged?')) {
      $.post('mark_status.php', {id: bookId, status: 'damaged'}, function(response){
        if(response == 'success'){
          $('#status-'+bookId).html('<span class="label" style="background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Damaged</span>');
          alert('Book marked as damaged.');
        } else {
          alert('Failed to update status.');
        }
      });
    }
  });

  // Handle "Available" button click
  $(document).on('click', '.mark-available', function() {
    var bookId = $(this).data('id');
    $.ajax({
      url: 'update_status.php',
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({id: bookId}),
      success: function(response) {
        if(response.trim() === 'success'){
          $('#status-'+bookId).html('<span class="label" style="background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Available</span>');
          alert('Book marked as available.');
        } else {
          alert('Failed to update status.');
        }
      }
    });
  });
});
</script>
</body>
</html>