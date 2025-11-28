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
  <section class="content-header" style="background-color: #006400; color: #FFD700; padding: 15px; border-radius: 5px 5px 0 0;">
    <h1 style="font-weight: bold;">🗂️ Archived Faculty</h1>
  </section>

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
    <div class="box">
      <div class="box-body">
        <table class="table table-bordered table-striped">
          <thead style="background-color: #006400; color: #FFD700; font-weight: bold;">
            <th>ID</th>
            <th>Firstname</th>
            <th>Middlename</th>
            <th>Lastname</th>
            <th>Department</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
          </thead>
          <tbody>
            <?php
              $query = $conn->query("SELECT * FROM faculty WHERE archived = 1 ORDER BY created_on DESC");
              if($query->num_rows > 0){
                while($row = $query->fetch_assoc()){
                  echo "
                  <tr>
                    <td>".htmlspecialchars($row['faculty_id'])."</td>
                    <td>".htmlspecialchars($row['firstname'])."</td>
                    <td>".(!empty($row['middlename']) ? htmlspecialchars($row['middlename']) : '-')."</td>
                    <td>".htmlspecialchars($row['lastname'])."</td>
                    <td>".htmlspecialchars($row['department'])."</td>
                    <td>".(!empty($row['email']) ? htmlspecialchars($row['email']) : '-')."</td>
                    <td>".(!empty($row['phone']) ? htmlspecialchars($row['phone']) : '-')."</td>
                    <td>
                      <form method='POST' action='restore_faculty.php' style='display:inline-block; margin-right:5px;'>
                        <input type='hidden' name='id' value='".$row['id']."'>
                        <button class='btn btn-success btn-sm'>Restore</button>
                      </form>
                      <form method='POST' action='delete_faculty_permanently.php' style='display:inline-block;' onsubmit=\"return confirm('Permanently delete this faculty?');\">
                        <input type='hidden' name='id' value='".$row['id']."'>
                        <button class='btn btn-danger btn-sm'>Delete</button>
                      </form>
                    </td>
                  </tr>
                  ";
                }
              } else {
                echo "<tr><td colspan='8' class='text-center'>No archived faculty found</td></tr>";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
