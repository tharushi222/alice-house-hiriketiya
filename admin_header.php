<?php
/**
 * Admin Panel Header
 * Alice House Hiriketiya
 */
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

$adminUser = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($adminTitle) ? htmlspecialchars($adminTitle) . ' | Admin Portal' : 'Admin Portal – Alice House Hiriketiya'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }
        .admin-sidebar {
            background: #13251e;
            color: #fff;
            padding: 24px 0;
            display: flex;
            flex-direction: column;
        }
        .admin-brand {
            padding: 0 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .admin-nav {
            list-style: none;
            padding: 20px 0;
            flex: 1;
        }
        .admin-nav li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #94a3b8;
            font-size: 0.92rem;
            font-weight: 500;
            transition: var(--transition);
        }
        .admin-nav li a:hover, .admin-nav li a.active {
            color: #fff;
            background: rgba(255,255,255,0.08);
            border-left: 4px solid var(--accent);
        }
        .admin-main {
            background: #f8faf9;
            padding: 30px;
            overflow-y: auto;
        }
        .admin-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border);
        }
        @media (max-width: 900px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }
            .admin-sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <div class="brand-logo" style="width: 36px; height: 36px; font-size: 16px;">AH</div>
            <div>
                <strong style="color: #fff; font-size: 1rem; display: block;">Alice House</strong>
                <span style="color: var(--accent); font-size: 0.72rem; letter-spacing: 1px; text-transform: uppercase;">Admin Portal</span>
            </div>
        </div>

        <ul class="admin-nav">
            <li><a href="<?php echo BASE_URL; ?>/admin/index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">📊 Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/rooms.php" class="<?php echo ($currentPage == 'rooms.php' || $currentPage == 'room_edit.php') ? 'active' : ''; ?>">🛏️ Rooms & Suites</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/bookings.php" class="<?php echo ($currentPage == 'bookings.php') ? 'active' : ''; ?>">📅 Bookings</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/users.php" class="<?php echo ($currentPage == 'users.php') ? 'active' : ''; ?>">👥 Guests</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/reviews.php" class="<?php echo ($currentPage == 'reviews.php') ? 'active' : ''; ?>">⭐ Reviews</a></li>
            <li style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                <a href="<?php echo BASE_URL; ?>/index.php" target="_blank">🌐 View Live Website ↗</a>
            </li>
            <li><a href="<?php echo BASE_URL; ?>/logout.php" style="color: #f87171;">🚪 Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <h2 style="color: var(--primary); font-size: 1.6rem; font-weight: 800;"><?php echo htmlspecialchars($adminTitle ?? 'Admin Dashboard'); ?></h2>
                <span style="font-size: 0.85rem; color: var(--text-muted);">Logged in as: <strong><?php echo htmlspecialchars($adminUser['name']); ?></strong> (<?php echo htmlspecialchars($adminUser['email']); ?>)</span>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?php echo BASE_URL; ?>/admin/rooms.php#add-room" class="btn btn-sm btn-primary">+ Add Room</a>
                <a href="<?php echo BASE_URL; ?>/admin/bookings.php" class="btn btn-sm btn-outline">All Bookings</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
                <span><?php echo htmlspecialchars($flash['message']); ?></span>
                <button onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; font-size:18px;">&times;</button>
            </div>
        <?php endif; ?>
