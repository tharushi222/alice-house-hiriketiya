<?php
/**
 * Core Helper Functions
 * Alice House Hiriketiya
 */

require_once __DIR__ . '/db.php';

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize string input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Set flash alert message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if logged-in user is admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current authenticated user details
 */
function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'guest'
    ];
}

/**
 * Format price in USD and Sri Lankan Rupees (LKR)
 * Conversion rate: 1 USD ≈ 310 LKR
 */
function formatPrice($usd, $showLkr = true) {
    $usdFormatted = '$' . number_format($usd, 2);
    if ($showLkr) {
        $lkr = number_format($usd * 310);
        return $usdFormatted . " <span class='price-lkr'>(approx LKR {$lkr})</span>";
    }
    return $usdFormatted;
}

/**
 * Calculate difference in days between two dates
 */
function calculateNights($checkin, $checkout) {
    try {
        $d1 = new DateTime($checkin);
        $d2 = new DateTime($checkout);
        $interval = $d1->diff($d2);
        return max(1, (int)$interval->days);
    } catch (Exception $e) {
        return 1;
    }
}

/**
 * Check if room is available for given date range
 */
function checkRoomAvailability($roomId, $checkin, $checkout, $excludeBookingId = null) {
    $db = getDB();
    $sql = "SELECT COUNT(*) FROM bookings 
            WHERE room_id = :room_id 
            AND status != 'cancelled'
            AND check_in < :checkout 
            AND check_out > :checkin";

    $params = [
        ':room_id' => $roomId,
        ':checkout' => $checkout,
        ':checkin' => $checkin
    ];

    if ($excludeBookingId) {
        $sql .= " AND id != :exclude_id";
        $params[':exclude_id'] = $excludeBookingId;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return ($stmt->fetchColumn() == 0);
}

/**
 * Generate unique human-readable booking reference
 * Format: AH-YYYYMM-XXXX
 */
function generateBookingReference() {
    $prefix = 'AH-' . date('Ym') . '-';
    $random = strtoupper(substr(uniqid(), -4));
    return $prefix . $random;
}

/**
 * Get average rating and count for a room
 */
function getRoomRatingSummary($roomId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE room_id = :room_id AND status = 'approved'");
    $stmt->execute([':room_id' => $roomId]);
    $res = $stmt->fetch();
    return [
        'avg' => $res['avg_rating'] ? round($res['avg_rating'], 1) : 5.0,
        'count' => (int)($res['total_reviews'] ?? 0)
    ];
}

/**
 * Render 5-star visual rating
 */
function renderStars($rating) {
    $html = '<div class="star-rating" title="' . $rating . ' out of 5">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= round($rating)) {
            $html .= '<span class="star filled">★</span>';
        } else {
            $html .= '<span class="star empty">☆</span>';
        }
    }
    $html .= ' <span class="rating-num">(' . number_format($rating, 1) . ')</span></div>';
    return $html;
}

/**
 * Redirect to path
 */
function redirect($path) {
    header("Location: " . $path);
    exit;
}
