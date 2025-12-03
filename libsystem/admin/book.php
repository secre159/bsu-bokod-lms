<?php 
include 'includes/session.php';
include 'includes/conn.php';
include 'includes/header.php'; 

$catid = 0;
$where = '';
if(isset($_GET['category'])){
  $catid = intval($_GET['category']);
  if($catid > 0){
<<<<<<< HEAD
    $where .= " AND m.category_id = $catid";
=======
    // Filter by category using book_category_map table
    $where .= " AND EXISTS (SELECT 1 FROM book_category_map WHERE book_category_map.book_id = books.id AND book_category_map.category_id = $catid)";
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
  }
}

$subjid = 0;
$subject_where = '';
if(isset($_GET['subject'])){
  $subjid = intval($_GET['subject']);
  if($subjid > 0){
<<<<<<< HEAD
    $subject_where .= " AND bsm.subject_id = $subjid";
=======
    // Filter by course subject using book_subject_map table
    $subject_where .= " AND EXISTS (SELECT 1 FROM book_subject_map WHERE book_subject_map.book_id = books.id AND book_subject_map.subject_id = $subjid)";
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
  }
}

// Get counts for statistics
$total_books_sql = "
    SELECT COUNT(DISTINCT books.id) AS total 
    FROM books
    LEFT JOIN book_category_map m ON books.id = m.book_id
<<<<<<< HEAD
    LEFT JOIN book_subject_map bsm ON books.id = bsm.book_id
=======
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
    WHERE 1=1 $where $subject_where
";
$total_books_query = $conn->query($total_books_sql);
$total_books = $total_books_query->fetch_assoc()['total'];

$available_books_sql = "
<<<<<<< HEAD
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
=======
    SELECT COUNT(DISTINCT books.id) AS available 
    FROM books
    LEFT JOIN book_category_map m ON books.id = m.book_id
    LEFT JOIN borrow_transactions bt 
        ON books.id = bt.book_id AND bt.status = 'borrowed'
    WHERE bt.id IS NULL $where $subject_where
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
";
$available_books_query = $conn->query($available_books_sql);
$available_books = $available_books_query->fetch_assoc()['available'];

?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
<<<<<<< HEAD
    <!-- Header Section -->
=======
    <!-- Enhanced Header -->
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
    <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h1 style="font-weight: 800; margin: 0; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        <i class="fa fa-book" style="margin-right: 10px;"></i>Book Collection
      </h1>
      <ol class="breadcrumb" style="background-color: transparent; margin: 10px 0 0 0; padding: 0; font-weight: 600;">
<<<<<<< HEAD
        <li style="color: #84ffceff;">HOME</li>
=======
      <li style="color: #84ffceff;">HOME</li>
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
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
<<<<<<< HEAD
        <!-- Total Books -->
=======
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
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
<<<<<<< HEAD
        <!-- Available Books -->
=======
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
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
<<<<<<< HEAD
        <!-- Borrowed Books -->
=======
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); border: 1px solid #006400; border-radius: 8px; padding: 15px; min-height: 90px;">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border-radius: 6px; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; float: left;">
              <i class="fa fa-users" style="font-size: 24px;"></i>
            </span>
            <div class="info-box-content" style="margin-left: 80px; padding-top: 5px;">
              <span class="info-box-text" style="font-weight: 600; color: #006400; font-size: 14px; display: block;">Borrowed</span>
<<<<<<< HEAD
              <span class="info-box-number" style="color: #006400; font-weight: 700; font-size: 24px; display: block;">
                <?= $total_books - $available_books ?>
              </span>
            </div>
          </div>
        </div>
        <!-- Categories -->
=======
              <span class="info-box-number" style="color: #006400; font-weight: 700; font-size: 24px; display: block;"><?= $total_books - $available_books ?></span>
            </div>
          </div>
        </div>
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
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

