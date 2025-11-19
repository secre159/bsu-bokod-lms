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
    <section class="content-header" style="padding:15px;">
      <h1 style="font-weight:bold;">📚 Subjects</h1>
      <ol class="breadcrumb" style="background-color:transparent;">
        <li><a href="#" style="color:#FFD700;"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active" style="color:#FFD700;">Subjects</li>
      </ol>
    </section>

    <section id="subjectAlertContainer" style="padding:15px;">
      <?php
        if(isset($_SESSION['error'])){
          echo "<div class='alert alert-danger'>".$_SESSION['error']."</div>";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
          unset($_SESSION['success']);
        }
      ?>
    </section>

    <section class="content" style="padding:15px;">
      <div class="box" style="border-top:3px solid #006400; background-color:#FFFFFF; padding:15px;">

        <!-- Filter and Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-3" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
          <div>
            <label for="filterSubject">Select Subject to Show:</label>
            <select id="filterSubject" class="form-control" style="width:250px; display:inline-block;">
              <option value="0">-- Select --</option>
              <?php
                $subject_query = $conn->query("SELECT * FROM subject ORDER BY name ASC");
                while($s = $subject_query->fetch_assoc()){
                  echo "<option value='{$s['id']}'>".htmlspecialchars($s['name'])."</option>";
                }
              ?>
            </select>
          </div>

          <div class="d-flex flex-wrap justify-content-start mb-3" style="gap:10px;">
            <button class="btn btn-success btn-flat" data-toggle="modal" data-target="#addSubjectModal">
              <i class="fa fa-plus"></i> Add Subject
            </button>
            <button class="btn btn-primary btn-flat" id="editSubjectBtn">
              <i class="fa fa-edit"></i> Edit Subject
            </button>
            <button class="btn btn-danger btn-flat" id="removeSubjectBtn">
              <i class="fa fa-trash"></i> Remove Subject
            </button>
          </div>
        </div>

        <!-- Subject Blocks -->
        <div id="subjectsContainer">
          <?php
          $subject_query = $conn->query("SELECT * FROM subject ORDER BY name ASC");
          while($subject = $subject_query->fetch_assoc()){
              echo "<div class='subject-block' data-subject='{$subject['id']}' style='margin-bottom:30px; display:none;'>
                      <h4 style='color:#006400;'>".htmlspecialchars($subject['name'])."</h4>";

              $books_query = $conn->prepare("
                SELECT b.* FROM books b
                INNER JOIN book_subject_map m ON m.book_id = b.id
                WHERE m.subject_id = ?
              ");
              $books_query->bind_param("i", $subject['id']);
              $books_query->execute();
              $books_result = $books_query->get_result();

              echo "<div class='table-responsive'>
                      <table class='table table-bordered table-striped'>
                        <thead style='background-color:#006400;color:#FFD700;'>
                          <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Publisher</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>";

              if ($books_result->num_rows > 0) {
                while($book = $books_result->fetch_assoc()){
                  echo "<tr>
                          <td>".htmlspecialchars($book['title'])."</td>
                          <td>".htmlspecialchars($book['author'])."</td>
                          <td>".htmlspecialchars($book['publisher'])."</td>
                          <td>
                            <button class='btn btn-danger btn-sm removeBookBtn' 
                                    data-book-id='{$book['id']}' 
                                    data-subject-id='{$subject['id']}' 
                                    data-title='".htmlspecialchars($book['title'])."'>
                              <i class='fa fa-times'></i> Remove
                            </button>
                          </td>
                        </tr>";
                }
              } else {
                echo "<tr><td colspan='4' class='text-center'>No books assigned to this subject.</td></tr>";
              }

              echo "</tbody></table></div>
                    <button class='btn btn-primary btn-sm manageBooks' data-id='{$subject['id']}' data-name='".htmlspecialchars($subject['name'])."'>
                      <i class='fa fa-edit'></i> Assign Books
                    </button>
                  </div>";
          }
          ?>
        </div>
      </div>
    </section>
  </div>
</div>

<?php include 'includes/scripts.php'; ?>

<!-- ADD, EDIT, REMOVE SUBJECT MODALS HERE (unchanged, as they are fine) -->
<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="subject_add.php">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#006400;color:#FFD700;">
          <h4 class="modal-title">Add New Subject</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Subject Name</label>
            <input type="text" name="subject_name" class="form-control" placeholder="Enter subject name..." required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add_subject" class="btn btn-success">
            <i class="fa fa-save"></i> Add
          </button>
          <button type="button" class="btn btn-default" data-dismiss="modal">
            <i class="fa fa-close"></i> Close
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Subject Modal -->
<div class="modal fade" id="editSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="subject_edit.php">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#006400;color:#FFD700;">
          <h4 class="modal-title">Edit Subject</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Subject Name</label>
            <input type="text" name="subject_name" id="editSubjectName" class="form-control" required>
            <input type="hidden" name="subject_id" id="edit_subject_id">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="edit_subject" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Confirm Remove Subject Modal -->
<div class="modal fade" id="confirmRemoveSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="subject_remove.php">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#8B0000;color:#FFD700;">
          <h4 class="modal-title">Confirm Remove Subject</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p id="removeSubjectMessage">Are you sure you want to remove this subject?</p>
          <input type="hidden" name="subject_id" id="remove_subject_id">
        </div>
        <div class="modal-footer">
          <button type="submit" name="confirm_remove_subject" class="btn btn-danger">Yes, Remove</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Confirm Remove Modal -->
<div class="modal fade" id="confirmRemoveModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="subject_remove_book.php">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#8B0000;color:#FFD700;">
          <h4 class="modal-title">Confirm Removal</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p id="removeMessage">Are you sure you want to remove this book from the subject?</p>
          <input type="hidden" name="subject_id" id="remove_subject_id">
          <input type="hidden" name="book_id" id="remove_book_id">
        </div>
        <div class="modal-footer">
          <button type="submit" name="confirm_remove" class="btn btn-danger">Yes, Remove</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ASSIGN BOOKS MODAL -->
<div class="modal fade" id="assignBooksModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-book"></i> Assign Books to Subject</h4>
      </div>

      <div class="modal-body">
        <input type="hidden" id="subject_id">
        <div class="form-group">
          <input type="text" id="bookSearch" class="form-control" placeholder="Search books by call no., title, author, or published date...">
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr class="bg-gray">
                <th style="width:50px;">Select</th>
                <th>Call No.</th>
                <th>Title</th>
                <th>Author</th>
                <th>Published Date</th>
              </tr>
            </thead>
            <tbody id="booksListBody">
              <tr><td colspan="5" class="text-center text-muted">Search to display books...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <!-- Save Button -->
        <button type="button" class="btn btn-success" id="saveBooksBtn">
          <i class="fa fa-save"></i> Save Selection
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>


<script>
$(document).ready(function() {
  // 🔸 Filter subjects
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

  // 🔸 Manage Books
  $('.manageBooks').on('click', function() {
    $('#subject_id').val($(this).data('id'));
    $('#assignBooksModal').modal('show');
    $('#booksListBody').html('<tr><td colspan="5" class="text-center">Search to display books...</td></tr>');
  });

  // 🔸 Live search books
  $('#bookSearch').on('keyup', function() {
    const query = $(this).val().trim();
    const subjectId = $('#subject_id').val();
    if (query.length === 0) {
      $('#booksListBody').html('<tr><td colspan="5" class="text-center">Search to display books...</td></tr>');
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

  // 🔸 Assign / Remove Book instantly
  $(document).on('change', '.book-checkbox', function() {
    const bookId = $(this).val();
    const subjectId = $('#subject_id').val();
    const isChecked = $(this).is(':checked');
    $.ajax({
      url: isChecked ? 'assign_book.php' : 'remove_book_assignment.php',
      type: 'POST',
      data: { book_id: bookId, subject_id: subjectId }
    });
  });

  // 🔸 Remove book from subject (from subject table)
  $(document).on('click', '.removeBookBtn', function() {
    const bookId = $(this).data('book-id');
    const subjectId = $(this).data('subject-id');
    $('#remove_book_id').val(bookId);
    $('#remove_subject_id').val(subjectId);
    $('#confirmRemoveModal').modal('show');
  });
});
</script>

</body>
</html>
