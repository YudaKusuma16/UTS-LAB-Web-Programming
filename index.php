<?php
session_start();
include 'config.php';  // Koneksi ke database

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    // Redirect ke halaman login jika user belum login
    header('Location: login.php');
    exit;
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Query untuk mengambil email pengguna berdasarkan user_id
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

// Simpan email ke session atau langsung tampilkan di halaman
$_SESSION['email'] = $email;

// Cek apakah ada input search dan status
$search_query = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';

// Query SQL untuk mengambil data board, list, dan tasks berdasarkan user_id, pencarian nama board, dan status tugas
$query = "
    SELECT 
        boards.id AS board_id, boards.name AS board_name,
        lists.id AS list_id, lists.name AS list_name,
        tasks.id AS task_id, tasks.description AS task_description, 
        tasks.status, tasks.due_date, tasks.category
    FROM boards
    LEFT JOIN lists ON boards.id = lists.board_id
    LEFT JOIN tasks ON lists.id = tasks.list_id
    WHERE boards.user_id = ? AND boards.name LIKE ?
";

// Tambahkan kondisi status jika ada filter status
if ($status_filter === 'complete' || $status_filter === 'incomplete') {
    $query .= " AND tasks.status = ?";
}

$query .= " ORDER BY boards.id, lists.id, tasks.id ASC";

$stmt = $conn->prepare($query);
$search_term = "%" . $search_query . "%";

// Bind parameter sesuai kondisi status filter
if ($status_filter === 'complete' || $status_filter === 'incomplete') {
    $stmt->bind_param('iss', $user_id, $search_term, $status_filter);  // Bind user_id, search, dan status
} else {
    $stmt->bind_param('is', $user_id, $search_term);  // Bind user_id dan search
}

$stmt->execute();
$result = $stmt->get_result();

// Proses hasil query ke dalam array boards
$boards = [];
while ($row = $result->fetch_assoc()) {
    $board_id = $row['board_id'];
    $list_id = $row['list_id'];
    $task_id = $row['task_id'];

    if (!isset($boards[$board_id])) {
        $boards[$board_id] = [
            'id' => $board_id,
            'name' => $row['board_name'],
            'lists' => []
        ];
    }

    if ($list_id && !isset($boards[$board_id]['lists'][$list_id])) {
        $boards[$board_id]['lists'][$list_id] = [
            'id' => $list_id,
            'name' => $row['list_name'],
            'tasks' => []
        ];
    }

    if ($task_id) {
        $boards[$board_id]['lists'][$list_id]['tasks'][$task_id] = [
            'id' => $task_id,
            'description' => $row['task_description'],
            'status' => $row['status'],
            'due_date' => $row['due_date'],
            'category' => $row['category']
        ];
    }
}


// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TASKLY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lobster&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


        .logo p {
            font-family: "Lobster", sans-serif;
            font-size: 2.6rem;
            font-weight: bold;
            color: white;
            font-style: normal;
        }
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .dropdown {
            margin-right: 10px; /* Beri jarak antara dropdown status dan tombol */
        }
        .dropdown .btn {
            padding: 10px 12px;
            border-radius: 10px;
            
        }
        .modal-body {
            color: black; /* Ubah warna teks menjadi hitam */
        }
        .modal-header {
            color: black; /* Ubah warna teks menjadi hitam */
        }

        :root {
            --primary: #625BFE;
            --text: #0F192D;
            --text-gray: #5A678C;
            --gray: #c0bcff;
            --error: #E3452F;
        }

        body {
            font-family: "Poppins", sans-serif;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }
        
        .add-board-btn {
            margin-left: 0; /* Hapus margin kiri */
            margin-right: 16px; /* Tambah sedikit jarak dengan elemen navigasi */
            display: flex;
            align-items: center;
            text-decoration: none; /* Ensure no underline */
            color: white; /* Text colour remains white */
            padding: 10px 12px; /* Add padding for a button-like appearance */
            border-radius: 8px; /* Rounded corners */
            transition: background-color 0.3s ease; /* Smooth transition */
        }

        .add-board-btn:hover {
            background-color: #0069D9; /* Change to a different colour on hover */
            color: white; /* Keep text colour white */
            text-decoration: none; /* Remove underline on hover */
        }


        .navigation__group {
            display: flex;
            gap: 16px;
            align-items: center;
            
        }

        .navigation__group > .icon {
            cursor: pointer;
            width: 36px;
            height: 36px;
            transition: all 0.2s ease-in-out;
        }

        .navigation__group > .icon:hover {
            transform: scale(1.1);
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
            text-align: left;
            color: var(--text);
        }

        .profile {
            display: block;
            width: 50px;
            height: 50px;
            cursor: pointer;
            border-radius: 50%;
            border: 3px solid #f7f7f7;
            background-color: white;
            filter: drop-shadow(-20px 0 10px rgba(0, 0, 0, 0.1));
        }

        .profile:hover {
            transform: scale(1.05);
            transition: all 0.2s ease-in-out;
        }

        .email {
            color: var(--text-gray);
        }

        .dropdown__wrapper {
            width: 240px;
            top: 88px;
            right: 16px;
            position: absolute;
            border-radius: 8px;
            border: 1px solid var(--text-gray);
            display: flex;
            flex-direction: column;
            gap: 4px;
            z-index: 999;
            animation: fadeOutAnimation ease-in-out 0.3s forwards;
        }

        .dropdown__wrapper--fade-in {
            animation: fadeInAnimation ease-in-out 0.3s forwards;
        }

        .none {
            display: none;
        }

        .hide {
            opacity: 0;
            visibility: hidden;
            animation: fadeOutAnimation ease-in-out 0.3s forwards;
        }


        @keyframes fadeInAnimation {
            0% {
                opacity: 0;
                visibility: hidden;
                width: 160px;
            }
            100% {
                opacity: 1;
                visibility: visible;
                width: 240px;
            }
        }

        @keyframes fadeOutAnimation {
            0% {
                opacity: 1;
                width: 240px;
                visibility: visible;
            }
            100% {
                opacity: 0;
                width: 160px;
                visibility: hidden;
            }
        }

        .dropdown__group {
            padding: 12px;
        }

        .divider {
            width: 100%;
            padding: 0;
            margin: 0;
            background-color: black;
        }

        .dropdown__wrapper {
            background-color: white; /* Mengatur background menjadi putih */
            border: 1px solid #ccc; /* (Opsional) Menambahkan border untuk memperjelas */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* (Opsional) Menambahkan efek bayangan */
            padding: 10px; /* Memberikan padding di dalam dropdown */
            border-radius: 8px; /* Membuat sudut dropdown menjadi lebih halus */
        }

        nav > ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            gap: 4px;
        }

        nav > ul > li {
            height: 40px;
            display: block;
            flex-direction: row;
            align-items: center;
            gap: 16px;
            padding-left: 8px;
            width: 100%;
        }


        nav > ul > li:hover {
            cursor: pointer;
            text-decoration: underline;
        }

        .board-title {
            font-size: 40px;
        }

        .status-dropdown {
            margin-top: 40px; /* Add some space above the dropdown to avoid overlap */
            padding: 3px;     /* Optional padding for a better appearance */
            border-radius: 5px; /* Rounded corners for better aesthetics */
        }

        .task-buttons {
            display: flex;
            gap: 8px; /* Add some space between the buttons */
            align-items: flex-start; /* Align buttons to the top of the container */
            margin-top: -20px; /* Adjust upward position if necessary */
        }




    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center mb-5">
            <div class="logo">
                <p>Taskly</p>
            </div>

            <div class="d-flex align-items-center">
                <!-- Filter choose -->
                <form action="index.php" method="GET">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-funnel"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item" type="submit" name="status" value="complete">Complete</button>
                            <button class="dropdown-item" type="submit" name="status" value="incomplete">Incomplete</button>
                            <button class="dropdown-item" type="submit" name="status" value="">Display All</button>
                        </div>
                    </div>
                </form>


                <!-- Search Form -->
                <form action="index.php" method="GET" class="search-container d-flex align-items-center" id="searchForm">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" name="search" id="searchInput" placeholder="Search boards..." value="<?= htmlspecialchars($search_query); ?>">
                </form>
            </div>
            <div class="d-flex align-items-center">
                <a href="add_board.php" class="add-board-btn">
                    <i class="bi bi-plus-lg"></i>
                    Add New Board
                </a>

                    <!-- profile -->
                <span class="navigation__group">
                    <img class="profile" src="assets/profile.svg" alt="Joe Doe Picture">
                </span>
                <div class="dropdown__wrapper hide dropdown__wrapper--fade-in none mr-3 mt-2">

                    <div class="dropdown__group">
                        <div class="user-name">
                            <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
                        </div>
                        <div class="email">
                            <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'guest@example.com'; ?>
                        </div>
                    </div>

                    <hr class="divider">
                    <nav>
                        <ul>
                            <li>
                                <a href= "profile.php">
                                <i class="bi bi-person-fill" alt="Profile"> My Profile</i>
                            </li>
                            <li>
                                <a href= "edit_profile.php">
                                <i class="bi bi-pencil-fill" alt="Settings"> Edit Profile</i>
                            </li>
                        </ul>
                        <hr class="divider">
                        <ul>
                            <li style="color: #E3452F;">
                                <a href="logout.php" style="text-decoration: none; color: #E3452F;">
                                    <i class="bi bi-door-open-fill" alt="Log Out"> Log out</i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Boards Container -->
        <div class="row g-4">
            <?php if (!empty($boards)): ?>
                <?php foreach ($boards as $board): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="board-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="board-title"><?= htmlspecialchars($board['name']); ?></h2>
                                <div>
                                    <a href="edit_board.php?board_id=<?= $board['id']; ?>" class="action-btn">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="action-btn delete ml-1" onclick="confirmDelete('delete_board.php?board_id=<?= $board['id']; ?>')">
                                        <i class="bi bi-trash"></i>
                                    </a>

                                </div>
                            </div>

                            <!-- List Section -->
                            <?php foreach ($board['lists'] as $list): ?>
                                <div class="list-container">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="list-title"><?= htmlspecialchars($list['name']); ?></h3>

                                        <!-- Tambahkan Edit dan Delete untuk List -->
                                        <div class="list-buttons d-flex gap-2">
                                            <a href="edit_list.php?list_id=<?= $list['id']; ?>" class="action-btn">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="#" class="action-btn delete" onclick="confirmDelete('delete_list.php?list_id=<?= $list['id']; ?>')">
                                                <i class="bi bi-trash"></i>
                                            </a>

                                        </div>
                                    </div>

                                    <!-- Task Section -->
                                    <?php foreach ($list['tasks'] as $task): ?>
                                        <div class="task-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="task-name"><?= htmlspecialchars($task['description']); ?></span>
                                                <div class="task-meta d-flex gap-2">
                                                    <span><i class="bi bi-clock"></i> <?= date('F j', strtotime($task['due_date'])); ?></span>
                                                    <span><i class="bi bi-tag"></i> <?= htmlspecialchars($task['category']); ?></span>
                                                </div>
                                            </div>

                                            <!-- Status dan tombol edit/delete akan berada di kanan -->
                                            <div class="task-actions d-flex align-items-center">
                                                <!-- Dropdown untuk mengubah status Incomplete/Complete -->
                                                <div class="status-container">
                                                    <form action="update_task_status.php" method="POST">
                                                        <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
                                                        <select name="status" class="status-dropdown" onchange="this.form.submit()">
                                                            <option value="incomplete" <?= $task['status'] === 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
                                                            <option value="complete" <?= $task['status'] === 'complete' ? 'selected' : ''; ?>>Complete</option>
                                                        </select>
                                                    </form>
                                                </div>

                                                <!-- Edit dan Delete muncul hanya saat hover -->
                                                <div class="task-buttons d-flex">
                                                    <a href="edit_task.php?task_id=<?= $task['id']; ?>" class="action-btn edit-btn">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="#" class="action-btn delete-btn" onclick="confirmDelete('delete_task.php?task_id=<?= $task['id']; ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>

                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <a href="add_task.php?list_id=<?= $list['id']; ?>" class="add-btn primary">
                                        <i class="bi bi-plus"></i> Add Task
                                    </a>
                                </div>
                            <?php endforeach; ?>

                            <a href="add_list.php?board_id=<?= $board['id']; ?>" class="add-btn secondary">
                                <i class="bi bi-plus"></i> Add List
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No boards found<?= htmlspecialchars($search_query); ?>.</p>
            <?php endif; ?>
        </div>
    </div>

        <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
            <div class="modal-body">
                Are you sure you want to delete this item? This action cannot be undone.
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        let typingTimer;
        let debounceInterval = 500;

        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');

        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                searchForm.submit();
            }, debounceInterval);
        });

        let deleteUrl = '';

        // Function to open the modal and set the delete URL
        function confirmDelete(url) {
            deleteUrl = url;  // Store the URL for deletion
            var myModal = new bootstrap.Modal(document.getElementById('confirmModal'), {
                keyboard: false
            });
            myModal.show();
        }

        // Add event listener to the delete button in the modal
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            window.location.href = deleteUrl;  // Redirect to the delete URL when confirmed
        });

        const profile = document.querySelector('.profile');
            const dropdown = document.querySelector('.dropdown__wrapper');
            
            profile.addEventListener('click', () => {
                dropdown.classList.remove('none');
                dropdown.classList.toggle('hide');
            })
            
            
        document.addEventListener("click", (event) => {
            const isClickInsideDropdown = dropdown.contains(event.target);
            const isProfileClicked = profile.contains(event.target);
            
            if (!isClickInsideDropdown && !isProfileClicked) {
                dropdown.classList.add('hide');
                dropdown.classList.add('dropdown__wrapper--fade-in');
            }
        });
    </script>

    <script src="./script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>


