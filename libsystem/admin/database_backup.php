<?php 
include 'includes/session.php';

if(!isset($_SESSION['admin'])){
    header('location: index.php');
    exit();
}

include 'includes/conn.php';

// Get database credentials from environment or use defaults
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'libsystem4';

// Handle backup download BEFORE any HTML output
if(isset($_POST['backup'])){
    $backup_file = 'backup_' . $db_name . '_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Get all tables
    $tables = array();
    $result = $conn->query("SHOW TABLES");
    while($row = $result->fetch_array()){
        $tables[] = $row[0];
    }
    
    $sql_dump = "-- Database Backup\n";
    $sql_dump .= "-- Database: {$db_name}\n";
    $sql_dump .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
    $sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
    
    // Loop through tables
    foreach($tables as $table){
        // Drop table statement
        $sql_dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
        
        // Create table statement
        $create_table = $conn->query("SHOW CREATE TABLE `{$table}`")->fetch_array();
        $sql_dump .= $create_table[1] . ";\n\n";
        
        // Insert data
        $rows = $conn->query("SELECT * FROM `{$table}`");
        if($rows->num_rows > 0){
            while($row = $rows->fetch_assoc()){
                $sql_dump .= "INSERT INTO `{$table}` VALUES(";
                $values = array();
                foreach($row as $value){
                    if($value === null){
                        $values[] = "NULL";
                    } else {
                        $values[] = "'" . $conn->real_escape_string($value) . "'";
                    }
                }
                $sql_dump .= implode(',', $values) . ");\n";
            }
            $sql_dump .= "\n";
        }
    }
    
    $sql_dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
    // Send file for download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $backup_file . '"');
    header('Content-Length: ' . strlen($sql_dump));
    echo $sql_dump;
    exit();
}

// Include header AFTER backup logic
include 'includes/header.php';

// Handle restore
if(isset($_POST['restore']) && isset($_FILES['sql_file'])){
    $sql_file = $_FILES['sql_file'];
    
    if($sql_file['error'] == 0){
        $file_content = file_get_contents($sql_file['tmp_name']);
        
        // Disable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=0");
        
        // Split by semicolon and execute each statement
        $statements = explode(';', $file_content);
        $success_count = 0;
        $error_count = 0;
        $errors = array();
        
        foreach($statements as $statement){
            $statement = trim($statement);
            if(!empty($statement) && !preg_match('/^--/', $statement)){
                if($conn->query($statement)){
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = $conn->error;
                }
            }
        }
        
        // Re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        
        if($error_count > 0){
            $_SESSION['error'] = "Restore completed with errors: {$error_count} failed queries. First error: " . $errors[0];
        } else {
            $_SESSION['success'] = "Database restored successfully! Executed {$success_count} queries.";
        }
    } else {
        $_SESSION['error'] = "Error uploading file.";
    }
    
    header('location: database_backup.php');
    exit();
}
?>

