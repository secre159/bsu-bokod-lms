<!-- ===================== ADD BOOK MODAL ===================== -->
<div class="modal fade" id="addnew">
  <div class="modal-dialog modal-lg custom-modal-width">
    <div class="modal-content book-modal">
      <div class="modal-header modal-header-green">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Add New Book</b></h4>
      </div>

      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="book_add.php">

          <!-- ISBN -->
          <div class="form-group">
            <label class="col-sm-3 control-label">ISBN</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="isbn" placeholder="Enter ISBN">
            </div>
          </div>

          <!-- Call No -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Call No.</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="call_no" placeholder="Enter Call Number">
            </div>
          </div>

          <!-- Title -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Title</label>
            <div class="col-sm-9">
              <textarea class="form-control" name="title" placeholder="Book title..." required></textarea>
            </div>
          </div>

          <!-- Category -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Category</label>
            <div class="col-sm-9 category-box">
              <?php
                $sql = "SELECT * FROM category ORDER BY name ASC";
                $query = $conn->query($sql);
                while($crow = $query->fetch_assoc()){
                  echo "
                    <div class='checkbox'>
                      <label>
                        <input type='checkbox' name='category[]' value='".htmlspecialchars($crow['id'])."'>
                        ".htmlspecialchars($crow['name'])."
                      </label>
                    </div>
                  ";
                }
              ?>
            </div>
          </div>

          <!-- Author -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Author</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="author" placeholder="Enter author name" required>
            </div>
          </div>

          <!-- Publisher -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Publisher</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="publisher" placeholder="Enter publisher">
            </div>
          </div>

          <!-- Number of Copies -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Number of Copies</label>
            <div class="col-sm-9">
              <input type="number" class="form-control" name="num_copies" min="1" value="1" required>
            </div>
          </div>

          <!-- Publish Date -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Publish Date</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="pub_date" placeholder="YYYY-MM-DD or YYYY">
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="form-group text-center" style="margin-top:20px;">
            <button type="submit" name="add" class="btn btn-gold">
              <i class="fa fa-save"></i> Add Book
            </button>
          </div>

        </form>
      </div>

      <div class="modal-footer modal-footer-gray">
        <button type="button" class="btn btn-green" data-dismiss="modal">
          <i class="fa fa-close"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ===================== DELETE BOOK MODAL ===================== -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content book-modal">
      <div class="modal-header modal-header-green">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><b>Deleting...</b></h4>
      </div>
      <div class="modal-body text-center">
        <form class="form-horizontal" method="POST" action="book_delete.php">
          <input type="hidden" class="bookid" name="id">
          <p>Are you sure you want to delete this book?</p>
          <h3 id="del_book" class="bold" style="color:#006400;"></h3>
      </div>
      <div class="modal-footer modal-footer-gray">
        <button type="button" class="btn btn-green" data-dismiss="modal">
          <i class="fa fa-close"></i> Cancel
        </button>
        <button type="submit" class="btn btn-gold" name="delete">
          <i class="fa fa-trash"></i> Delete
        </button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- ===================== EDIT BOOK MODAL ===================== -->
<div class="modal fade" id="edit">
  <div class="modal-dialog modal-lg custom-modal-width">
    <div class="modal-content book-modal">
      <div class="modal-header modal-header-green">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Edit Book</b></h4>
      </div>

      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="book_edit.php">
          <input type="hidden" id="edit_id" name="id">

          <!-- ISBN -->
          <div class="form-group">
            <label class="col-sm-3 control-label">ISBN</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_isbn" name="isbn" placeholder="Enter ISBN">
            </div>
          </div>

          <!-- Call No -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Call No.</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_call_no" name="call_no" placeholder="Enter Call Number">
            </div>
          </div>

          <!-- Title -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Title</label>
            <div class="col-sm-9">
              <textarea class="form-control" id="edit_title" name="title" placeholder="Book title..." required></textarea>
            </div>
          </div>

          <!-- Category -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Category</label>
            <div class="col-sm-9 category-box">
              <?php
                $sql = "SELECT * FROM category ORDER BY name ASC";
                $query = $conn->query($sql);
                while($crow = $query->fetch_assoc()){
                  echo "
                    <div class='checkbox'>
                      <label>
                        <input type='checkbox' name='category[]' value='".htmlspecialchars($crow['id'])."'>
                        ".htmlspecialchars($crow['name'])."
                      </label>
                    </div>
                  ";
                }
              ?>
            </div>
          </div>

          <!-- Author -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Author</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_author" name="author" placeholder="Enter author name">
            </div>
          </div>

          <!-- Publisher -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Publisher</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_publisher" name="publisher" placeholder="Enter publisher">
            </div>
          </div>

          <!-- Publish Date -->
          <div class="form-group">
            <label class="col-sm-3 control-label">Publish Date</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="datepicker_edit" name="pub_date" placeholder="YYYY-MM-DD or YYYY">
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="form-group text-center" style="margin-top:20px;">
            <button type="submit" name="edit" class="btn btn-gold">
              <i class="fa fa-check-square-o"></i> Update Book
            </button>
          </div>

        </form>
      </div>

      <div class="modal-footer modal-footer-gray">
        <button type="button" class="btn btn-green" data-dismiss="modal">
          <i class="fa fa-close"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ===================== MODAL STYLES ===================== -->
<style>
.book-modal {
  background-color: #fff;
  color: #000;
  border-radius: 10px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.25);
  overflow: hidden;
}

.modal-header-green {
  background-color: #006400;
  color: #FFD700;
  border-bottom: 3px solid #FFD700;
}

.modal-header-green .close {
  color: #FFD700;
  opacity: 1;
  font-size: 24px;
}

.modal-header-green .close:hover {
  color: #fff;
}

.category-box {
  max-height: 220px;
  overflow-y: auto;
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 6px;
  background: #fafafa;
}

.custom-modal-width {
  max-width: 900px;
  width: 90%;
}

.modal-body {
  overflow-y: auto;
  max-height: calc(100vh - 180px);
  padding: 20px 30px;
}

.modal-footer-gray {
  background-color: #f5f5f5;
  border-top: 2px solid #006400;
  padding: 15px;
  text-align: right;
}

.btn-green {
  background-color: #006400;
  color: #FFD700;
  border: none;
  transition: 0.3s;
}

.btn-green:hover {
  background-color: #004d00;
  color: #fff;
}

.btn-gold {
  background-color: #FFD700;
  color: #006400;
  border: none;
  transition: 0.3s;
}

.btn-gold:hover {
  background-color: #e6c200;
  color: #fff;
}

.form-control {
  border-radius: 5px;
  border: 1px solid #ccc;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #006400;
  box-shadow: 0 0 5px rgba(0,100,0,0.3);
}

@media (max-width: 768px) {
  .custom-modal-width {
    width: 95%;
    margin: 10px auto;
  }
  .form-group label {
    text-align: left !important;
  }
}
</style>
