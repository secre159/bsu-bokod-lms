<?php
include 'includes/session.php';
include 'includes/conn.php';
include 'includes/header.php';
?>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <!-- Enhanced Header -->
    <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h1 style="font-weight: 800; margin: 0; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        <i class="fa fa-bookmark" style="margin-right: 10px;"></i>Subject Management
      </h1>
      <ol class="breadcrumb" style="background-color: transparent; margin: 10px 0 0 0; padding: 0; font-weight: 600;">
      <li style="color: #84ffceff;">HOME</li>
        <li><a href="home.php" style="color: #FFD700;"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li style="color: #84ffceff;">MANAGE</li>
        <li style="color: #84FFCEFF;">Books</li>
        <li class="active" style="color: #ffffffff;">Assign Subject</li>
      </ol>
    </section>

    <!-- Main Content -->
    <section class="content" style="background: linear-gradient(135deg, #f8fff0 0%, #e8f5e8 100%); padding: 20px; border-radius: 0 0 10px 10px; min-height: 80vh;">
      
      <!-- Dismissible Alert Container -->
      <section id="subjectAlertContainer" style="margin-bottom: 20px;">
        <?php
          // Enhanced Error/Success Messages
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

      <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">
        
        <!-- Enhanced Box Header -->
        <div class="box-header with-border" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px; border-bottom: 2px solid #006400;">
          <div class="row">
            <div class="col-md-6">
              <h3 style="font-weight: 700; color: #006400; margin: 0; font-size: 22px;">
                <i class="fa fa-graduation-cap" style="margin-right: 10px;"></i>Subject Organization
              </h3>
              <small style="color: #006400; font-weight: 500;">Manage subjects and assign books for better categorization</small>
            </div>
          </div>
        </div>

        <!-- Filter and Action Buttons -->
        <div class="box-body" style="background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%); padding: 25px;">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="filterSubject" style="font-weight: 600; color: #006400; margin-bottom: 8px;">
                  <i class="fa fa-filter" style="margin-right: 8px;"></i>Select Subject to Show:
                </label>
                <select id="filterSubject" class="form-control" style="border-radius: 6px; border: 1px solid #006400; padding: 10px; font-weight: 500; width: 100%; max-width: 300px; background-color: white; color: #006400;">
                  <option value="0">-- All Subjects --</option>
                  <?php
                    $subject_query = $conn->query("SELECT * FROM subject ORDER BY name ASC");
                    while($s = $subject_query->fetch_assoc()){
                      echo "<option value='{$s['id']}' style='color: #006400; padding: 8px;'>".htmlspecialchars($s['name'])."</option>";
                    }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6 text-right">
              <div class="btn-group" role="group" style="gap: 10px; display: flex; flex-wrap: wrap; justify-content: flex-end;">
                <button class="btn btn-success btn-flat" data-toggle="modal" data-target="#addSubjectModal" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px; box-shadow: 0 2px 4px rgba(0,100,0,0.2);">
                  <i class="fa fa-plus-circle"></i> Add Subject
                </button>
                <button class="btn btn-warning btn-flat" id="editSubjectBtn" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px; box-shadow: 0 2px 4px rgba(0,100,0,0.2);">
                  <i class="fa fa-edit"></i> Edit Subject
                </button>
                <button class="btn btn-danger btn-flat" id="removeSubjectBtn" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px; box-shadow: 0 2px 4px rgba(0,100,0,0.2);">
                  <i class="fa fa-trash"></i> Remove Subject
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Subject Blocks -->
        <div class="box-body" style="background-color: #FFFFFF; padding: 25px;">
          <div id="subjectsContainer">
            <?php
            $subject_query = $conn->query("SELECT * FROM subject ORDER BY name ASC");
            while($subject = $subject_query->fetch_assoc()){
                echo "<div class='subject-block' data-subject='{$subject['id']}' style='margin-bottom: 30px; display: none; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%); box-shadow: 0 2px 8px rgba(0,100,0,0.1);'>
                        <div class='d-flex justify-content-between align-items-center mb-3'>
                          <h4 style='color: #006400; font-weight: 700; margin: 0;'>
                            <i class='fa fa-bookmark' style='margin-right: 10px; color: #FFD700;'></i>
                            ".htmlspecialchars($subject['name'])."
                          </h4>
                          <button class='btn btn-primary btn-flat manageBooks m-3' data-id='{$subject['id']}' data-name='".htmlspecialchars($subject['name'])."' style='background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border: none; border-radius: 5px; font-weight: 600; padding: 6px 15px;margin:10px;'>
                            <i class='fa fa-book'></i> Assign Books
                          </button>
                        </div>";

                $books_query = $conn->prepare("
                  SELECT b.* FROM books b
                  INNER JOIN book_subject_map m ON m.book_id = b.id
                  WHERE m.subject_id = ?
                ");
                $books_query->bind_param("i", $subject['id']);
                $books_query->execute();
                $books_result = $books_query->get_result();

                echo "<div class='table-responsive'>
                        <table class='table table-bordered table-striped table-hover'>
                          <thead style='background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; font-weight: 700;'>
                            <tr>
                              <th style='border-right: 1px solid #228B22;'>📚 Title</th>
                              <th style='border-right: 1px solid #228B22;'>✍️ Author</th>
                              <th style='border-right: 1px solid #228B22;'>🏢 Publisher</th>
                              <th>🛠️ Action</th>
                            </tr>
                          </thead>
                          <tbody>";

                if ($books_result->num_rows > 0) {
                  while($book = $books_result->fetch_assoc()){
                    echo "<tr style='transition: all 0.3s ease;'>
                            <td style='border-right: 1px solid #f0f0f0; font-weight: 500;'>".htmlspecialchars($book['title'])."</td>
                            <td style='border-right: 1px solid #f0f0f0;'>".htmlspecialchars($book['author'])."</td>
                            <td style='border-right: 1px solid #f0f0f0;'>".htmlspecialchars($book['publisher'])."</td>
                            <td class='text-center'>
                              <button class='btn btn-danger btn-sm btn-flat removeBookBtn' 
                                      data-book-id='{$book['id']}' 
                                      data-subject-id='{$subject['id']}' 
                                      data-title='".htmlspecialchars($book['title'])."'
                                      style='background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 4px; font-weight: 600;'>
                                <i class='fa fa-times'></i> Remove
                              </button>
                            </td>
                          </tr>";
                  }
                } else {
                  echo "<tr><td colspan='4' class='text-center text-muted' style='padding: 20px;'>
                          <i class='fa fa-book' style='margin-right: 8px;'></i>No books assigned to this subject
                        </td></tr>";
                }

                echo "</tbody></table></div>
                      </div>";
            }
            ?>
          </div>
        </div>

        <!-- Box Footer -->
        <div class="box-footer" style="background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); padding: 15px; border-top: 1px solid #e0e0e0;">
          <div class="text-muted text-center" style="font-weight: 500;">
            <i class="fa fa-info-circle" style="color: #006400;"></i>
            Total Subjects: <strong><?php echo $subject_query->num_rows; ?></strong> | 
            Use the filter to view specific subjects and their assigned books
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<?php include 'includes/scripts.php'; ?>

<!-- Enhanced Modals -->
<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border: 2px solid #006400; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,100,0,0.3);">
      <form method="POST" action="subject_add.php">
        <div class="modal-header" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; padding: 20px;">
          <h4 class="modal-title" style="font-weight: 700; margin: 0;">
            <i class="fa fa-plus-circle" style="margin-right: 10px;"></i>Add New Subject
          </h4>
          <button type="button" class="close" data-dismiss="modal" style="color: #FFD700; opacity: 0.8;">&times;</button>
        </div>
        <div class="modal-body" style="padding: 25px; background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);">
          <div class="form-group">
            <label style="font-weight: 600; color: #006400;">📚 Subject Name</label>
            <input type="text" name="subject_name" class="form-control" placeholder="Enter subject name..." required style="border-radius: 6px; border: 1px solid #006400; padding: 10px;">
          </div>
        </div>
        <div class="modal-footer" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px;">
          <button type="submit" name="add_subject" class="btn btn-success btn-flat" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
            <i class="fa fa-save"></i> Add Subject
          </button>
          <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
            <i class="fa fa-close"></i> Close
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border: 2px solid #006400; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,100,0,0.3);">
      <form method="POST" action="subject_edit.php">
        <div class="modal-header" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; padding: 20px;">
          <h4 class="modal-title" style="font-weight: 700; margin: 0;">
            <i class="fa fa-edit" style="margin-right: 10px;"></i>Edit Subject
          </h4>
          <button type="button" class="close" data-dismiss="modal" style="color: #FFD700; opacity: 0.8;">&times;</button>
        </div>
        <div class="modal-body" style="padding: 25px; background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);">
          <div class="form-group">
            <label style="font-weight: 600; color: #006400;">📚 Subject Name</label>
            <input type="text" name="subject_name" id="editSubjectName" class="form-control" required style="border-radius: 6px; border: 1px solid #006400; padding: 10px;">
            <input type="hidden" name="subject_id" id="edit_subject_id">
          </div>
        </div>
        <div class="modal-footer" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px;">
          <button type="submit" name="edit_subject" class="btn btn-primary btn-flat" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
            <i class="fa fa-save"></i> Save Changes
          </button>
          <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
            <i class="fa fa-close"></i> Close
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Confirm Remove Subject Modal -->
<div class="modal fade" id="confirmRemoveSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="border: 2px solid #8B0000; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(139,0,0,0.3);">
      <form method="POST" action="subject_remove.php">
        <div class="modal-header" style="background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%); color: #FFD700; padding: 20px;">
          <h4 class="modal-title" style="font-weight: 700; margin: 0;">
            <i class="fa fa-exclamation-triangle" style="margin-right: 10px;"></i>Confirm Remove Subject
          </h4>
          <button type="button" class="close" data-dismiss="modal" style="color: #FFD700; opacity: 0.8;">&times;</button>
        </div>
        <div class="modal-body" style="padding: 25px; background: linear-gradient(135deg, #fff8f8 0%, #ffffff 100%);">
          <p id="removeSubjectMessage" style="font-weight: 500; color: #8B0000;">Are you sure you want to remove this subject?</p>
          <input type="hidden" name="subject_id" id="remove_subject_id">
        </div>
        <div class="modal-footer" style="background: linear-gradient(135deg, #fff0f0 0%, #ffe8e8 100%); padding: 20px;">
          <button type="submit" name="confirm_remove_subject" class="btn btn-danger btn-flat" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
            <i class="fa fa-trash"></i> Yes, Remove
          </button>
          <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
            <i class="fa fa-close"></i> Cancel
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Confirm Remove Book Modal -->
<div class="modal fade" id="confirmRemoveModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content" style="border: 2px solid #8B0000; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(139,0,0,0.3);">
      <div class="modal-header" style="background: linear-gradient(135deg, #8B0000 0%, #A52A2A 100%); color: white; padding: 20px;">
        <h4 class="modal-title" style="font-weight: 700; margin: 0;">
          <i class="fa fa-exclamation-triangle" style="margin-right: 10px;"></i> Confirm Removal
        </h4>
      </div>
      <div class="modal-body" style="padding: 25px; background: linear-gradient(135deg, #fff8f8 0%, #ffffff 100%);">
        <p style="font-weight: 500; color: #8B0000;">Are you sure you want to remove this book from the subject?</p>
        <input type="hidden" id="remove_book_id">
        <input type="hidden" id="remove_subject_id">
      </div>
      <div class="modal-footer" style="background: linear-gradient(135deg, #fff0f0 0%, #ffe8e8 100%); padding: 20px;">
        <button type="button" class="btn btn-secondary btn-flat" data-dismiss="modal" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
          <i class="fa fa-close"></i> Cancel
        </button>
        <button type="button" id="confirmRemoveBookBtn" class="btn btn-danger btn-flat" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
          <i class="fa fa-trash"></i> Remove
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ASSIGN BOOKS MODAL -->
<div class="modal fade" id="assignBooksModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="border: 2px solid #006400; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,100,0,0.3);">
      <div class="modal-header" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; padding: 20px;">
        <button type="button" class="close" data-dismiss="modal" style="color: #FFD700; opacity: 0.8;">&times;</button>
        <h4 class="modal-title" style="font-weight: 700; margin: 0;">
          <i class="fa fa-book" style="margin-right: 10px;"></i> Assign Books to Subject
        </h4>
      </div>
      <div class="modal-body" style="padding: 25px; background: linear-gradient(135deg, #f8fff8 0%, #ffffff 100%);">
        <input type="hidden" id="subject_id">
        <div class="form-group">
          <label style="font-weight: 600; color: #006400; margin-bottom: 8px;">
            <i class="fa fa-search" style="margin-right: 8px;"></i>Search Books
          </label>
          <input type="text" id="bookSearch" class="form-control" placeholder="Search books by call no., title, author, or published date..." style="border-radius: 6px; border: 1px solid #006400; padding: 10px;">
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%);">
              <tr>
                <th style="width:50px; border-right: 1px solid #e0e0e0;">Select</th>
                <th style="border-right: 1px solid #e0e0e0;">📞 Call No.</th>
                <th style="border-right: 1px solid #e0e0e0;">📚 Title</th>
                <th style="border-right: 1px solid #e0e0e0;">✍️ Author</th>
                <th>📅 Published Date</th>
              </tr>
            </thead>
            <tbody id="booksListBody">
              <tr><td colspan="5" class="text-center text-muted" style="padding: 20px;">
                <i class="fa fa-search" style="margin-right: 8px;"></i>Search to display books...
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px;">
        <button type="button" class="btn btn-success btn-flat" id="saveBooksBtn" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
          <i class="fa fa-save"></i> Save Selection
        </button>
        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; border: none; border-radius: 6px; font-weight: 600; padding: 8px 20px;">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  // Add hover effects to table rows
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

  // 🔸 Filter subjects by dropdown
  $('#filterSubject').on('change', function() {
    const selected = $(this).val();
    $('.subject-block').hide();
    if (selected === "0") $('.subject-block').fadeIn();
    else $(`.subject-block[data-subject="${selected}"]`).fadeIn();
  });

  // 🔸 Edit Subject
  $('#editSubjectBtn').on('click', function() {
    const id = $('#filterSubject').val();
    if (id === "0") return alert('Please select a subject to edit.');
    $('#edit_subject_id').val(id);
    $('#editSubjectName').val($('#filterSubject option:selected').text());
    $('#editSubjectModal').modal('show');
  });

  // 🔸 Remove Subject
  $('#removeSubjectBtn').on('click', function() {
    const id = $('#filterSubject').val();
    if (id === "0") return alert('Please select a subject to remove.');
    $('#remove_subject_id').val(id);
    $('#removeSubjectMessage').html(`Are you sure you want to remove <strong>${$('#filterSubject option:selected').text()}</strong>?`);
    $('#confirmRemoveSubjectModal').modal('show');
  });

  // 🔸 Manage Books (Assign)
  $('.manageBooks').on('click', function() {
    $('#subject_id').val($(this).data('id'));
    $('#assignBooksModal').modal('show');
    $('#booksListBody').html('<tr><td colspan="5" class="text-center text-muted" style="padding: 20px;"><i class="fa fa-search" style="margin-right: 8px;"></i>Search to display books...</td></tr>');
  });

  // 🔸 Live Search for Books
  $('#bookSearch').on('keyup', function() {
    const query = $(this).val().trim();
    const subjectId = $('#subject_id').val();

    if (query.length === 0) {
      $('#booksListBody').html('<tr><td colspan="5" class="text-center text-muted" style="padding: 20px;"><i class="fa fa-search" style="margin-right: 8px;"></i>Search to display books...</td></tr>');
      return;
    }

    $.ajax({
      url: 'fetch_books_for_subject.php',
      type: 'POST',
      data: { query: query, subject_id: subjectId },
      success: function(data) {
        $('#booksListBody').html(data);
      }
    });
  });

  // 🔸 Assign Selected Books to Subject
  $('#saveBooksBtn').on('click', function() {
    const subjectId = $('#subject_id').val();
    const selectedBooks = [];

    $('.book-checkbox:checked').each(function() {
      selectedBooks.push($(this).val());
    });

    if (selectedBooks.length === 0) {
      alert('Please select at least one book to assign.');
      return;
    }

    let successCount = 0;

    console.log('Assigning books:', selectedBooks, 'to subject:', subjectId);
    
    selectedBooks.forEach(bookId => {
      $.ajax({
        url: 'assign_book.php',
        type: 'POST',
        data: { book_id: bookId, subject_id: subjectId },
        success: function(response) {
          console.log('Response for book', bookId, ':', response);
          successCount++;
          if (successCount === selectedBooks.length) {
            alert('Books successfully assigned. Response: ' + response);
            $('#assignBooksModal').modal('hide');
            // Full page reload to show updated assignments
            location.reload();
          }
        },
        error: function(xhr, status, error) {
          console.error('Error assigning book', bookId, ':', error);
          alert('Error assigning some books: ' + error);
        }
      });
    });
  });

  // 🔸 Highlight + auto-select identical book copies when checked
  $(document).on('change', '.book-checkbox', function() {
    const callNo = $(this).data('call-no');
    const title = $(this).data('title');
    const published = $(this).data('published');
    const isChecked = $(this).is(':checked');

    $('.book-checkbox').each(function() {
      if (
        $(this).data('call-no') === callNo &&
        $(this).data('title') === title &&
        $(this).data('published') === published
      ) {
        $(this).prop('checked', isChecked);
        const row = $(this).closest('tr');
        if (isChecked) row.addClass('highlighted-row');
        else row.removeClass('highlighted-row');
      }
    });
  });

  // 🔸 Show Confirmation Modal for Book Removal
  $(document).on('click', '.removeBookBtn', function() {
    const bookId = $(this).data('book-id');
    const subjectId = $(this).data('subject-id');
    const title = $(this).data('title');

    // Store in hidden fields
    $('#remove_book_id').val(bookId);
    $('#remove_subject_id').val(subjectId);

    // Update modal message
    $('#confirmRemoveModal .modal-body p').html(
      `Are you sure you want to remove <strong>${title}</strong> (and identical copies) from this subject?`
    );

    $('#confirmRemoveModal').modal('show');
  });

  // 🔸 Confirm Remove (Delete identical copies)
  $('#confirmRemoveBookBtn').on('click', function() {
    const bookId = $('#remove_book_id').val();
    const subjectId = $('#remove_subject_id').val();

    $.ajax({
      url: 'subject_remove_book.php',
      type: 'POST',
      data: { book_id: bookId, subject_id: subjectId },
      success: function(response) {
        $('#confirmRemoveModal').modal('hide');
        alert(response);
        // Full page reload to show updated assignments
        location.reload();
      },
      error: function() {
        alert('Error: Could not remove the selected books.');
      }
    });
  });

});
</script>

<style>
.highlighted-row {
  background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%) !important;
  transition: all 0.3s ease;
  border-left: 3px solid #28a745 !important;
}

.subject-block {
  transition: all 0.3s ease;
}

.subject-block:hover {
  box-shadow: 0 4px 12px rgba(0,100,0,0.15) !important;
  transform: translateY(-2px);
}

/* Improved select dropdown styling */
#filterSubject {
  min-width: 250px;
  background-color: white !important;
  color: #006400 !important;
  border: 1px solid #006400 !important;
  border-radius: 6px !important;
  padding: 10px !important;
  font-weight: 500 !important;
  appearance: menulist !important;
  -webkit-appearance: menulist !important;
  -moz-appearance: menulist !important;
}

#filterSubject option {
  padding: 8px 12px !important;
  color: #006400 !important;
  background-color: white !important;
  font-weight: 500 !important;
}

#filterSubject:focus {
  border-color: #006400 !important;
  box-shadow: 0 0 0 0.2rem rgba(0, 100, 0, 0.25) !important;
  outline: none !important;
}

/* Ensure proper dropdown display */
select.form-control {
  height: auto !important;
  min-height: 42px !important;
}
</style>

</body>
</html>