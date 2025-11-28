<?php 
include 'includes/session.php';
include 'includes/conn.php';

if(!isset($_SESSION['admin'])){
    header('location: index.php');
    exit();
}

// Handle filters
$filter_search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_category = isset($_GET['category']) ? $_GET['category'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_author = isset($_GET['author']) ? $_GET['author'] : '';

// Build WHERE clause for filters
$where_clause = "WHERE 1=1";
if($filter_search) {
    $where_clause .= " AND (b.title LIKE '%$filter_search%' OR b.author LIKE '%$filter_search%' OR b.isbn LIKE '%$filter_search%' OR b.call_no LIKE '%$filter_search%')";
}
if($filter_category && $filter_category != 'all') {
    $where_clause .= " AND bc.category_id = '$filter_category'";
}
if($filter_status && $filter_status != 'all') {
    if($filter_status == 'available') {
        $where_clause .= " AND b.status = 1 AND b.id NOT IN (SELECT book_id FROM borrow_transactions WHERE status = 'borrowed')";
    } elseif($filter_status == 'borrowed') {
        $where_clause .= " AND b.id IN (SELECT book_id FROM borrow_transactions WHERE status = 'borrowed')";
    }
}
if($filter_author) {
    $where_clause .= " AND b.author LIKE '%$filter_author%'";
}

// Get categories for dropdown
$category_sql = "SELECT * FROM category ORDER BY name";
$category_query = $conn->query($category_sql);

// Get unique authors for dropdown
$author_sql = "SELECT DISTINCT author FROM books WHERE author IS NOT NULL AND author != '' ORDER BY author";
$author_query = $conn->query($author_sql);

// Get inventory statistics
$stats_sql = "SELECT 
    COUNT(*) as total_books,
    SUM(b.copy_number) as total_copies,
    COUNT(DISTINCT b.author) as unique_authors,
    COUNT(DISTINCT bc.category_id) as categories_used
    FROM books b
    LEFT JOIN book_category_map bc ON b.id = bc.book_id
    $where_clause";
$stats_query = $conn->query($stats_sql);
$stats = $stats_query->fetch_assoc();

// Get books data with categories and availability status
$sql = "SELECT b.*, 
        GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as categories,
        CASE 
            WHEN EXISTS (SELECT 1 FROM borrow_transactions bt WHERE bt.book_id = b.id AND bt.status = 'borrowed') THEN 'borrowed'
            ELSE 'available'
        END as availability_status,
        (SELECT COUNT(*) FROM borrow_transactions bt WHERE bt.book_id = b.id AND bt.status = 'borrowed') as times_borrowed
        FROM books b
        LEFT JOIN book_category_map bc ON b.id = bc.book_id
        LEFT JOIN category c ON bc.category_id = c.id
        $where_clause
        GROUP BY b.id
        ORDER BY b.title ASC";
$query = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Inventory - Library System</title>
    <?php include 'includes/header.php'; ?>
    
    <style>
        @media print {
            .no-print, .navbar, .menubar, .content-header, .card-header, .card-footer,
            .btn, .form-control, .alert, .info-box, .breadcrumb, .sidebar,
            .filter-section, .export-group {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            body {
                background: white !important;
                color: black !important;
                font-size: 12pt;
                margin: 0;
                padding: 15px;
            }
            
            .table th, .table td {
                border: 1px solid #000 !important;
                padding: 6px 4px !important;
                font-size: 10pt !important;
            }
            
            .table th {
                background: #f0f0f0 !important;
                color: #000 !important;
            }
        }
        
        .print-only {
            display: none;
        }
        
        .export-btn {
            background: linear-gradient(135deg, #1E90FF 0%, #1C86EE 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            padding: 10px 16px !important;
            transition: all 0.3s ease;
        }
        
        .print-btn {
            background: linear-gradient(135deg, #006400 0%, #004d00 100%) !important;
            color: #FFD700 !important;
            border: none !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
            padding: 10px 16px !important;
            transition: all 0.3s ease;
        }
        
        .filter-section {
            background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
            border: 1px solid #006400;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,100,0,0.1);
        }
        
        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #006400;
        }
        
        .filter-title {
            font-size: 18px;
            font-weight: 700;
            color: #006400;
            margin: 0;
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .export-group {
            display: flex;
            gap: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%);
            border: 1px solid #006400;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,100,0,0.15);
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #006400;
            display: block;
        }
        
        .stat-label {
            font-size: 14px;
            color: #006400;
            font-weight: 600;
        }
        
        .availability-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .available {
            background: linear-gradient(135deg, #32CD32 0%, #228B22 100%);
            color: white;
        }
        
        .borrowed {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #006400;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .filter-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .filter-grid {
                grid-template-columns: 1fr;
            }
            
            .export-group {
                width: 100%;
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body class="hold-transition skin-green sidebar-mini">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">
        <!-- Enhanced Header -->
        <section class="content-header" style="background: linear-gradient(135deg, #006400 0%, #228B22 100%); color: #FFD700; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h1 style="font-weight: 800; margin: 0; font-size: 28px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                <i class="fa fa-book" style="margin-right: 10px;"></i>Book Inventory
            </h1>
            <ol class="breadcrumb" style="background-color: transparent; margin: 10px 0 0 0; padding: 0; font-weight: 600;">
                <li><a href="#" style="color: #FFD700;"><i class="fa fa-dashboard"></i> Home</a></li>
                <li style="color: #FFF;">Books</li>
                <li class="active" style="color: #FFD700;">Inventory</li>
            </ol>
        </section>

        <!-- Main Content -->
        <section class="content" style="background: linear-gradient(135deg, #f8fff0 0%, #e8f5e8 100%); padding: 20px; min-height: 80vh;">
            
            <!-- Alert Messages -->
            <?php
            if(isset($_SESSION['error'])){
                echo "
                <div class='alert alert-danger alert-dismissible no-print' style='background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;'>
                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true' style='color: white; opacity: 0.8;'>&times;</button>
                    <h4><i class='icon fa fa-warning'></i> Alert!</h4>".$_SESSION['error']."
                </div>";
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])){
                echo "
                <div class='alert alert-success alert-dismissible no-print' style='background: linear-gradient(135deg, #32CD32 0%, #28a428 100%); color: #003300; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;'>
                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true' style='color: #003300; opacity: 0.8;'>&times;</button>
                    <h4><i class='icon fa fa-check'></i> Success!</h4>".$_SESSION['success']."
                </div>";
                unset($_SESSION['success']);
            }
            ?>

            <!-- Statistics Cards -->
            <div class="stats-grid no-print">
                <div class="stat-card">
                    <span class="stat-number"><?= $stats['total_books'] ?></span>
                    <span class="stat-label">Total Books</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $stats['total_copies'] ?: 0 ?></span>
                    <span class="stat-label">Total Copies</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $stats['unique_authors'] ?></span>
                    <span class="stat-label">Unique Authors</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $stats['categories_used'] ?></span>
                    <span class="stat-label">Categories Used</span>
                </div>
            </div>

            <!-- Enhanced Filter Section -->
            <div class="filter-section no-print">
                <div class="filter-header">
                    <h3 class="filter-title">
                        <i class="fa fa-filter"></i> Filter Inventory
                    </h3>
                    <div class="export-group">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'word'])); ?>" class="export-btn">
                            <i class="fa fa-file-word-o"></i> Export to Word
                        </a>
                        <button onclick="window.print()" class="print-btn">
                            <i class="fa fa-print"></i> Print Report
                        </button>
                    </div>
                </div>
                
                <form method="GET" class="filter-grid">
                    <div class="form-group">
                        <label style="font-weight: 600; color: #006400; margin-bottom: 5px; display: block;">
                            <i class="fa fa-search"></i> Search
                        </label>
                        <input type="text" name="search" class="form-control" placeholder="Title, Author, ISBN, Call No..." 
                               value="<?= htmlspecialchars($filter_search) ?>" 
                               style="border-radius: 6px; border: 1px solid #006400; padding: 10px;">
                    </div>
                    
                    <div class="form-group">
                        <label style="font-weight: 600; color: #006400; margin-bottom: 5px; display: block;">
                            <i class="fa fa-tags"></i> Category
                        </label>
                        <select name="category" class="form-control" style="border-radius: 6px; border: 1px solid #006400; padding: 10px;">
                            <option value="all">All Categories</option>
                            <?php while($cat = $category_query->fetch_assoc()): ?>
                                <option value="<?= $cat['id'] ?>" <?= $filter_category == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label style="font-weight: 600; color: #006400; margin-bottom: 5px; display: block;">
                            <i class="fa fa-user"></i> Author
                        </label>
                        <select name="author" class="form-control" style="border-radius: 6px; border: 1px solid #006400; padding: 10px;">
                            <option value="">All Authors</option>
                            <?php while($auth = $author_query->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($auth['author']) ?>" <?= $filter_author == $auth['author'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($auth['author']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label style="font-weight: 600; color: #006400; margin-bottom: 5px; display: block;">
                            <i class="fa fa-info-circle"></i> Status
                        </label>
                        <select name="status" class="form-control" style="border-radius: 6px; border: 1px solid #006400; padding: 10px;">
                            <option value="all">All Status</option>
                            <option value="available" <?= $filter_status == 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="borrowed" <?= $filter_status == 'borrowed' ? 'selected' : '' ?>>Borrowed</option>
                        </select>
                    </div>
                </form>
                
                <div class="filter-group">
                    <button type="submit" form="filter-form" class="btn btn-success btn-flat" style="background: linear-gradient(135deg, #32CD32 0%, #228B22 100%); color: white; border: none; border-radius: 6px; font-weight: 600; padding: 10px 20px;">
                        <i class="fa fa-search"></i> Apply Filters
                    </button>
                    <a href="book_inventory.php" class="btn btn-default btn-flat" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); color: #006400; border: 1px solid #006400; border-radius: 6px; font-weight: 600; padding: 10px 20px;">
                        <i class="fa fa-refresh"></i> Clear Filters
                    </a>
                </div>
            </div>

            <!-- Print Header -->
            <div class="print-only print-header">
                <h1>Book Inventory Report</h1>
                <div class="print-info">
                    Library System | Generated on: <?= date('F j, Y \a\t g:i A') ?>
                    | Total Books: <?= $query->num_rows ?>
                </div>
            </div>

            <!-- Main Inventory Table -->
            <div class="card" style="border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,100,0,0.15); overflow: hidden;">
                <!-- Card Header -->
                <div class="card-header no-print" style="background: linear-gradient(135deg, #f0fff0 0%, #e0f7e0 100%); padding: 20px; border-bottom: 2px solid #006400;">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="card-title" style="font-weight: 700; color: #006400; margin: 0; font-size: 22px;">
                                <i class="fa fa-list-alt" style="margin-right: 10px;"></i>Book Inventory List
                            </h3>
                            <small style="color: #006400; font-weight: 500;">Showing <?= $query->num_rows ?> books</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="badge" style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; padding: 8px 16px; border-radius: 20px; font-weight: 600;">
                                <i class="fa fa-database"></i> Total Records: <?= $query->num_rows ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead style="background: linear-gradient(135deg, #006400 0%, #004d00 100%); color: #FFD700; font-weight: 700;">
                                <tr>
                                    <th style="border-right: 1px solid #228B22;">📚 Title</th>
                                    <th style="border-right: 1px solid #228B22;">✍️ Author</th>
                                    <th style="border-right: 1px solid #228B22;">🏷️ ISBN</th>
                                    <th style="border-right: 1px solid #228B22;">📞 Call No.</th>
                                    <th style="border-right: 1px solid #228B22;">📊 Categories</th>
                                    <th style="border-right: 1px solid #228B22;">🏢 Publisher</th>
                                    <th style="border-right: 1px solid #228B22;">📅 Pub Date</th>
                                    <th style="border-right: 1px solid #228B22;">📦 Copies</th>
                                    <th style="border-right: 1px solid #228B22;">📍 Location</th>
                                    <th style="border-right: 1px solid #228B22;">📊 Status</th>
                                    <th class="no-print">🛠️ Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if($query->num_rows > 0):
                                    while($row = $query->fetch_assoc()): 
                                ?>
                                <tr style="transition: all 0.3s ease;">
                                    <td style="border-right: 1px solid #f0f0f0; font-weight: 600; color: #006400;">
                                        <?= htmlspecialchars($row['title']) ?>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0; font-weight: 500;">
                                        <?= htmlspecialchars($row['author']) ?>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0; font-family: monospace;">
                                        <?= htmlspecialchars($row['isbn']) ?>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0; font-weight: 500;">
                                        <?= htmlspecialchars($row['call_no']) ?>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0;">
                                        <small><?= $row['categories'] ? htmlspecialchars($row['categories']) : '<span class="text-muted">No categories</span>' ?></small>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0;">
                                        <?= htmlspecialchars($row['publisher']) ?>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0;">
                                        <?= $row['publish_date'] ? date('M Y', strtotime($row['publish_date'])) : 'N/A' ?>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0; text-align: center; font-weight: 600;">
                                        <?= $row['copy_number'] ?>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0;">
                                        <small><?= htmlspecialchars($row['location']) ?></small>
                                    </td>
                                    <td style="border-right: 1px solid #f0f0f0; text-align: center;">
                                        <span class="availability-badge <?= $row['availability_status'] ?>">
                                            <?= ucfirst($row['availability_status']) ?>
                                        </span>
                                        <?php if($row['times_borrowed'] > 0): ?>
                                            <br><small class="text-muted">(<?= $row['times_borrowed'] ?> borrows)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center no-print">
                                        <div class="action-buttons">
                                            <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm btn-flat" 
                                               style="background: linear-gradient(135deg, #1E90FF 0%, #1C86EE 100%); color: white; border: none; border-radius: 4px; font-weight: 600; padding: 5px 10px;">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="book_details.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm btn-flat"
                                               style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border: none; border-radius: 4px; font-weight: 600; padding: 5px 10px;">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else: 
                                ?>
                                <tr>
                                    <td colspan="11" class="text-center" style="padding: 40px; color: #666;">
                                        <i class="fa fa-book" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                                        <h5 style="color: #666; font-weight: 600;">No books found</h5>
                                        <p class="text-muted">
                                            <?php if($filter_search || $filter_category || $filter_status || $filter_author): ?>
                                                Try adjusting your filters to see more results
                                            <?php else: ?>
                                                No books in inventory yet
                                            <?php endif; ?>
                                        </p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="card-footer no-print" style="background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); padding: 15px; border-top: 1px solid #e0e0e0;">
                    <div class="text-muted text-center" style="font-weight: 500;">
                        <i class="fa fa-info-circle" style="color: #006400;"></i>
                        Book Inventory - Last updated: <?= date('M j, Y g:i A') ?>
                    </div>
                </div>

                <!-- Print Footer -->
                <div class="print-only" style="text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #000; font-size: 9pt; color: #666;">
                    Page <span class="pageNumber"></span> of <span class="totalPages"></span> | 
                    Library System Book Inventory Report | Generated on <?= date('F j, Y \a\t g:i A') ?>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include 'includes/scripts.php'; ?>

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

    // Initialize DataTables
    $('#inventoryTable').DataTable({
        "pageLength": 25,
        "language": {
            "search": "🔍 Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "previous": "◀ Previous",
                "next": "Next ▶"
            }
        }
    });

    // Add page numbers for print
    window.onbeforeprint = function() {
        var totalPages = Math.ceil($('tbody tr').length / 15);
        $('.totalPages').text(totalPages || 1);
    };
});
</script>

</body>
</html>