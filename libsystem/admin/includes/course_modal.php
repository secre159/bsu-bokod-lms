<!-- Add Student -->
<div class="modal fade" id="addnew">
  <div class="modal-dialog">
    <div class="modal-content" style="border: 2px solid #006400;">
      <div class="modal-header" style="background-color:#006400; color:#FFD700;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b><i class="fa fa-user-plus"></i> Add New Student</b></h4>
      </div>
      <div class="modal-body" style="background-color:white;">
        <form class="form-horizontal" method="POST" action="student_add.php">
          <div class="form-group">
            <label for="student_id" class="col-sm-3 control-label">Student ID</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="student_id" name="student_id" required>
            </div>
          </div>
          <div class="form-group">
            <label for="firstname" class="col-sm-3 control-label">First Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="middlename" class="col-sm-3 control-label">Middle Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="middlename" name="middlename">
            </div>
          </div>
          <div class="form-group">
            <label for="lastname" class="col-sm-3 control-label">Last Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="email" class="col-sm-3 control-label">Email</label>
            <div class="col-sm-9">
              <input type="email" class="form-control" id="email" name="email">
            </div>
          </div>
          <div class="form-group">
            <label for="phone" class="col-sm-3 control-label">Phone</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="phone" name="phone">
            </div>
          </div>
          <div class="form-group">
            <label for="course_id" class="col-sm-3 control-label">Course</label>
            <div class="col-sm-9">
              <select class="form-control" id="course_id" name="course_id">
                <?php
                  $courses = $conn->query("SELECT * FROM course ORDER BY code ASC");
                  while($c = $courses->fetch_assoc()){
                    echo "<option value='".$c['id']."'>".$c['code']." - ".$c['title']."</option>";
                  }
                ?>
              </select>
            </div>
          </div>
      </div>
      <div class="modal-footer" style="background-color:#F0F0F0;">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
          <i class="fa fa-close"></i> Close
        </button>
        <button type="submit" class="btn btn-primary btn-flat" name="add" style="background-color:#006400; color:#FFD700;">
          <i class="fa fa-save"></i> Save
        </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Student -->
<div class="modal fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content" style="border: 2px solid #006400;">
      <div class="modal-header" style="background-color:#006400; color:#FFD700;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b><i class="fa fa-edit"></i> Edit Student</b></h4>
      </div>
      <div class="modal-body" style="background-color:white;">
        <form class="form-horizontal" method="POST" action="student_edit.php">
          <input type="hidden" class="studid" name="id">
          <div class="form-group">
            <label for="edit_firstname" class="col-sm-3 control-label">First Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_firstname" name="firstname">
            </div>
          </div>
          <div class="form-group">
            <label for="edit_middlename" class="col-sm-3 control-label">Middle Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_middlename" name="middlename">
            </div>
          </div>
          <div class="form-group">
            <label for="edit_lastname" class="col-sm-3 control-label">Last Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_lastname" name="lastname">
            </div>
          </div>
          <div class="form-group">
            <label for="edit_email" class="col-sm-3 control-label">Email</label>
            <div class="col-sm-9">
              <input type="email" class="form-control" id="edit_email" name="email">
            </div>
          </div>
          <div class="form-group">
            <label for="edit_phone" class="col-sm-3 control-label">Phone</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_phone" name="phone">
            </div>
          </div>
          <div class="form-group">
            <label for="selcourse" class="col-sm-3 control-label">Course</label>
            <div class="col-sm-9">
              <select class="form-control" id="selcourse" name="course_id"></select>
            </div>
          </div>
      </div>
      <div class="modal-footer" style="background-color:#F0F0F0;">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
          <i class="fa fa-close"></i> Close
        </button>
        <button type="submit" class="btn btn-success btn-flat" name="edit" style="background-color:#006400; color:#FFD700;">
          <i class="fa fa-check-square-o"></i> Update
        </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Student -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content" style="border: 2px solid #006400;">
      <div class="modal-header" style="background-color:#006400; color:#FFD700;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b><i class="fa fa-trash"></i> Delete Student</b></h4>
      </div>
      <div class="modal-body text-center" style="background-color:white;">
        <form class="form-horizontal" method="POST" action="student_delete.php">
          <input type="hidden" class="studid" name="id">
          <p style="font-size:16px;">Are you sure you want to delete this student?</p>
          <h3 class="del_stu" style="color:#B22222;"></h3>
      </div>
      <div class="modal-footer" style="background-color:#F0F0F0;">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
          <i class="fa fa-close"></i> Close
        </button>
        <button type="submit" class="btn btn-danger btn-flat" name="delete" style="background-color:#006400; color:#FFD700;">
          <i class="fa fa-trash"></i> Delete
        </button>
        </form>
      </div>
    </div>
  </div>
</div>
