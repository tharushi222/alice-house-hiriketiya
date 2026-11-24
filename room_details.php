<?php
/**
 * Room Details View & Interactive Booking Widget
 * Alice House Hiriketiya
 */
require_once __DIR__ . '/includes/header.php';

$db = getDB();

$roomId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $db->prepare("SELECT * FROM rooms WHERE id = :id");
$stmt->execute([':id' => $roomId]);
$room = $stmt->fetch();

if (!$room) {
    setFlash('danger', 'Room not found.');
    redirect(BASE_URL . '/rooms.php');
}

$pageTitle = $room['room_name'] . " – Alice House Hiriketiya";

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    if (!isLoggedIn()) {
        setFlash('warning', 'Please log in to submit a review.');
        redirect(BASE_URL . '/login.php');
    }

    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    $comment = isset($_POST['comment']) ? sanitize($_POST['comment']) : '';

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $stmtRev = $db->prepare("INSERT INTO reviews (room_id, user_id, rating, comment, status) VALUES (:room_id, :user_id, :rating, :comment, 'approved')");
        $stmtRev->execute([
            ':room_id' => $roomId,
            ':user_id' => $_SESSION['user_id'],
            ':rating' => $rating,
            ':comment' => $comment
        ]);
        setFlash('success', 'Thank you! Your review has been published.');
        redirect(BASE_URL . '/room_details.php?id=' . $roomId);
    } else {
        setFlash('danger', 'Please provide a valid rating and review comment.');
    }
}

// Dates from query or defaults
$checkin = isset($_GET['check_in']) && !empty($_GET['check_in']) ? sanitize($_GET['check_in']) : date('Y-m-d');
$checkout = isset($_GET['check_out']) && !empty($_GET['check_out']) ? sanitize($_GET['check_out']) : date('Y-m-d', strtotime('+2 days'));
$guests = isset($_GET['guests']) && !empty($_GET['guests']) ? min((int)$_GET['guests'], $room['capacity']) : 1;

$nights = calculateNights($checkin, $checkout);
$isAvailable = checkRoomAvailability($roomId, $checkin, $checkout);
$ratingSummary = getRoomRatingSummary($roomId);

// Fetch room reviews
$stmtReviews = $db->prepare("SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.room_id = :room_id AND r.status = 'approved' ORDER BY r.id DESC");
$stmtReviews->execute([':room_id' => $roomId]);
$reviews = $stmtReviews->fetchAll();

// Gallery images array
$gallery = [];
if (!empty($room['gallery'])) {
    $gallery = json_decode($room['gallery'], true);
}
if (empty($gallery)) {
    $gallery = [$room['primary_image'], 'garden.jpg', 'breakfast.jpg'];
}

$mainImageSrc = BASE_URL . '/assets/images/' . $room['primary_image'];
if (!file_exists(__DIR__ . '/assets/images/' . $room['primary_image'])) {
    $mainImageSrc = BASE_URL . '/uploads/rooms/' . $room['primary_image'];
}
?>

