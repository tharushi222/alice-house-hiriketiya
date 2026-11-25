<?php
/**
 * Bookings Management - Admin Panel
 * Alice House Hiriketiya
 */
$adminTitle = "Guest Reservations & Booking Management";
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();

// Handle Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $bookingId = (int)$_POST['booking_id'];
        $newStatus = sanitize($_POST['new_status']);
        $stmt = $db->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $bookingId]);
        setFlash('success', "Booking #{$bookingId} status updated to " . ucfirst($newStatus));
    } elseif ($_POST['action'] === 'update_payment') {
        $bookingId = (int)$_POST['booking_id'];
        $payStatus = sanitize($_POST['payment_status']);
        $stmt = $db->prepare("UPDATE bookings SET payment_status = :pay WHERE id = :id");
        $stmt->execute([':pay' => $payStatus, ':id' => $bookingId]);
        setFlash('success', "Booking #{$bookingId} payment marked as " . ucfirst($payStatus));
    } elseif ($_POST['action'] === 'delete_booking') {
        $bookingId = (int)$_POST['booking_id'];
        $stmt = $db->prepare("DELETE FROM bookings WHERE id = :id");
        $stmt->execute([':id' => $bookingId]);
        setFlash('success', "Booking deleted successfully.");
    }
    redirect(BASE_URL . '/admin/bookings.php');
}

// Filters & Search
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$sql = "SELECT b.*, u.full_name, u.email, u.phone, r.room_name, p.payment_method 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        JOIN rooms r ON b.room_id = r.id 
        LEFT JOIN payments p ON b.id = p.booking_id 
        WHERE 1=1";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " AND b.status = :status";
    $params[':status'] = $statusFilter;
}

if (!empty($search)) {
    $sql .= " AND (b.booking_reference LIKE :s OR u.full_name LIKE :s OR u.email LIKE :s OR u.phone LIKE :s OR r.room_name LIKE :s)";
    $params[':s'] = "%$search%";
}

$sql .= " ORDER BY b.id DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>

<!-- Search & Status Filter Bar -->
<div style="background: #fff; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 25px; box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <!-- Status Tabs -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="<?php echo BASE_URL; ?>/admin/bookings.php" class="btn btn-sm <?php echo empty($statusFilter) ? 'btn-primary' : 'btn-outline'; ?>">All</a>
            <a href="<?php echo BASE_URL; ?>/admin/bookings.php?status=pending" class="btn btn-sm <?php echo ($statusFilter === 'pending') ? 'btn-primary' : 'btn-outline'; ?>">Pending</a>
            <a href="<?php echo BASE_URL; ?>/admin/bookings.php?status=confirmed" class="btn btn-sm <?php echo ($statusFilter === 'confirmed') ? 'btn-primary' : 'btn-outline'; ?>">Confirmed</a>
            <a href="<?php echo BASE_URL; ?>/admin/bookings.php?status=completed" class="btn btn-sm <?php echo ($statusFilter === 'completed') ? 'btn-primary' : 'btn-outline'; ?>">Completed</a>
            <a href="<?php echo BASE_URL; ?>/admin/bookings.php?status=cancelled" class="btn btn-sm <?php echo ($statusFilter === 'cancelled') ? 'btn-primary' : 'btn-outline'; ?>">Cancelled</a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="<?php echo BASE_URL; ?>/admin/bookings.php" style="display: flex; gap: 8px;">
            <input type="text" name="search" class="form-control" style="width: 260px; padding: 6px 12px;" placeholder="Search Ref, Guest, Room..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
            <?php if (!empty($search) || !empty($statusFilter)): ?>
                <a href="<?php echo BASE_URL; ?>/admin/bookings.php" class="btn btn-sm btn-outline">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Bookings Data Table -->
<div style="background: #fff; padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="color: var(--primary); font-size: 1.25rem;">
            Reservations (<?php echo count($bookings); ?>)
        </h3>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Guest Details</th>
                    <th>Room</th>
                    <th>Dates</th>
                    <th>Guests</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="9" style="text-align: center; color: var(--text-muted); padding: 30px;">No bookings match the selected criteria.</td></tr>
                <?php else: ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td>
                                <strong>#<?php echo htmlspecialchars($b['booking_reference']); ?></strong><br>
                                <small style="color: var(--text-muted);"><?php echo date('M d, Y', strtotime($b['created_at'])); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($b['full_name']); ?></strong><br>
                                <small style="color: var(--text-muted);">📧 <?php echo htmlspecialchars($b['email']); ?></small><br>
                                <small style="color: var(--text-muted);">📞 <?php echo htmlspecialchars($b['phone']); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($b['room_name']); ?></strong>
                            </td>
                            <td>
                                <strong>In:</strong> <?php echo date('M d, Y', strtotime($b['check_in'])); ?><br>
                                <strong>Out:</strong> <?php echo date('M d, Y', strtotime($b['check_out'])); ?><br>
                                <small style="color: var(--text-muted);"><?php echo $b['total_nights']; ?> Night<?php echo ($b['total_nights'] > 1 ? 's' : ''); ?></small>
                            </td>
                            <td>👥 <?php echo $b['guests']; ?></td>
                            <td>
                                <strong style="color: var(--primary); font-size: 1.05rem;">$<?php echo number_format($b['total_price'], 2); ?></strong>
                                <small style="display: block; color: var(--text-muted);">≈ LKR <?php echo number_format($b['total_price'] * 310); ?></small>
                            </td>
                            <td>
                                <form method="POST" action="<?php echo BASE_URL; ?>/admin/bookings.php">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                    <select name="new_status" onchange="this.form.submit()" style="padding: 4px 6px; font-size: 0.8rem; border-radius: 4px; border: 1px solid var(--border);">
                                        <option value="pending" <?php echo ($b['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo ($b['status'] === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="completed" <?php echo ($b['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo ($b['status'] === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="<?php echo BASE_URL; ?>/admin/bookings.php">
                                    <input type="hidden" name="action" value="update_payment">
                                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                    <select name="payment_status" onchange="this.form.submit()" style="padding: 4px 6px; font-size: 0.8rem; border-radius: 4px; border: 1px solid var(--border);">
                                        <option value="unpaid" <?php echo ($b['payment_status'] === 'unpaid') ? 'selected' : ''; ?>>Unpaid</option>
                                        <option value="paid" <?php echo ($b['payment_status'] === 'paid') ? 'selected' : ''; ?>>Paid</option>
                                        <option value="refunded" <?php echo ($b['payment_status'] === 'refunded') ? 'selected' : ''; ?>>Refunded</option>
                                    </select>
                                </form>
                                <small style="font-size: 0.72rem; color: var(--text-muted); display: block; margin-top: 2px;">
                                    <?php echo htmlspecialchars($b['payment_method'] ?? 'Cash'); ?>
                                </small>
                            </td>
                            <td>
                                <form method="POST" action="<?php echo BASE_URL; ?>/admin/bookings.php" onsubmit="return confirm('Delete this booking record permanently?');">
                                    <input type="hidden" name="action" value="delete_booking">
                                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
