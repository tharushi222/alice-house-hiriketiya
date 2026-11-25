<?php
/**
 * Admin Dashboard Overview
 * Alice House Hiriketiya
 */
$adminTitle = "Dashboard & Property Analytics";
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();

// Statistics Queries
$totalBookings = $db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$confirmedBookings = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();
$pendingBookings = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$totalRevenue = $db->query("SELECT SUM(total_price) FROM bookings WHERE status != 'cancelled'")->fetchColumn() ?: 0;
$totalRooms = $db->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$activeRooms = $db->query("SELECT COUNT(*) FROM rooms WHERE status = 'available'")->fetchColumn();
$totalGuests = $db->query("SELECT COUNT(*) FROM users WHERE role = 'guest'")->fetchColumn();
$totalReviews = $db->query("SELECT COUNT(*) FROM reviews WHERE status = 'approved'")->fetchColumn();

// Fetch 5 most recent bookings
$stmtRecent = $db->query("SELECT b.*, u.full_name, u.email, u.phone, r.room_name 
                          FROM bookings b 
                          JOIN users u ON b.user_id = u.id 
                          JOIN rooms r ON b.room_id = r.id 
                          ORDER BY b.id DESC LIMIT 5");
$recentBookings = $stmtRecent->fetchAll();

// Fetch recent inquiries
$stmtInquiries = $db->query("SELECT * FROM inquiries ORDER BY id DESC LIMIT 4");
$recentInquiries = $stmtInquiries->fetchAll();
?>

<!-- Metric Cards -->
<div class="admin-metrics">
    <div class="metric-card">
        <div class="metric-icon" style="background: #e0f2fe; color: #0284c7;">💰</div>
        <div class="metric-info">
            <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
            <p>Total Revenue</p>
            <small style="color: var(--text-muted); font-size: 0.75rem;">≈ LKR <?php echo number_format($totalRevenue * 310); ?></small>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon" style="background: #fef3c7; color: #d97706;">📅</div>
        <div class="metric-info">
            <h3><?php echo $totalBookings; ?></h3>
            <p>Total Bookings</p>
            <small style="color: var(--success); font-size: 0.75rem;"><strong><?php echo $confirmedBookings; ?></strong> Confirmed / <strong><?php echo $pendingBookings; ?></strong> Pending</small>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon" style="background: #dcfce7; color: #16a34a;">🛏️</div>
        <div class="metric-info">
            <h3><?php echo $activeRooms; ?> / <?php echo $totalRooms; ?></h3>
            <p>Active Rooms</p>
            <small style="color: var(--text-muted); font-size: 0.75rem;">Available on system</small>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon" style="background: #f3e8ff; color: #9333ea;">👥</div>
        <div class="metric-info">
            <h3><?php echo $totalGuests; ?></h3>
            <p>Registered Guests</p>
            <small style="color: var(--text-muted); font-size: 0.75rem;"><?php echo $totalReviews; ?> verified reviews</small>
        </div>
    </div>
</div>

<!-- Recent Bookings Table -->
<div style="background: #fff; padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 30px; box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="color: var(--primary); font-size: 1.25rem;">Recent Reservations</h3>
        <a href="<?php echo BASE_URL; ?>/admin/bookings.php" class="btn btn-sm btn-outline">View All Bookings &rarr;</a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Dates</th>
                    <th>Nights</th>
                    <th>Total ($)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentBookings)): ?>
                    <tr><td colspan="8" style="text-align: center; color: var(--text-muted);">No bookings found yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($b['booking_reference']); ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($b['full_name']); ?></strong><br>
                                <small style="color: var(--text-muted);"><?php echo htmlspecialchars($b['phone']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($b['room_name']); ?></td>
                            <td>
                                <?php echo date('M d', strtotime($b['check_in'])); ?> - <?php echo date('M d, Y', strtotime($b['check_out'])); ?>
                            </td>
                            <td><?php echo $b['total_nights']; ?></td>
                            <td>
                                <strong>$<?php echo number_format($b['total_price'], 2); ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo htmlspecialchars($b['status']); ?>">
                                    <?php echo ucfirst($b['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/admin/bookings.php?search=<?php echo urlencode($b['booking_reference']); ?>" class="btn btn-sm btn-outline">Manage</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Inquiries Section -->
<div style="background: #fff; padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <h3 style="color: var(--primary); font-size: 1.25rem; margin-bottom: 16px;">Recent Guest Inquiries</h3>
    <?php if (empty($recentInquiries)): ?>
        <p style="color: var(--text-muted); font-size: 0.9rem;">No guest messages submitted yet.</p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <?php foreach ($recentInquiries as $inq): ?>
                <div style="background: var(--bg-sand); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <strong><?php echo htmlspecialchars($inq['name']); ?> (<a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>"><?php echo htmlspecialchars($inq['email']); ?></a>)</strong>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo date('M d, Y H:i', strtotime($inq['created_at'])); ?></span>
                    </div>
                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--primary); margin-bottom: 4px;">
                        Subject: <?php echo htmlspecialchars($inq['subject']); ?>
                    </div>
                    <p style="font-size: 0.88rem; color: var(--text-dark); margin: 0;"><?php echo nl2br(htmlspecialchars($inq['message'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
