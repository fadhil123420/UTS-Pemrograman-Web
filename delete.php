<?php
require_once 'auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: index.php');
    exit;
}
$stmt = $conn->prepare('DELETE FROM alat_elektromedis WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$deleted = $stmt->affected_rows;
$stmt->close();
header('Location: index.php?status=' . ($deleted > 0 ? 'deleted' : 'notfound'));
exit;
