<?php
/**
 * Reviews Moderation - Admin Panel
 * Alice House Hiriketiya
 */
$adminTitle = "Guest Reviews & Testimonials Moderation";
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();

// Handle Delete or Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_review') {
        $reviewId = (int)$_POST['review_id'];
        $stmt = $db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute([':id' => $reviewId]);
        setFlash('success', 'Review deleted.');
    } elseif ($_POST['action'] === 'toggle_status') {
        $reviewId = (int)$_POST['review_id'];
        $status = sanitize($_POST['status']);
        $stmt = $db->prepare("UPDATE reviews SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $reviewId]);
        setFlash('success', 'Review status updated.');
    }
    redirect(BASE_URL . '/admin/reviews.php');
}

$reviews = $db->query("SELECT r.*, u.full_name, rm.room_name 
                       FROM reviews r 
                       JOIN users u ON r.user_id = u.id 
                       JOIN rooms rm ON r.room_id = rm.id 
                       ORDER BY r.id DESC")->fetchAll();
?>

<div style="background: #fff; padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="color: var(--primary); font-size: 1.3rem;">Guest Reviews (<?php echo count($reviews); ?>)</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Moderate customer ratings and published testimonials.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 30px;">No reviews submitted yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($rev['full_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($rev['room_name']); ?></td>
                            <td><?php echo renderStars($rev['rating']); ?></td>
                            <td style="max-width: 320px; font-size: 0.88rem; color: var(--text-dark);">
                                "<?php echo htmlspecialchars($rev['comment']); ?>"
                            </td>
                            <td><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></td>
                            <td>
                                <form method="POST" action="<?php echo BASE_URL; ?>/admin/reviews.php">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="review_id" value="<?php echo $rev['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" style="padding: 4px 6px; font-size: 0.8rem; border-radius: 4px; border: 1px solid var(--border);">
                                        <option value="approved" <?php echo ($rev['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="pending" <?php echo ($rev['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form method="POST" action="<?php echo BASE_URL; ?>/admin/reviews.php" onsubmit="return confirm('Delete this review permanently?');">
                                    <input type="hidden" name="action" value="delete_review">
                                    <input type="hidden" name="review_id" value="<?php echo $rev['id']; ?>">
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
