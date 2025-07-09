<?php
// Mulai session untuk menyimpan data tugas
session_start();

// Inisialisasi array tugas jika belum ada
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Tambah tugas
if (isset($_POST['add'])) {
    $new_task = [
        'name' => $_POST['task'],
        'status' => false
    ];
    $_SESSION['tasks'][] = $new_task;
}

// Toggle status tugas (selesai/belum)
if (isset($_POST['toggle'])) {
    $index = $_POST['toggle'];
    $_SESSION['tasks'][$index]['status'] = !$_SESSION['tasks'][$index]['status'];
}

// Hapus tugas
if (isset($_POST['delete'])) {
    $index = $_POST['delete'];
    array_splice($_SESSION['tasks'], $index, 1);
}

// Edit tugas
if (isset($_POST['edit']) && isset($_POST['edit_task'])) {
    $index = $_POST['edit'];
    $_SESSION['tasks'][$index]['name'] = $_POST['edit_task'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>
        <form method="POST">
            <input type="text" name="task" placeholder="Tambah tugas baru" required>
            <button type="submit" name="add">Tambah</button>
        </form>
        <ul>
            <?php foreach ($_SESSION['tasks'] as $index => $task): ?>
                <li class="task <?php echo $task['status'] ? 'completed' : ''; ?>">
                    <form method="POST" class="inline-form">
                        <input type="hidden" name="toggle" value="<?php echo $index; ?>">
                        <input type="checkbox" onchange="this.form.submit()" <?php echo $task['status'] ? 'checked' : ''; ?>>
                    </form>
                    <?php if (isset($_POST['edit_mode']) && $_POST['edit_mode'] == $index): ?>
                        <form method="POST" class="inline-form">
                            <input type="text" name="edit_task" value="<?php echo $task['name']; ?>">
                            <input type="hidden" name="edit" value="<?php echo $index; ?>">
                            <button type="submit">Simpan</button>
                        </form>
                    <?php else: ?>
                        <span><?php echo $task['name']; ?></span>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="edit_mode" value="<?php echo $index; ?>">
                            <button type="submit">Edit</button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" class="inline-form">
                        <input type="hidden" name="delete" value="<?php echo $index; ?>">
                        <button type="submit">Hapus</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