<<<<<<< HEAD
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
=======
      <div class="row">
        <div class="col-xs-12">
          <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">
            <!-- Enhanced Card Header -->
            <div class="box-header with-border" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 25px; border-bottom: 2px solid #006400;">
              <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-12">
                  <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <a href="#addnew" data-toggle="modal" class="btn btn-success btn-flat" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); border: none; border-radius: 6px; font-weight: 600; padding: 12px 25px; box-shadow: 0 2px 4px rgba(0,100,0,0.2);">
                      <i class="fa fa-plus-circle" style="margin-right: 8px;"></i> Add New Book
                    </a>
                    <div style="flex: 1; max-width: 400px;">
                      <input type="text" id="quickSearch" class="form-control" placeholder="🔍 Quick search by title, author, ISBN, call no..." style="border-radius: 6px; border: 1px solid #006400; padding: 10px;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                      <span class="badge" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; padding: 8px 16px; border-radius: 20px; font-weight: 600;">
                        <i class="fa fa-filter"></i> Active Filters
                      </span>
                      <?php if($catid > 0 || $subjid > 0): ?>
                        <div style="display: flex; gap: 8px;">
                          <?php if($catid > 0): ?>
                            <span class="badge" style="background: linear-gradient(135deg, #1E90FF 0%, #1C86EE 100%); color: white; padding: 6px 12px; border-radius: 15px; font-size: 12px;">
                              Category Filter
                            </span>
                          <?php endif; ?>
                          <?php if($subjid > 0): ?>
                            <span class="badge" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); color: white; padding: 6px 12px; border-radius: 15px; font-size: 12px;">
                              Subject Filter
                            </span>
                          <?php endif; ?>
                        </div>
                      <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="box-tools pull-right">
                    <div class="filter-section" style="display: flex; gap: 15px; justify-content: flex-end;">
                      <div class="form-group" style="margin: 0;">
                        <label style="color: #006400; font-weight: 700; margin-right: 8px; font-size: 14px;">📚 Category:</label>
                        <select class="form-control input-sm" id="select_category" style="border-radius: 6px; border: 1px solid #006400; font-weight: 500; min-width: 180px;">
                          <option value="0">ALL CATEGORIES</option>
                          <?php
                            $sql = "SELECT * FROM category ORDER BY name ASC";
                            $query = $conn->query($sql);
                            while($catrow = $query->fetch_assoc()){
                              $selected = ($catid == $catrow['id']) ? " selected" : "";
                              echo "<option value='".$catrow['id']."' ".$selected.">".$catrow['name']."</option>";
                            }
                          ?>
                        </select>
                      </div>

                      <div class="form-group" style="margin: 0;">
                        <label style="color: #006400; font-weight: 700; margin-right: 8px; font-size: 14px;">📖 Subject:</label>
                        <select class="form-control input-sm" id="select_subject" style="border-radius: 6px; border: 1px solid #006400; font-weight: 500; min-width: 180px;">
                          <option value="0">ALL SUBJECTS</option>
                          <?php
                            $sql = "SELECT * FROM subject ORDER BY name ASC";
                            $query = $conn->query($sql);
                            while($subjrow = $query->fetch_assoc()){
                              $selected = ($subjid == $subjrow['id']) ? " selected" : "";
                              echo "<option value='".$subjrow['id']."' ".$selected.">".$subjrow['name']."</option>";
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Table -->
            <div class="box-body table-responsive" style="background-color: #FFFFFF;">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; font-weight: 700;">
                  <th style="border-right: 1px solid #228B22;">Categories</th>
                  <th style="border-right: 1px solid #228B22;">Subject</th>
                  <th style="border-right: 1px solid #228B22;">Course Subject</th>
                  <th style="border-right: 1px solid #228B22;">ISBN</th>
                  <th style="border-right: 1px solid #228B22;">Call No.</th>
                  <th style="border-right: 1px solid #228B22;">Title</th>
                  <th style="border-right: 1px solid #228B22;">Author</th>
                  <th style="border-right: 1px solid #228B22;">Publisher</th>
                  <th style="border-right: 1px solid #228B22;">Publish Date</th>
                  <th style="border-right: 1px solid #228B22;">Date Added</th>
                  <th style="border-right: 1px solid #228B22;">Copy No.</th>
                  <th style="border-right: 1px solid #228B22;">No. of Copies</th>
                  <th style="border-right: 1px solid #228B22;">Status</th>
                  <th>Tools</th>
                </thead>
                <tbody>
                  <!-- Data loaded via Ajax -->
                </tbody>
              </table>
            </div>

            <!-- Box Footer -->
            <div class="box-footer" style="background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); padding: 15px; border-top: 1px solid #e0e0e0;">
              <div class="text-muted text-center" style="font-weight: 500;">
                <i class="fa fa-info-circle" style="color: #006400;"></i>
                Filtered by: 
                <strong>
                  <?php 
                    if($catid > 0) echo "Category | ";
                    if($subjid > 0) echo "Subject | ";
                    if($catid == 0 && $subjid == 0) echo "All Books";
                  ?>
                </strong>
              </div>
            </div>
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/book_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<<<<<<< HEAD
<!-- JavaScript for status update buttons -->
<script>
$(function () {
  $('#example1').DataTable({
    responsive: true,
    "language": {
      "search": "🔍 Search books:",
      "lengthMenu": "Show _MENU_ books per page",
      "info": "Showing _START_ to _END_ of _TOTAL_ books",
=======
<script>
// Store table reference globally
var bookTable;

$(function(){
  // Category and Subject filters - reload table instead of page
  $('#select_category, #select_subject').change(function(){
    if(bookTable) {
      bookTable.ajax.reload();
    }
  });

  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'book_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      // ✅ For Edit
      $('#edit_id').val(response.id);
      $('#edit_isbn').val(response.isbn);
      $('#edit_call_no').val(response.call_no);
      $('#edit_title').val(response.title);
      $('#edit_author').val(response.author);
      $('#edit_publisher').val(response.publisher);
      $('#datepicker_edit').val(response.publish_date);
      $('#edit_subject').val(response.subject || '');

      // ✅ For Delete
      $('.bookid').val(response.id);        // 🔥 Fix: sets the hidden input for deletion
      $('#del_book').html(response.title);  // show title in modal

      // Reset all checkboxes
      $('input[name="category[]"]').prop('checked', false);
      $('input[name="course_subject[]"]').prop('checked', false);

      // Check existing categories
      if (response.categories) {
        response.categories.forEach(function(cat){
          $('input[name="category[]"][value="'+cat+'"]').prop('checked', true);
        });
      }

      // Check existing course subjects
      if (response.subjects) {
        response.subjects.forEach(function(subj){
          $('input[name="course_subject[]"][value="'+subj+'"]').prop('checked', true);
        });
      }
    }
  });
}


$(function () {
  // Initialize DataTable with Ajax server-side processing
  bookTable = $('#example1').DataTable({
    responsive: true,
    "processing": true,
    "serverSide": true,
    "ajax": {
      "url": "book_data.php",
      "type": "POST",
      "data": function(d) {
        d.category = $('#select_category').val();
        d.subject = $('#select_subject').val();
      }
    },
    "columns": [
      { "data": "category" },
      { "data": "subject" },
      { "data": "course_subject" },
      { "data": "isbn" },
      { "data": "call_no" },
      { "data": "title" },
      { "data": "author" },
      { "data": "publisher" },
      { "data": "publish_date" },
      { "data": "date_created" },
      { "data": "copy_number" },
      { "data": "num_copies" },
      { "data": "status" },
      { "data": "tools", "orderable": false }
    ],
    "pageLength": 25,
    "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
    "language": {
      "search": "🔍 Filter results:",
      "lengthMenu": "Show _MENU_ books per page",
      "info": "Showing _START_ to _END_ of _TOTAL_ books",
      "infoFiltered": "(filtered from _MAX_ total books)",
      "processing": "<i class='fa fa-spinner fa-spin'></i> Loading books...",
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
      "paginate": {
        "previous": "← Previous",
        "next": "Next →"
      }
<<<<<<< HEAD
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
=======
    },
    "dom": 'lrtip', // Remove default search box since we have custom one
    "order": [[9, "desc"]] // Order by date_created descending
  });

  // Quick search functionality
  $('#quickSearch').on('keyup', function() {
    bookTable.search(this.value).draw();
  });

  // Add hover effects after table draw
  bookTable.on('draw', function() {
    $('tbody tr').hover(
      function() {
        $(this).css('background-color', '#f8fff8');
        $(this).css('transform', 'translateY(-2px)');
        $(this).css('box-shadow', '0 2px 8px rgba(0,100,0,0.1)');
      },
      function() {
        $(this).css('background-color', '');
        $(this).css('transform', 'translateY(0)');
        $(this).css('box-shadow', 'none');
      }
    );
  });
});

function getRow(id){
>>>>>>> 61c0223f54f548fedd5bd0268df79d9be837abd3
</body>
</html>