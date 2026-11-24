<?php
/**
 * My Bookings - Guest Booking Management
 * Alice House Hiriketiya
 */
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = "My Bookings – Alice House Hiriketiya";
$db = getDB();
$user = currentUser();

// Handle Booking Cancellation request by Guest
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_booking') {
    $bookingId = (int)$_POST['booking_id'];
    
    // Verify booking belongs to this user and is not already cancelled
    $stmtCheck = $db->prepare("SELECT * FROM bookings WHERE id = :id AND user_id = :user_id");
    $stmtCheck->execute([':id' => $bookingId, ':user_id' => $user['id']]);
    $booking = $stmtCheck->fetch();

    if ($booking) {
        if ($booking['status'] === 'cancelled') {
            setFlash('info', 'This booking is already cancelled.');
        } else {
            $stmtCancel = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id");
            $stmtCancel->execute([':id' => $bookingId]);
            setFlash('success', "Booking #{$booking['booking_reference']} has been cancelled.");
        }
    } else {
        setFlash('danger', 'Booking not found or permission denied.');
    }
    redirect(BASE_URL . '/my_bookings.php');
}

// Fetch all bookings for this user
$stmt = $db->prepare("SELECT b.*, r.room_name, r.room_type, r.primary_image, p.payment_method, p.payment_status as p_status 
                      FROM bookings b 
                      JOIN rooms r ON b.room_id = r.id 
                      LEFT JOIN payments p ON b.id = p.booking_id
                      WHERE b.user_id = :user_id 
                      ORDER BY b.id DESC");
$stmt->execute([':user_id' => $user['id']]);
$bookings = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 70px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="color: var(--primary); font-size: 2rem; font-weight: 800;">My Reservations</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Manage your active bookings, view details, and review your stays.</p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>/rooms.php" class="btn btn-primary">+ Book Another Room</a>
        </div>
    </div>

    <?php if (empty($bookings)): ?>
        <div style="background: #fff; padding: 60px 20px; text-align: center; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <div style="font-size: 45px; margin-bottom: 15px;">🏝️</div>
            <h3 style="color: var(--primary); margin-bottom: 10px;">No Bookings Found</h3>
            <p style="color: var(--text-muted); max-width: 480px; margin: 0 auto 24px;">You haven't made any reservations yet at Alice House Hiriketiya. Browse our rooms and book your tropical getaway!</p>
            <a href="<?php echo BASE_URL; ?>/rooms.php" class="btn btn-accent">Explore Available Rooms</a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($bookings as $b): 
                $img = BASE_URL . '/assets/images/' . $b['primary_image'];
                if (!file_exists(__DIR__ . '/assets/images/' . $b['primary_image'])) {
                    $img = BASE_URL . '/uploads/rooms/' . $b['primary_image'];
                }
            ?>
                <div style="background: #fff; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; display: flex; flex-direction: row; flex-wrap: wrap;">
                    <div style="width: 240px; min-height: 180px; flex-shrink: 0;">
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($b['room_name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                    <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <div>
                                    <span style="font-size: 0.82rem; font-weight: 700; color: var(--accent); letter-spacing: 0.5px;">BOOKING REF: #<?php echo htmlspecialchars($b['booking_reference']); ?></span>
                                    <h3 style="color: var(--primary); font-size: 1.3rem; margin-top: 2px;">
                                        <a href="<?php echo BASE_URL; ?>/room_details.php?id=<?php echo $b['room_id']; ?>"><?php echo htmlspecialchars($b['room_name']); ?></a>
                                    </h3>
                                </div>
                                <div>
                                    <span class="badge badge-<?php echo htmlspecialchars($b['status']); ?>">
                                        <?php echo ucfirst($b['status']); ?>
                                    </span>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin: 14px 0; font-size: 0.9rem;">
                                <div>
                                    <span style="color: var(--text-muted); display: block; font-size: 0.78rem;">CHECK-IN</span>
                                    <strong><?php echo date('D, M d, Y', strtotime($b['check_in'])); ?></strong>
                                    <small style="display: block; color: var(--text-muted);">From 2:00 PM</small>
                                </div>
                                <div>
                                    <span style="color: var(--text-muted); display: block; font-size: 0.78rem;">CHECK-OUT</span>
                                    <strong><?php echo date('D, M d, Y', strtotime($b['check_out'])); ?></strong>
                                    <small style="display: block; color: var(--text-muted);">Until 11:00 AM</small>
                                </div>
                                <div>
                                    <span style="color: var(--text-muted); display: block; font-size: 0.78rem;">DETAILS</span>
                                    <strong><?php echo $b['total_nights']; ?> Nights, <?php echo $b['guests']; ?> Guests</strong>
                                    <small style="display: block; color: var(--text-muted);"><?php echo htmlspecialchars($b['payment_method'] ?? 'Pay at Property'); ?></small>
                                </div>
                                <div>
                                    <span style="color: var(--text-muted); display: block; font-size: 0.78rem;">TOTAL AMOUNT</span>
                                    <strong style="color: var(--primary); font-size: 1.1rem;">$<?php echo number_format($b['total_price'], 2); ?></strong>
                                    <small style="display: block; color: var(--text-muted);">≈ LKR <?php echo number_format($b['total_price'] * 310); ?></small>
                                </div>
                            </div>

                            <?php if (!empty($b['special_requests'])): ?>
                                <div style="background: var(--bg-sand); padding: 8px 12px; border-radius: var(--radius-sm); font-size: 0.85rem; color: var(--text-dark); margin-bottom: 12px;">
                                    <strong>Your Notes:</strong> "<?php echo htmlspecialchars($b['special_requests']); ?>"
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 14px; margin-top: 10px;">
                            <span style="font-size: 0.82rem; color: var(--text-muted);">
                                Booked on <?php echo date('M d, Y', strtotime($b['created_at'])); ?>
                            </span>

                            <div style="display: flex; gap: 10px;">
                                <a href="<?php echo BASE_URL; ?>/room_details.php?id=<?php echo $b['room_id']; ?>#reviews" class="btn btn-sm btn-outline">
                                    ★ Leave Review
                                </a>

                                <?php if ($b['status'] !== 'cancelled'): ?>
                                    <form method="POST" action="<?php echo BASE_URL; ?>/my_bookings.php" onsubmit="return confirm('Are you sure you want to cancel booking #<?php echo $b['booking_reference']; ?>?');">
                                        <input type="hidden" name="action" value="cancel_booking">
                                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Cancel Reservation</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
