<?php
session_start();

// Inisialisasi array tugas jika belum ada
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [
        ["id" => 1, "title" => "Belajar PHP", "status" => "belum"],
        ["id" => 2, "title" => "kerjakan tugas UX", "status" => "selesai"],
    ];
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengubah status tugas
    if (isset($_POST['status'])) {
        foreach ($_SESSION['tasks'] as &$task) {
            if (isset($_POST['status'][$task['id']])) {
                // Jika checkbox dicentang, set status ke 'selesai'
                $task['status'] = 'selesai';
            } else {
                // Jika checkbox tidak dicentang, set status ke 'belum'
                $task['status'] = 'belum';
            }
        }
    }

    // Menghapus tugas
    if (isset($_POST['delete'])) {
        $idToDelete = $_POST['delete'];
        $_SESSION['tasks'] = array_filter($_SESSION['tasks'], function($task) use ($idToDelete) {
            return $task['id'] != $idToDelete;
        });
    }

    // Menambahkan tugas baru
    if (!empty($_POST['task_title'])) {
        $newTask = [
            "id" => count($_SESSION['tasks']) + 1, // Menggunakan count untuk ID baru
            "title" => $_POST['task_title'],
            "status" => "belum"
        ];
        $_SESSION['tasks'][] = $newTask; // Menambahkan tugas baru ke dalam session
    }

    // Mengedit tugas
    if (isset($_POST['edit_id']) && !empty($_POST['edit_title'])) {
        $editId = $_POST['edit_id'];
        $editTitle = $_POST['edit_title'];
        foreach ($_SESSION['tasks'] as &$task) {
            if ($task['id'] == $editId) {
                $task['title'] = $editTitle; // Update judul tugas
            }
        }
    }
}

// Fungsi untuk menampilkan daftar tugas
function tampilkanDaftar($tasks) {
    foreach ($tasks as $task) {
        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
        echo "<form method='POST' action='' class='w-100 d-flex justify-content-between align-items-center'>";
        // Checkbox untuk mengubah status
        echo "<input type='checkbox' onchange='this.form.submit()' name='status[{$task['id']}]' " . ($task['status'] === 'selesai' ? 'checked' : '') . ">";
        echo "<span class='ml-2'>{$task['title']} - Status: {$task['status']}</span>";
        echo "<button type='button' class='btn btn-warning btn-sm' onclick='editTask({$task['id']}, \"{$task['title']}\")'>Edit</button>";
        echo "<button type='submit' name='delete' value='{$task['id']}' class='btn btn-danger btn-sm'>Hapus</button>";
        echo "</form>";
        echo "</li>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To-Do List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function editTask(id, title) {
            const newTitle = prompt("Edit Tugas:", title);
            if (newTitle !== null) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'edit_id';
                inputId.value = id;
                form.appendChild(inputId);
                
                const inputTitle = document.createElement('input');
                inputTitle.type = 'hidden';
                inputTitle.name = 'edit_title';
                inputTitle.value = newTitle;
                form.appendChild(inputTitle);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <header class="text-center mb-4">
            <h1>Aplikasi To-Do List</h1>
        </header>

        <form method="POST" action="" class="mb-4">
            <div class="input-group">
                <input type="text" name="task_title" class="form-control" placeholder="Masukkan tugas baru" required>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Tambah Tugas</button>
                </div>
            </div>
        </form>

        <h2>Daftar Tugas</h2>
        <ul class="list-group">
            <?php tampilkanDaftar($_SESSION['tasks']); ?>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
