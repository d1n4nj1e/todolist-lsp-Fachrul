<?php
session_start();

$defaultTasks = [
  ["id" => 1, "title" => "Belajar PHP", "status" => "belum"],
  ["id" => 2, "title" => "kerjakan tugas UX", "status" => "selesai"],
];

if (!isset($_SESSION['tasks']) || empty($_SESSION['tasks'])) {
  $_SESSION['tasks'] = $defaultTasks;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // AJAX: toggle status
  if (isset($_POST['toggle_id'])) {
    header('Content-Type: application/json');
    foreach ($_SESSION['tasks'] as &$task) {
      if ($task['id'] == $_POST['toggle_id']) {
        $task['status'] = $task['status'] === 'selesai' ? 'belum' : 'selesai';
        echo json_encode(['status' => $task['status']]);
        exit;
      }
    }
    echo json_encode(['error' => 'Task not found']);
    exit;
  }

  // Tambah tugas
  if (!empty($_POST['task_title'])) {
    $newTask = [
      "id" => time(),
      "title" => $_POST['task_title'],
      "status" => "belum"
    ];
    $_SESSION['tasks'][] = $newTask;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }

  // Edit tugas
  if (isset($_POST['edit_id']) && isset($_POST['edit_title'])) {
    foreach ($_SESSION['tasks'] as &$task) {
      if ($task['id'] == $_POST['edit_id']) {
        $task['title'] = $_POST['edit_title'];
        break;
      }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }

  // Hapus tugas
  if (isset($_POST['delete'])) {
    $idToDelete = $_POST['delete'];
    $_SESSION['tasks'] = array_values(array_filter($_SESSION['tasks'], function ($task) use ($idToDelete) {
      return (string)$task['id'] !== (string)$idToDelete;
    }));
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
  }
}

function tampilkanDaftar($tasks)
{
  foreach ($tasks as $task) {
    if (!isset($task['title']) || trim($task['title']) === '') continue;
    $checked = $task['status'] === 'selesai' ? 'checked' : '';
    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
    echo "<div class='form-check'>";
    echo "<input class='form-check-input status-checkbox' type='checkbox' data-id='{$task['id']}' {$checked}>";
    echo "<label class='form-check-label ml-2'>{$task['title']} - Status: <span class='task-status'>{$task['status']}</span></label>";
    echo "</div>";
    echo "<form method='POST' action='' class='d-flex align-items-center ml-3'>";
    echo "<input type='hidden' name='edit_id' value='{$task['id']}'>";
    echo "<input type='text' name='edit_title' placeholder='Edit tugas' class='form-control mx-2' style='width: 200px;'>";
    echo "<button type='submit' class='btn btn-warning btn-sm mr-1'>Edit</button>";
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

  <script>
    document.querySelectorAll('.status-checkbox').forEach(cb => {
      cb.addEventListener('change', function() {
        fetch('', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
              toggle_id: this.dataset.id
            })
          })
          .then(res => res.json())
          .then(data => {
            if (data.status) {
              this.closest('li').querySelector('.task-status').textContent = data.status;
            }
          });
      });
    });
  </script>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
