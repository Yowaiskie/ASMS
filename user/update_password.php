<?php
session_start();
require_once '../app/config/config.php';
require_once '../app/helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit;
    }

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        setFlash('password_error', 'New passwords do not match.', 'bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm');
        header('Location: profile.php');
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verify current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user && password_verify($current_password, $user->password)) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($update_stmt->execute([$hashed_password, $_SESSION['user_id']])) {
                setFlash('password_success', 'Password updated successfully!');
                
                // Log action
                // Note: The global logAction uses the App\Core\Database which might need the autoloader.
                // For now, let's just stick to simple success.
            }
        } else {
            setFlash('password_error', 'Current password is incorrect.', 'bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm');
        }

    } catch (PDOException $e) {
        setFlash('password_error', 'Something went wrong. Please try again.', 'bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm');
    }

    header('Location: profile.php');
    exit;
}
