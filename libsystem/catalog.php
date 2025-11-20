<?php 
include 'includes/session.php'; 
include 'includes/header.php'; 
include 'includes/conn.php';

// Function to render badges with "More" toggle
function render_badges_with_more($items, $color='bg-light', $max_visible=3) {
    if(empty($items) || $items == '-') return '-';
    $items = array_map('trim', explode(',', $items));
    $count = count($items);
    $html = '';

    foreach(array_slice($items, 0, $max_visible) as $item){
        $html .= "<span class='badge $color text-dark me-1 mb-1'>".htmlspecialchars($item)."</span>";
    }

    if($count > $max_visible){
        $hiddenItems = array_slice($items, $max_visible);
        $hiddenHtml = '';
        foreach($hiddenItems as $item){
            $hiddenHtml .= "<span class='badge $color text-dark me-1 mb-1'>".htmlspecialchars($item)."</span>";
        }

        $html .= "<span class='badge bg-secondary text-white me-1 mb-1' style='cursor:pointer;' onclick='toggleMoreBadges(this)'>More</span>";
        $html .= "<span class='more-badges d-none'>$hiddenHtml</span>";
    }

    return $html;
}
?>

<style>
body {
  background-color: #f8fafc;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}
.content-wrapper { background: transparent; }
.page-header { text-align: center; margin-bottom: 2rem; padding: 2rem 0 1rem 0; }
.page-header h2 { color: #166534; font-weight: 700; font-size: 2rem; margin-bottom: 0.5rem; }
.page-header p { color: #6b7280; font-size: 1.1rem; }

/* Filter Section */
.filter-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  margin-bottom: 1.5rem;
  border: 1px solid #e5e7eb;
}

.filter-row {
  align-items: end;
}

.filter-group {
  margin-bottom: 1rem;
}

.filter-label {
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.filter-select {
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 0.5rem 0.75rem;
  background: white;
  width: 100%;
  font-size: 0.9rem;
}

.filter-select:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
  outline: none;
}

.filter-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.filter-badge {
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-badge:hover {
  transform: translateY(-1px);
}

.filter-badge.active {
  border: 2px solid #166534;
}

.clear-filters {
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  color: #374151;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}

.clear-filters:hover {
  background: #e5e7eb;
}

.card { background: white; border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; }
.card-header { background: white; border-bottom: 1px solid #e5e7eb; padding: 1.25rem 1.5rem; font-weight: 600; color: #166534; font-size: 1.1rem; }

.badge { font-size: 0.75rem; padding: 0.35em 0.65em; border-radius: 6px; font-weight: 500; }
.badge.bg-light { background-color: #f3f4f6 !important; color: #374151 !important; }
.badge.bg-info { background-color: #dbeafe !important; color: #1e40af !important; }
.badge.bg-success { background-color: #d1fae5 !important; color: #065f46 !important; }
.badge.bg-danger { background-color: #fee2e2 !important; color: #991b1b !important; }
.badge.bg-warning { background-color: #fef9c3 !important; color: #92400e !important; }
.badge.bg-primary { background-color: #dbeafe !important; color: #1e40af !important; }
.badge.bg-secondary { background-color: #e5e7eb !important; color: #374151 !important; }

.book-type-indicator { font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px; background: #f3f4f6; color: #6b7280; }

.badge-container {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

/* Status indicator styles */
.status-available { color: #065f46; }
.status-unavailable { color: #991b1b; }
.status-overdue { color: #92400e; }

/* Active filter highlight */
.active-filters {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
}

.active-filter-item {
  background: #dcfce7;
  color: #166534;
  padding: 0.25rem 0.75rem;
  border-radius: 16px;
  font-size: 0.8rem;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  margin: 0.25rem;
}

.active-filter-item .remove {
  margin-left: 0.5rem;
  cursor: pointer;
  opacity: 0.7;
}

.active-filter-item .remove:hover {
  opacity: 1;
}

@media (max-width: 768px) {
  .table th, .table td { padding: 0.75rem 0.5rem; font-size: 0.85rem; }
  .page-header h2 { font-size: 1.5rem; }
  .filter-row { flex-direction: column; }
  .filter-group { margin-bottom: 1rem; }
}

.search-section {
  transition: all 0.3s ease;
}

.search-section .card {
  border-left: 4px solid #10b981;
}

#bookSearch {
  border-radius: 0 8px 8px 0;
}

#bookSearch:focus {
  box-shadow: none;
  border-color: #d1d5db;
}
</style>

<body class="bg-gray-50">
<div class="wrapper">
  <?php include 'includes/navbar.php'; ?>

  <div class="content-wrapper py-4">
    <div class="container">
      <div class="page-header">
        <h2><i class="fa fa-book-open me-2"></i> Library Catalog</h2>
        <p>Browse and search all available books and digital materials</p>
      </div>

      <!-- Advanced Filters Section -->
      <div class="filter-section">
        <div class="row filter-row g-3">
          <!-- Book Type Filter -->
          <div class="col-md-3">
            <div class="filter-group">
              <div class="filter-label">Book Type</div>
              <select class="filter-select" id="typeFilter">
                <option value="">All Types</option>
                <option value="physical">Physical Books</option>
                <option value="digital">Digital Books</option>
              </select>
            </div>
          </div>

          <!-- Category Filter -->
          <div class="col-md-3">
            <div class="filter-group">
              <div class="filter-label">Category</div>
              <select class="filter-select" id="categoryFilter">
                <option value="">All Categories</option>
                <?php
                $catSql = "SELECT * FROM category ORDER BY name ASC";
                $catQuery = $conn->query($catSql);
                while($cat = $catQuery->fetch_assoc()){
                  echo "<option value='".htmlspecialchars($cat['name'])."'>".htmlspecialchars($cat['name'])."</option>";
                }
                ?>
              </select>
            </div>
          </div>

          <!-- Status Filter -->
          <div class="col-md-3">
            <div class="filter-group">
              <div class="filter-label">Status</div>
              <select class="filter-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
              </select>
            </div>
          </div>

        <!-- Quick Filter Badges -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="filter-label">Quick Filters</div>
            <div class="filter-badges">
              <span class="badge bg-light filter-badge" data-filter='{"type":"physical"}'>
                <i class="fa fa-book me-1"></i>Physical Books
              </span>
              <span class="badge bg-info filter-badge" data-filter='{"type":"digital"}'>
                <i class="fa fa-file-pdf me-1"></i>Digital Books
              </span>
              <span class="badge bg-success filter-badge" data-filter='{"status":"available"}'>
                <i class="fa fa-check me-1"></i>Available Now
              </span>
              <span class="badge bg-danger filter-badge" data-filter='{"status":"unavailable"}'>
                <i class="fa fa-times me-1"></i>Currently Unavailable
              </span>
            </div>
          </div>
        </div>

        <!-- Active Filters Display -->
        <div class="active-filters mt-3" id="activeFilters" style="display: none;">
          <div class="filter-label mb-2">Active Filters:</div>
          <div id="activeFiltersList"></div>
          <button class="clear-filters mt-2" onclick="clearAllFilters()">
            <i class="fa fa-times me-1"></i>Clear All Filters
          </button>
        </div>

      </div>
      <!-- Search Bar Section -->
              <div class="search-section mb-3" id="searchSection" style="display: none;">
                <div class="card">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-md-8">
                        <div class="input-group">
                          <span class="input-group-text bg-light border-end-0">
                            <i class="fa fa-search text-muted"></i>
                          </span>
                          <input type="text" id="bookSearch" class="form-control border-start-0" 
                                placeholder="Search books by title, author, category, topic, or call number...">
                          <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                            <i class="fa fa-times"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <small class="text-muted" id="searchHelp">
                          Search will activate when multiple filters are selected
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


      <!-- Book Table -->
      <div class="card">
        <div class="card-header">
          <i class="fa fa-list me-2"></i> Book & e-Book Collection
          <small class="float-end text-muted">Total: <span id="totalItems">0</span></small>
        </div>

        <div class="card-body p-2">
          <div class="table-responsive">
            <table id="booklist" class="table table-striped table-hover align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Author / Type</th>
                  <th>Call No.</th>
                  <th>Location</th>
                  <th>Categories</th>
                  <th>Topics</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;

                // Physical Books with simplified status (combining overdue and borrowed as unavailable)
                $sqlBooks = "
                    SELECT 
                        b.id, b.title, b.author, b.call_no, b.location,
                        GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories,
                        GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') AS topics,
                        CASE 
                            WHEN bt.status = 'borrowed' THEN 'unavailable'
                            ELSE 'available'
                        END AS book_status,
                        bt.due_date,
                        bt.status as transaction_status
                    FROM books b
                    LEFT JOIN book_category_map bcm ON b.id = bcm.book_id
                    LEFT JOIN category c ON bcm.category_id = c.id
                    LEFT JOIN book_subject_map bsm ON b.id = bsm.book_id
                    LEFT JOIN subject s ON bsm.subject_id = s.id
                    LEFT JOIN borrow_transactions bt ON bt.book_id = b.id AND bt.status = 'borrowed'
                    GROUP BY b.id
                    ORDER BY b.title ASC
                ";

                $queryBooks = $conn->query($sqlBooks);
                while($row = $queryBooks->fetch_assoc()) {
                    $categories = $row['categories'] ?: '-';
                    $topics = $row['topics'] ?: '-';

                    $statusBadge = '';
                    $statusText = '';
                    $statusClass = '';

                    if ($row['book_status'] == 'available') {
                        $statusBadge = "<span class='badge bg-success'>Available</span>";
                        $statusText = 'available';
                        $statusClass = 'status-available';
                    } else {
                        $statusBadge = "<span class='badge bg-danger'>Unavailable</span>";
                        $statusText = 'unavailable';
                        $statusClass = 'status-unavailable';
                        
                        // Add tooltip for unavailable books to show due date if available
                        if ($row['due_date']) {
                            $dueDate = date('M d, Y', strtotime($row['due_date']));
                            $statusBadge = "<span class='badge bg-danger' title='Due: $dueDate'>Unavailable</span>";
                        }
                    }

                    echo "
                    <tr class='$statusClass' data-type='physical' data-status='$statusText' data-category='".htmlspecialchars($row['categories'] ?? '')."' data-location='".htmlspecialchars($row['location'] ?? '')."'>
                        <td class='text-center fw-bold'>{$i}</td>
                        <td class='fw-semibold'>".htmlspecialchars($row['title'] ?? '')."</td>
                        <td>
                          <div class='mb-1'>".htmlspecialchars($row['author'] ?? '')."</div>
                          <small class='book-type-indicator'><i class='fa fa-book me-1'></i>Physical</small>
                        </td>
                        <td class='text-center'>".htmlspecialchars($row['call_no'] ?? '-')."</td>
                        <td>".htmlspecialchars($row['location'] ?? '-')."</td>
                        <td><div class='badge-container'>".render_badges_with_more($categories,'bg-light')."</div></td>
                        <td><div class='badge-container'>".render_badges_with_more($topics,'bg-info')."</div></td>
                        <td class='text-center'>$statusBadge</td>
                        <td class='text-center text-muted'>—</td>
                    </tr>";
                    $i++;
                }

                // Digital Books (Calibre) - Always available
                $sqlCalibre = "SELECT id, title, author, tags, file_path, external_link FROM calibre_books ORDER BY title ASC";
                $queryCalibre = $conn->query($sqlCalibre);
                while($row = $queryCalibre->fetch_assoc()) {
                    $topics = $row['tags'] ?: '-';
                    $status = "<span class='badge bg-success'>-</span>";
                    $location = "Available for download at the library via Calibre";

                    $actions = '';
                    if(!empty($row['file_path'])) {
                        $actions = "
                            <div class='btn-group-vertical btn-group-sm'>
                                <a href='e-books/".htmlspecialchars($row['file_path'])."' target='_blank' class='btn btn-success'>
                                    <i class='fa fa-eye'></i> View
                                </a>
                                <a href='e-books/".htmlspecialchars($row['file_path'])."' download class='btn btn-warning'>
                                    <i class='fa fa-download'></i> Download
                                </a>
                            </div>";
                    } elseif(!empty($row['external_link'])) {
                        $actions = "
                            <a href='".htmlspecialchars($row['external_link'])."' target='_blank' class='btn btn-success btn-sm'>
                                <i class='fa fa-external-link-alt me-1'></i> Access
                            </a>";
                    }

                    echo "
                    <tr class='status-available' data-type='digital' data-status='available' data-category='-' data-location='digital'>
                        <td class='text-center fw-bold'>{$i}</td>
                        <td class='fw-semibold'>".htmlspecialchars($row['title'] ?? '')."</td>
                        <td>
                            <div class='mb-1'>".htmlspecialchars($row['author'] ?? '')."</div>
                            <small class='book-type-indicator'><i class='fa fa-file-pdf me-1'></i>Digital</small>
                        </td>
                        <td class='text-center'>—</td>
                        <td><small class='text-muted'>".htmlspecialchars($location ?? '')."</small></td>
                        <td>-</td>
                        <td><div class='badge-container'>".render_badges_with_more($topics,'bg-info')."</div></td>
                        <td class='text-center'>$status</td>
                        <td class='text-center'>$actions</td>
                    </tr>";
                    $i++;
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
function toggleMoreBadges(element) {
  const moreBadges = element.nextElementSibling;
  moreBadges.classList.toggle('d-none');
  element.textContent = element.textContent === "More" ? "Less" : "More";
}

let activeFilters = {};
let searchTimeout;

$(document).ready(function () {
  const table = $('#booklist').DataTable({
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    responsive: true,
    language: {
      search: "<b>Search:</b>",
      info: "Showing _START_ to _END_ of _TOTAL_ items",
      lengthMenu: "Show _MENU_ items"
    },
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    initComplete: function() {
      $('#totalItems').text(this.api().data().length);
      updateSearchVisibility(); // Initialize search visibility
    },
    drawCallback: function() {
      $('#totalItems').text(this.api().rows({ search: 'applied' }).count());
    }
  });

  // Initialize filter functionality
  initializeFilters(table);

  // Handle quick search redirect from index.php
  const urlParams = new URLSearchParams(window.location.search);
  const searchParam = urlParams.get('search');
  if (searchParam) {
    // Show search section and set value
    $('#searchSection').show();
    $('#bookSearch').val(searchParam);
    table.search(searchParam).draw();
    $('html, body').animate({ scrollTop: $('#booklist').offset().top - 80 }, 600);
  }
});

function initializeFilters(table) {
  // Filter change handlers
  $('#typeFilter, #categoryFilter, #statusFilter, #locationFilter').on('change', function() {
    const filterType = this.id.replace('Filter', '');
    const value = this.value;
    
    if (value) {
      activeFilters[filterType] = value;
    } else {
      delete activeFilters[filterType];
    }
    
    applyFilters(table);
    updateActiveFiltersDisplay();
    updateSearchVisibility(); // Update search visibility on filter change
  });

  // Quick filter badges
  $('.filter-badge').on('click', function() {
    const filter = JSON.parse($(this).attr('data-filter'));
    Object.assign(activeFilters, filter);
    
    // Update dropdowns to match
    if (filter.type) $('#typeFilter').val(filter.type);
    if (filter.status) $('#statusFilter').val(filter.status);
    
    applyFilters(table);
    updateActiveFiltersDisplay();
    updateSearchVisibility(); // Update search visibility on badge click
    
    // Add active class to clicked badge
    $('.filter-badge').removeClass('active');
    $(this).addClass('active');
  });

  // Search functionality with debouncing
  $('#bookSearch').on('input', function() {
    clearTimeout(searchTimeout);
    const searchTerm = $(this).val().trim();
    
    if (searchTerm.length === 0) {
      const table = $('#booklist').DataTable();
      table.search('').draw();
      return;
    }

    // Only search if we should apply search
    if (shouldApplySearch()) {
      searchTimeout = setTimeout(() => {
        const table = $('#booklist').DataTable();
        table.search(searchTerm).draw();
      }, 300); // 300ms debounce
    }
  });
}

// Determine when to show/search
function updateSearchVisibility() {
  const activeFilterCount = Object.keys(activeFilters).length;
  const searchSection = $('#searchSection');
  const searchHelp = $('#searchHelp');
  
  // Show search bar when:
  // - No filters selected (showing all books)
  // - Multiple filters active
  const shouldShow = activeFilterCount === 0 || activeFilterCount >= 2;
  
  if (shouldShow) {
    searchSection.show();
    
    if (activeFilterCount === 0) {
      searchHelp.text('Search all books in the catalog...').removeClass('text-warning');
    } else {
      searchHelp.text('Search is now active across filtered results...').removeClass('text-warning');
    }
  } else {
    searchSection.hide();
    $('#bookSearch').val(''); // Clear search when hidden
    const table = $('#booklist').DataTable();
    table.search('').draw(); // Clear any active search
  }
}

// Smart search application
function shouldApplySearch() {
  const activeFilterCount = Object.keys(activeFilters).length;
  const searchTerm = $('#bookSearch').val().trim();
  
  // Apply search when:
  // 1. We have a search term AND
  // 2. Either no filters or multiple filters are active
  return searchTerm.length > 0 && (activeFilterCount === 0 || activeFilterCount >= 2);
}

function applyFilters(table) {
  $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
    const row = table.row(dataIndex).node();
    
    for (const [filterType, filterValue] of Object.entries(activeFilters)) {
      const rowValue = $(row).attr('data-' + filterType);
      
      if (filterType === 'category') {
        // For categories, check if the row contains the selected category
        if (!rowValue.includes(filterValue)) {
          return false;
        }
      } else if (filterType === 'location' && filterValue === 'digital') {
        // Special handling for digital location
        if ($(row).attr('data-type') !== 'digital') {
          return false;
        }
      } else if (rowValue !== filterValue) {
        return false;
      }
    }
    
    return true;
  });
  
  table.draw();
  $.fn.dataTable.ext.search.pop(); // Remove the filter function after applying
}

function updateActiveFiltersDisplay() {
  const activeFiltersContainer = $('#activeFiltersList');
  activeFiltersContainer.empty();
  
  if (Object.keys(activeFilters).length === 0) {
    $('#activeFilters').hide();
    return;
  }
  
  $('#activeFilters').show();
  
  for (const [filterType, filterValue] of Object.entries(activeFilters)) {
    const filterText = getFilterDisplayText(filterType, filterValue);
    activeFiltersContainer.append(`
      <span class="active-filter-item">
        ${filterText}
        <span class="remove" onclick="removeFilter('${filterType}')">×</span>
      </span>
    `);
  }
}

function getFilterDisplayText(filterType, filterValue) {
  const texts = {
    type: { physical: 'Physical Books', digital: 'Digital Books' },
    status: { available: 'Available', unavailable: 'Unavailable' },
    category: filterValue,
    location: filterValue === 'digital' ? 'Digital Collection' : filterValue
  };
  
  return texts[filterType][filterValue] || filterValue;
}

function removeFilter(filterType) {
  delete activeFilters[filterType];
  $('#' + filterType + 'Filter').val('');
  $('.filter-badge').removeClass('active');
  
  const table = $('#booklist').DataTable();
  applyFilters(table);
  updateActiveFiltersDisplay();
  updateSearchVisibility(); // Update search visibility when removing filters
}

function clearAllFilters() {
  activeFilters = {};
  $('#typeFilter, #categoryFilter, #statusFilter, #locationFilter').val('');
  $('.filter-badge').removeClass('active');
  
  const table = $('#booklist').DataTable();
  applyFilters(table);
  updateActiveFiltersDisplay();
  updateSearchVisibility(); // Update search visibility when clearing all
}

function clearSearch() {
  $('#bookSearch').val('');
  const table = $('#booklist').DataTable();
  table.search('').draw();
}
</script>