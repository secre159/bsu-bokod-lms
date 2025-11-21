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
    $subject_where .= " AND books.subject_id = $subjid";
  }
}

// Get counts for statistics
$total_books_sql = "
    SELECT COUNT(DISTINCT books.id) AS total 
    FROM books
    LEFT JOIN book_category_map m ON books.id = m.book_id
    WHERE 1=1 $where $subject_where
";
$total_books_query = $conn->query($total_books_sql);
$total_books = $total_books_query->fetch_assoc()['total'];

$available_books_sql = "
    SELECT COUNT(DISTINCT books.id) AS available 
    FROM books
    LEFT JOIN book_category_map m ON books.id = m.book_id
    LEFT JOIN borrow_transactions bt 
        ON books.id = bt.book_id AND bt.status = 'borrowed'
    WHERE bt.id IS NULL $where $subject_where
";
$available_books_query = $conn->query($available_books_sql);
$available_books = $available_books_query->fetch_assoc()['available'];

?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <!-- Enhanced Header -->
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
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); border: 1px solid #006400; border-radius: 8px; padding: 15px; min-height: 90px;">
            <span class="info-box-icon" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border-radius: 6px; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; float: left;">
              <i class="fa fa-users" style="font-size: 24px;"></i>
            </span>
            <div class="info-box-content" style="margin-left: 80px; padding-top: 5px;">
              <span class="info-box-text" style="font-weight: 600; color: #006400; font-size: 14px; display: block;">Borrowed</span>
              <span class="info-box-number" style="color: #006400; font-weight: 700; font-size: 24px; display: block;"><?= $total_books - $available_books ?></span>
            </div>
          </div>
        </div>
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

      <div class="row">
        <div class="col-xs-12">
          <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">
            <!-- Enhanced Card Header -->
            <div class="box-header with-border" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 25px; border-bottom: 2px solid #006400;">
              <div class="row">
                <div class="col-md-6">
                  <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="#addnew" data-toggle="modal" class="btn btn-success btn-flat" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); border: none; border-radius: 6px; font-weight: 600; padding: 12px 25px; box-shadow: 0 2px 4px rgba(0,100,0,0.2);">
                      <i class="fa fa-plus-circle" style="margin-right: 8px;"></i> Add New Book
                    </a>
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
                <div class="col-md-6">
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
                    <?php
                    $today = date('Y-m-d');

                    // Fetch books with their categories, subjects, and latest borrow info
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
                            books.subject AS book_subject,
                            GROUP_CONCAT(DISTINCT subject.name ORDER BY subject.name SEPARATOR ', ') AS subject_list,
                            GROUP_CONCAT(DISTINCT category.name ORDER BY category.name SEPARATOR ', ') AS category_list,
                            bt.status AS borrow_status,
                            bt.due_date AS borrow_due_date
                        FROM books
                        LEFT JOIN book_category_map bcm ON books.id = bcm.book_id
                        LEFT JOIN category ON bcm.category_id = category.id
                        LEFT JOIN book_subject_map bsm ON books.id = bsm.book_id
                        LEFT JOIN subject ON bsm.subject_id = subject.id
                        LEFT JOIN (
                            SELECT * 
                            FROM borrow_transactions
                            WHERE status = 'borrowed'
                        ) bt ON books.id = bt.book_id
                        WHERE 1=1
                        $where
                        $subject_where
                        GROUP BY books.id
                        ORDER BY books.id DESC
                        ";


                    $query = $conn->query($sql);

                    while ($row = $query->fetch_assoc()) {

                        // Determine book status
                        if (!$row['borrow_status']) {
                            $status = '<span class="label" style="background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Available</span>';
                        } elseif ($row['borrow_status'] == 'borrowed' && $today > $row['borrow_due_date']) {
                            $status = '<span class="label" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Overdue</span>';
                        } elseif ($row['borrow_status'] == 'borrowed') {
                            $status = '<span class="label" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Borrowed</span>';
                        } else {
                            $status = '<span class="label" style="background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: white; padding: 5px 12px; border-radius: 15px; font-weight: 600;">Available</span>';
                        }

                        echo "
                        <tr style='transition: all 0.3s ease;'>
                            <td style='border-right: 1px solid #f0f0f0;'><small>".htmlspecialchars($row['category_list'] ?: 'Uncategorized')."</small></td>
                            <td style='border-right: 1px solid #f0f0f0;'><small>".htmlspecialchars($row['book_subject'] ?: '-')."</small></td>
                            <td style='border-right: 1px solid #f0f0f0;'><small>".htmlspecialchars($row['subject_list'] ?: 'Unassigned')."</small></td>
                            <td style='border-right: 1px solid #f0f0f0; font-family: monospace;'><code>".htmlspecialchars($row['isbn'])."</code></td>
                            <td style='border-right: 1px solid #f0f0f0;'>".htmlspecialchars($row['call_no'])."</td>
                            <td style='border-right: 1px solid #f0f0f0; font-weight: 500; color: #006400;'>".htmlspecialchars($row['title'])."</td>
                            <td style='border-right: 1px solid #f0f0f0;'>".htmlspecialchars($row['author'])."</td>
                            <td style='border-right: 1px solid #f0f0f0;'>".htmlspecialchars($row['publisher'])."</td>
                            <td style='border-right: 1px solid #f0f0f0;'><small>".htmlspecialchars($row['publish_date'])."</small></td>
                            <td style='border-right: 1px solid #f0f0f0;'><small>".htmlspecialchars(date('F d, Y', strtotime($row['date_created'])))."</small></td>
                            <td style='border-right: 1px solid #f0f0f0; text-align: center; font-weight: 600;'>".htmlspecialchars($row['copy_number'])."</td>
                            <td style='border-right: 1px solid #f0f0f0; text-align: center; font-weight: 600;'>".htmlspecialchars($row['num_copies'])."</td>
                            <td style='border-right: 1px solid #f0f0f0; text-align: center;'>".$status."</td>
                            <td class='text-center'>
                                <div class='btn-group btn-group-sm' role='group'>
                                    <button class='btn btn-warning edit' data-id='".$row['bookid']."' title='Edit' style='border-radius: 5px; margin-right: 5px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); border: none; color: #006400;'>
                                        <i class='fa fa-edit'>Edit</i>
                                    </button>
                                    <button class='btn btn-danger delete' data-id='".$row['bookid']."' title='Delete' style='border-radius: 5px; background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); border: none;'>
                                        <i class='fa fa-trash'>Delete</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        ";
                    }
                    ?>
                  </tbody>

              </table>
            </div>

            <!-- Box Footer -->
            <div class="box-footer" style="background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); padding: 15px; border-top: 1px solid #e0e0e0;">
              <div class="text-muted text-center" style="font-weight: 500;">
                <i class="fa fa-info-circle" style="color: #006400;"></i>
                Total Books: <strong><?php echo $query->num_rows; ?></strong> | 
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
          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/book_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  $('#select_category').change(function(){
    var value = $(this).val();
    var subj = $('#select_subject').val();
    var url = 'book.php?';
    if(value > 0) url += 'category=' + value + '&';
    if(subj > 0) url += 'subject=' + subj;
    window.location = url;
  });

  $('#select_subject').change(function(){
    var value = $(this).val();
    var cat = $('#select_category').val();
    var url = 'book.php?';
    if(cat > 0) url += 'category=' + cat + '&';
    if(value > 0) url += 'subject=' + value;
    window.location = url;
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


  // Add hover effects to table rows (excluding header)
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
});
</script>

</body>
</html>