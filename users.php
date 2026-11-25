<?php
/**
 * User & Guest Management - Admin Panel
 * Alice House Hiriketiya
 */
$adminTitle = "Registered Guests & User Accounts";
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();

// Handle User Role Toggle or Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_user') {
        $userId = (int)$_POST['user_id'];
        if ($userId === (int)$_SESSION['user_id']) {
            setFlash('danger', 'You cannot delete your own logged-in admin account!');
        } else {
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $userId]);
            setFlash('success', 'User account deleted.');
        }
    }
    redirect(BASE_URL . '/admin/users.php');
}

// Fetch users with booking counts
$sql = "SELECT u.*, COUNT(b.id) as booking_count 
        FROM users u 
        LEFT JOIN bookings b ON u.id = b.user_id 
        GROUP BY u.id 
        ORDER BY u.id DESC";
$users = $db->query($sql)->fetchAll();
?>

<div style="background: #fff; padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="color: var(--primary); font-size: 1.3rem;">All Users (<?php echo count($users); ?>)</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Registered guest profiles and administrator accounts.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Phone / WhatsApp</th>
                    <th>Role</th>
                    <th>Registered Date</th>
                    <th>Total Bookings</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>#<?php echo $u['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($u['full_name']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($u['phone']); ?></td>
                        <td>
                            <span class="badge <?php echo ($u['role'] === 'admin') ? 'badge-confirmed' : 'badge-completed'; ?>">
                                <?php echo ucfirst($u['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        <td>
                            <a href="<?php echo BASE_URL; ?>/admin/bookings.php?search=<?php echo urlencode($u['email']); ?>" class="btn btn-sm btn-outline">
                                📅 <?php echo $u['booking_count']; ?> Bookings
                            </a>
                        </td>
                        <td>
                            <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                                <form method="POST" action="<?php echo BASE_URL; ?>/admin/users.php" onsubmit="return confirm('Delete this user? All their bookings and reviews will also be removed!');">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            <?php else: ?>
                                <small style="color: var(--text-muted);">Current User</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
