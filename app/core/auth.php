<?php
// File untuk fungsi autentikasi
session_start();

// Fungsi untuk mengecek apakah user sudah login
function is_logged_in()
{
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

// Fungsi untuk mengecek apakah user adalah admin
function is_admin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Fungsi untuk memaksa login
function require_login()
{
    if (!is_logged_in()) {
        header('Location: ../../index.php');
        exit();
    }
}

// Fungsi untuk memaksa admin
function require_admin()
{
    require_login();
    if (!is_admin()) {
        header('Location: ../views/dashboard.php');
        exit();
    }
}

// Fungsi untuk logout
function logout()
{
    session_unset();
    session_destroy();
    header('Location: ../../index.php');
    exit();
}

// Fungsi untuk mendapatkan info user yang sedang login
function get_logged_in_user()
{
    if (!is_logged_in()) {
        return null;
    }

    $role_map = [
        1 => 'pegawai',
        2 => 'admin',
        3 => 'operator',
        4 => 'kepala_bps',
        5 => 'pemeriksa'
    ];

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $role_map[$_SESSION['user_role']] ?? 'user'
    ];
}