<div class="container" style="padding-top: 30px; padding-bottom: 60px;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;">
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a> &rsaquo; 
        <a href="<?php echo BASE_URL; ?>/rooms.php">Rooms</a> &rsaquo; 
        <span style="color: var(--text-dark);"><?php echo htmlspecialchars($room['room_name']); ?></span>
    </div>

    <!-- Title Bar -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px; margin-bottom: 25px;">
        <div>
            <h1 style="color: var(--primary); font-size: 2.2rem; font-weight: 800; line-height: 1.2;">
                <?php echo htmlspecialchars($room['room_name']); ?>
            </h1>
            <div style="display: flex; align-items: center; gap: 14px; margin-top: 8px;">
                <span class="badge badge-available"><?php echo htmlspecialchars($room['room_type']); ?></span>
                <?php echo renderStars($ratingSummary['avg']); ?>
                <span style="color: var(--text-muted); font-size: 0.88rem;">• <?php echo $ratingSummary['count']; ?> guest reviews</span>
                <span style="color: var(--text-muted); font-size: 0.88rem;">• Wedagedara, Dodampahala, Dikwella</span>
            </div>
        </div>
        <div>
            <a href="https://www.booking.com/Share-aINqUA" target="_blank" rel="noopener noreferrer" class="booking-badge-btn">
                <span>View on Booking.com</span> ↗
            </a>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="details-gallery">
        <div class="gallery-main">
            <img id="mainGalleryImg" src="<?php echo $mainImageSrc; ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>">
        </div>
        <div class="gallery-thumbs">
            <?php foreach ($gallery as $idx => $img): 
                $thumbSrc = BASE_URL . '/assets/images/' . $img;
                if (!file_exists(__DIR__ . '/assets/images/' . $img)) {
                    $thumbSrc = BASE_URL . '/uploads/rooms/' . $img;
                }
            ?>
                <img class="gallery-thumb-item" src="<?php echo $thumbSrc; ?>" data-full="<?php echo $thumbSrc; ?>" alt="Thumbnail <?php echo $idx+1; ?>" style="cursor: pointer; <?php echo ($idx == 0) ? 'border: 2px solid var(--accent);' : 'opacity: 0.8;'; ?>">
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Layout: Left Info, Right Booking Widget -->
    <div class="details-layout">
        <!-- Left Content -->
        <div>
            <!-- Overview Badges -->
            <div style="display: flex; flex-wrap: wrap; gap: 20px; background: #fff; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 30px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 24px;">👥</span>
                    <div>
                        <strong style="display:block; font-size:0.9rem;">Capacity</strong>
                        <span style="color:var(--text-muted); font-size:0.85rem;">Up to <?php echo $room['capacity']; ?> Guests</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 24px;">🛏️</span>
                    <div>
                        <strong style="display:block; font-size:0.9rem;">Bed Setup</strong>
                        <span style="color:var(--text-muted); font-size:0.85rem;"><?php echo htmlspecialchars($room['bed_type']); ?></span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 24px;">📐</span>
                    <div>
                        <strong style="display:block; font-size:0.9rem;">Room Size</strong>
                        <span style="color:var(--text-muted); font-size:0.85rem;"><?php echo htmlspecialchars($room['room_size']); ?></span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 24px;">🌿</span>
                    <div>
                        <strong style="display:block; font-size:0.9rem;">View</strong>
                        <span style="color:var(--text-muted); font-size:0.85rem;">Tropical Garden & Palms</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div style="background: #fff; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 30px;">
                <h3 style="color: var(--primary); font-size: 1.3rem; margin-bottom: 14px;">About this Room</h3>
                <p style="color: var(--text-dark); line-height: 1.8; font-size: 0.98rem;"><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
            </div>

            <!-- Facilities & Amenities -->
            <div style="background: #fff; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 30px;">
                <h3 style="color: var(--primary); font-size: 1.3rem; margin-bottom: 18px;">Room Amenities & Features</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px;">
                    <?php 
                        $facilities = array_map('trim', explode(',', $room['facilities']));
                        foreach ($facilities as $fac): 
                    ?>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.92rem; color: var(--text-dark);">
                            <span style="color: var(--success); font-weight: bold;">✔</span>
                            <span><?php echo htmlspecialchars($fac); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- House Rules -->
            <div style="background: #fff; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 30px;">
                <h3 style="color: var(--primary); font-size: 1.3rem; margin-bottom: 16px;">House Policies & Good to Know</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.92rem;">
                    <div>
                        <strong>🕒 Check-in:</strong> 2:00 PM – 10:00 PM<br>
                        <strong>🕚 Check-out:</strong> 7:00 AM – 11:00 AM
                    </div>
                    <div>
                        <strong>🚭 Smoking:</strong> Outdoors / Garden area only<br>
                        <strong>🐾 Pets:</strong> Allowed upon prior request
                    </div>
                    <div>
                        <strong>💳 Payment:</strong> Cash on Arrival or Card<br>
                        <strong>❌ Cancellation:</strong> Free cancellation up to 48h prior
                    </div>
                    <div>
                        <strong>🍳 Breakfast:</strong> Available daily ($4 per person)<br>
                        <strong>🛵 Transport:</strong> Scooter rentals & tuk-tuks on premise
                    </div>
                </div>
            </div>

            <!-- Guest Reviews Section -->
            <div style="background: #fff; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="color: var(--primary); font-size: 1.3rem;">Guest Reviews (<?php echo count($reviews); ?>)</h3>
                    <div><?php echo renderStars($ratingSummary['avg']); ?></div>
                </div>

                <!-- Review list -->
                <?php if (empty($reviews)): ?>
                    <p style="color: var(--text-muted);">No reviews written yet for this room. Be the first to review!</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 30px;">
                        <?php foreach ($reviews as $rev): ?>
                            <div style="border-bottom: 1px solid var(--border); padding-bottom: 14px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                    <strong><?php echo htmlspecialchars($rev['full_name']); ?></strong>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></span>
                                </div>
                                <?php echo renderStars($rev['rating']); ?>
                                <p style="font-size: 0.92rem; color: var(--text-dark); margin-top: 6px;"><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Review Form -->
                <div style="background: var(--bg-sand); padding: 20px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                    <h4 style="color: var(--primary); margin-bottom: 12px;">Leave a Verified Review</h4>
                    <?php if (isLoggedIn()): ?>
                        <form method="POST" action="<?php echo BASE_URL; ?>/room_details.php?id=<?php echo $roomId; ?>">
                            <input type="hidden" name="action" value="submit_review">
                            <div class="form-group" style="margin-bottom: 12px;">
                                <label for="rating">Rating (1 to 5 Stars)</label>
                                <select name="rating" id="rating" class="form-control" style="width: auto;">
                                    <option value="5">★★★★★ - 5 Stars (Exceptional)</option>
                                    <option value="4">★★★★☆ - 4 Stars (Very Good)</option>
                                    <option value="3">★★★☆☆ - 3 Stars (Average)</option>
                                    <option value="2">★★☆☆☆ - 2 Stars (Poor)</option>
                                    <option value="1">★☆☆☆☆ - 1 Star (Terrible)</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 14px;">
                                <label for="comment">Your Experience</label>
                                <textarea name="comment" id="comment" rows="3" class="form-control" placeholder="Share your experience at Alice House Hiriketiya..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Submit Review</button>
                        </form>
                    <?php else: ?>
                        <p style="font-size: 0.9rem; color: var(--text-muted);">
                            Please <a href="<?php echo BASE_URL; ?>/login.php" style="font-weight: 700; text-decoration: underline;">log in</a> to share your feedback.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Booking Widget -->
        <div>
            <div class="booking-card">
                <div class="booking-card-header">
                    <div class="booking-card-price">
                        $<?php echo number_format($room['price_per_night'], 2); ?>
                        <small>/ night</small>
                    </div>
                    <span class="price-lkr">≈ LKR <?php echo number_format($room['price_per_night'] * 310); ?> per night</span>
                </div>

                <form action="<?php echo BASE_URL; ?>/book.php" method="GET">
                    <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">
                    <input type="hidden" id="price_per_night" value="<?php echo $room['price_per_night']; ?>">

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label for="check_in">Check-in</label>
                        <input type="date" name="check_in" id="check_in" class="form-control" required value="<?php echo htmlspecialchars($checkin); ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 14px;">
                        <label for="check_out">Check-out</label>
                        <input type="date" name="check_out" id="check_out" class="form-control" required value="<?php echo htmlspecialchars($checkout); ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 18px;">
                        <label for="guests">Guests (Max: <?php echo $room['capacity']; ?>)</label>
                        <select name="guests" id="guests" class="form-control">
                            <?php for ($g = 1; $g <= $room['capacity']; $g++): ?>
                                <option value="<?php echo $g; ?>" <?php echo ($g == $guests) ? 'selected' : ''; ?>>
                                    <?php echo $g; ?> <?php echo ($g == 1 ? 'Guest' : 'Guests'); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Dynamic Cost Breakdown -->
                    <div class="cost-breakdown">
                        <div class="cost-row">
                            <span>$<?php echo number_format($room['price_per_night'], 2); ?> &times; <span id="calc_nights"><?php echo $nights; ?> night<?php echo ($nights > 1 ? 's' : ''); ?></span></span>
                            <span id="calc_total">$<?php echo number_format($room['price_per_night'] * $nights, 2); ?></span>
                        </div>
                        <div class="cost-row">
                            <span>Service & Cleaning</span>
                            <span style="color: var(--success); font-weight: bold;">FREE</span>
                        </div>
                        <div class="cost-row">
                            <span>Taxes & Fees</span>
                            <span>Included</span>
                        </div>
                        <div class="cost-row total">
                            <span>Total (USD)</span>
                            <span id="calc_total_display">$<?php echo number_format($room['price_per_night'] * $nights, 2); ?></span>
                        </div>
                        <div style="text-align: right; margin-top: 4px;">
                            <small id="calc_total_lkr" style="color: var(--text-muted); font-size: 0.85rem;">
                                ≈ LKR <?php echo number_format($room['price_per_night'] * $nights * 310); ?>
                            </small>
                        </div>
                    </div>

                    <?php if ($isAvailable): ?>
                        <button type="submit" class="btn btn-accent btn-lg" style="width: 100%;">
                            Reserve This Room Direct
                        </button>
                    <?php else: ?>
                        <div class="alert alert-warning" style="margin-bottom: 14px; font-size: 0.85rem;">
                            ⚠️ Selected dates overlap with an existing booking. Please choose different dates.
                        </div>
                        <button type="button" class="btn btn-primary" style="width: 100%;" disabled>
                            Dates Unavailable
                        </button>
                    <?php endif; ?>
                </form>

                <div style="margin-top: 20px; font-size: 0.82rem; color: var(--text-muted); text-align: center;">
                    🔒 Instant Confirmation • Pay at Property / Cash • No Prepayment Needed
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