<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    
    <!-- Enhanced Header -->
    <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
      <h1 style="font-weight: 800; margin: 0; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
        <i class="fa fa-database" style="margin-right: 10px;"></i>Database Backup & Restore
      </h1>
      <ol class="breadcrumb" style="background-color: transparent; margin: 10px 0 0 0; padding: 0; font-weight: 600;">
        <li style="color: #84ffceff;">HOME</li>
        <li><a href="home.php" style="color: #FFD700;"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li style="color: #84ffceff;">SYSTEM</li>
        <li class="active" style="color: #FFF;">Database Backup</li>
      </ol>
    </section>

    <!-- Main Content -->
    <section class="content" style="background: linear-gradient(135deg, #f8fff0 0%, #e8f5e8 100%); padding: 20px; min-height: 80vh;">
      
      <!-- Alert Messages -->
      <?php
      if(isset($_SESSION['error'])){
        echo "
        <div class='alert alert-danger alert-dismissible' style='background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true' style='color: white; opacity: 0.8;'>&times;</button>
          <h4><i class='icon fa fa-warning'></i> Alert!</h4>".$_SESSION['error']."
        </div>";
        unset($_SESSION['error']);
      }
      if(isset($_SESSION['success'])){
        echo "
        <div class='alert alert-success alert-dismissible' style='background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: #003300; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;'>
          <button type='button' class='close' data-dismiss='alert' aria-hidden='true' style='color: #003300; opacity: 0.8;'>&times;</button>
          <h4><i class='icon fa fa-check'></i> Success!</h4>".$_SESSION['success']."
        </div>";
        unset($_SESSION['success']);
      }
      ?>

      <!-- Warning Notice -->
      <div class="alert" style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #006400; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <h4><i class="icon fa fa-exclamation-triangle"></i> Important Notice</h4>
        <p style="margin: 0;">
          <strong>Backup:</strong> Downloads the entire database as an SQL file.<br>
          <strong>Restore:</strong> Replaces ALL current data with the uploaded backup file. This action cannot be undone!
        </p>
      </div>

      <div class="row">
        <!-- Backup Section -->
        <div class="col-md-6">
          <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">
            <div class="box-header with-border" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px; border-bottom: 2px solid #006400;">
              <h3 style="font-weight: 700; color: #006400; margin: 0; font-size: 20px;">
                <i class="fa fa-download" style="margin-right: 10px;"></i>Create Backup
              </h3>
            </div>
            <div class="box-body" style="background-color: #FFFFFF; padding: 25px;">
              <p style="color: #006400; font-weight: 500; margin-bottom: 20px;">
                Download a complete backup of your database. This includes all tables, data, and structure.
              </p>
              
              <!-- Database Info -->
              <div style="background: #f8fff8; padding: 15px; border-radius: 8px; border: 1px solid #006400; margin-bottom: 20px;">
                <h4 style="color: #006400; margin: 0 0 10px 0; font-size: 16px;"><i class="fa fa-info-circle"></i> Database Information</h4>
                <p style="margin: 5px 0;"><strong>Database:</strong> <?php echo htmlspecialchars($db_name); ?></p>
                <p style="margin: 5px 0;"><strong>Host:</strong> <?php echo htmlspecialchars($db_host); ?></p>
                <p style="margin: 5px 0;"><strong>Tables:</strong> 
                  <?php 
                  $table_count = $conn->query("SHOW TABLES")->num_rows;
                  echo $table_count;
                  ?>
                </p>
              </div>

              <form method="POST">
                <button type="submit" name="backup" class="btn btn-block" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); color: white; border: none; border-radius: 6px; font-weight: 600; padding: 12px 20px; font-size: 16px; box-shadow: 0 2px 4px rgba(0,100,0,0.2);">
                  <i class="fa fa-download"></i> Download Backup
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Restore Section -->
        <div class="col-md-6">
          <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">
            <div class="box-header with-border" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px; border-bottom: 2px solid #006400;">
              <h3 style="font-weight: 700; color: #006400; margin: 0; font-size: 20px;">
                <i class="fa fa-upload" style="margin-right: 10px;"></i>Restore Backup
              </h3>
            </div>
            <div class="box-body" style="background-color: #FFFFFF; padding: 25px;">
              <p style="color: #ff6b6b; font-weight: 600; margin-bottom: 20px;">
                <i class="fa fa-exclamation-triangle"></i> Warning: This will replace ALL existing data!
              </p>
              
              <form method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to restore the database? This will REPLACE ALL current data and cannot be undone!');">
                <div class="form-group">
                  <label style="font-weight: 600; color: #006400; margin-bottom: 10px;">
                    <i class="fa fa-file"></i> Select SQL Backup File
                  </label>
                  <input type="file" name="sql_file" accept=".sql" required class="form-control" style="border: 2px solid #006400; border-radius: 6px; padding: 10px;">
                  <small style="color: #666; display: block; margin-top: 5px;">Only .sql files are accepted</small>
                </div>
                
                <button type="submit" name="restore" class="btn btn-block" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 6px; font-weight: 600; padding: 12px 20px; font-size: 16px; box-shadow: 0 2px 4px rgba(255,0,0,0.2);">
                  <i class="fa fa-upload"></i> Restore Database
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Instructions -->
      <div class="box" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden; margin-top: 20px;">
        <div class="box-header with-border" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px; border-bottom: 2px solid #006400;">
          <h3 style="font-weight: 700; color: #006400; margin: 0; font-size: 20px;">
            <i class="fa fa-question-circle" style="margin-right: 10px;"></i>How to Use
          </h3>
        </div>
        <div class="box-body" style="background-color: #FFFFFF; padding: 25px;">
          <div class="row">
            <div class="col-md-6">
              <h4 style="color: #006400; font-weight: 600;"><i class="fa fa-download"></i> Backup Instructions</h4>
              <ol style="color: #333; line-height: 1.8;">
                <li>Click the <strong>"Download Backup"</strong> button</li>
                <li>Save the .sql file to a safe location</li>
                <li>Store backups regularly (daily/weekly recommended)</li>
                <li>Keep multiple backup copies in different locations</li>
              </ol>
            </div>
            <div class="col-md-6">
              <h4 style="color: #006400; font-weight: 600;"><i class="fa fa-upload"></i> Restore Instructions</h4>
              <ol style="color: #333; line-height: 1.8;">
                <li><strong style="color: #ff6b6b;">Create a backup first!</strong></li>
                <li>Click "Choose File" and select your .sql backup</li>
                <li>Confirm the restore action</li>
                <li>Wait for the process to complete</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

</body>
</html>
