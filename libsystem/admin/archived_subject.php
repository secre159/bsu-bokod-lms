<?php 
include 'includes/session.php'; 
include 'includes/header.php'; 
include 'includes/conn.php';
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <!-- Header -->
    <section class="content-header" style="background-color: #006400; color: #FFD700; padding: 15px; border-radius: 5px 5px 0 0;">
      <h1 style="font-weight: bold;">📚 Archived Subjects</h1>
    </section>

    <!-- Content -->
    <section class="content" style="background-color: #F8FFF0; padding: 15px; border-radius: 0 0 5px 5px;">
      <?php
        if(isset($_SESSION['error'])){
          echo "<div class='alert alert-danger' style='background-color: #FF6347; color: white; font-weight: bold; padding:10px; border-radius:5px;'>".$_SESSION['error']."</div>";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "<div class='alert alert-success' style='background-color: #32CD32; color: #006400; font-weight: bold; padding:10px; border-radius:5px;'>".$_SESSION['success']."</div>";
          unset($_SESSION['success']);
        }
      ?>

      <div class="box" style="border-top: 3px solid #006400; border-radius: 5px;">
        <div class="box-body" style="background-color: #FFFFFF; border-radius: 0 0 5px 5px;">
          <table class="table table-bordered table-striped">
            <thead style="background-color: #006400; color: #FFD700; font-weight: bold;">
              <th>#</th>
              <th>Title</th>
              <th>Author</th>
              <th>Publisher</th>
              <th>Tools</th>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT * FROM archived_subject ORDER BY id DESC";
                $query = $conn->query($sql);
                if($query->num_rows > 0){
                  $i = 1;
                  while($row = $query->fetch_assoc()){
                    echo "
                      <tr>
                        <td>".$i++."</td>
                        <td>".htmlspecialchars($row['subject_title'] ?? '')."</td>
                        <td>".htmlspecialchars($row['author'] ?? '')."</td>
                        <td>".htmlspecialchars($row['publisher'] ?? '')."</td>
                        <td>
                          <!-- Restore Button -->
                          <form method='POST' action='subject_restore.php' style='display:inline-block; margin-right:5px;'>
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <button class='btn btn-success btn-sm' type='submit' style='background-color:#32CD32; color:white; font-weight:bold;'>
                              <i class='fa fa-undo'></i> Restore
                            </button>
                          </form>

                          <!-- Delete Permanently Button -->
                          <form method='POST' action='subject_delete_permanently.php' style='display:inline-block;' onsubmit=\"return confirm('Are you sure you want to permanently delete this subject?');\">
                            <input type='hidden' name='id' value='".$row['id']."'>
                            <button class='btn btn-danger btn-sm' type='submit' style='background-color:#FF6347; color:white; font-weight:bold;'>
                              <i class='fa fa-trash'></i> Delete
                            </button>
                          </form>
                        </td>
                      </tr>
                    ";
                  }
                } else {
                  echo "<tr><td colspan='5' class='text-center'>No archived subjects found</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </section>
  </div>

  <?php include 'includes/scripts.php'; ?>
</div>
</body>
</html>
