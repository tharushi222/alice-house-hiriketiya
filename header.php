<?php
/**
 * Common Header Template
 * Alice House Hiriketiya
 */
require_once __DIR__ . '/functions.php';
$flash = getFlash();
$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Alice House Hiriketiya' : 'Alice House Hiriketiya – Tropical Boutique Homestay & Accommodation'; ?></title>
    <meta name="description" content="Alice House Hiriketiya – peaceful tropical homestay in Dodampahala, Dikwella, near Hiriketiya beach. Book direct for best rates and authentic Sri Lankan hospitality.">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/garden.jpg">
</head>
<body>

<header class="site-header">
    <div class="container navbar">
        <a href="<?php echo BASE_URL; ?>/index.php" class="brand">
            <div class="brand-logo">AH</div>
            <div class="brand-text">
                <h1>Alice House</h1>
                <span>Hiriketiya Beach</span>
            </div>
        </a>

        <button class="mobile-toggle" id="mobileMenuToggle" aria-label="Toggle Navigation">☰</button>

        <ul class="nav-menu" id="navMenu">
            <li><a href="<?php echo BASE_URL; ?>/index.php" class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/rooms.php" class="nav-link <?php echo ($currentPage == 'rooms.php' || $currentPage == 'room_details.php' || $currentPage == 'book.php') ? 'active' : ''; ?>">Rooms & Suites</a></li>
            <li><a href="<?php echo BASE_URL; ?>/about.php" class="nav-link <?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">About & Location</a></li>
            <li><a href="<?php echo BASE_URL; ?>/contact.php" class="nav-link <?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
            
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <li><a href="<?php echo BASE_URL; ?>/admin/index.php" class="btn btn-sm btn-accent">👑 Admin Panel</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>/my_bookings.php" class="nav-link <?php echo ($currentPage == 'my_bookings.php') ? 'active' : ''; ?>">My Bookings</a></li>
                <?php endif; ?>
                <li><a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-sm btn-outline">Logout (<?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?>)</a></li>
            <?php else: ?>
                <li><a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-sm btn-outline">Sign In</a></li>
                <li><a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-sm btn-primary">Book Now</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<main>
<?php if ($flash): ?>
    <div class="container" style="margin-top: 20px;">
        <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
            <span><?php echo htmlspecialchars($flash['message']); ?></span>
            <button onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; font-size:18px;">&times;</button>
        </div>
    </div>
<?php endif; ?>
