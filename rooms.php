<?php
/**
 * Rooms Catalog & Availability Search
 * Alice House Hiriketiya
 */
$pageTitle = "Rooms & Rates – Alice House Hiriketiya";
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// Capture filter parameters
$checkin = isset($_GET['check_in']) && !empty($_GET['check_in']) ? sanitize($_GET['check_in']) : date('Y-m-d');
$checkout = isset($_GET['check_out']) && !empty($_GET['check_out']) ? sanitize($_GET['check_out']) : date('Y-m-d', strtotime('+2 days'));
$guests = isset($_GET['guests']) && !empty($_GET['guests']) ? (int)$_GET['guests'] : 1;
$roomType = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$maxPrice = isset($_GET['max_price']) && !empty($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Build query
$sql = "SELECT * FROM rooms WHERE status != 'unavailable'";
$params = [];

if (!empty($roomType)) {
    $sql .= " AND room_type = :type";
    $params[':type'] = $roomType;
}

if ($guests > 0) {
    $sql .= " AND capacity >= :guests";
    $params[':guests'] = $guests;
}

if ($maxPrice > 0) {
    $sql .= " AND price_per_night <= :max_price";
    $params[':max_price'] = $maxPrice;
}

$sql .= " ORDER BY price_per_night ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rooms = $stmt->fetchAll();
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <!-- Search & Filter Bar -->
    <div class="search-box" style="margin-bottom: 40px;">
        <form action="<?php echo BASE_URL; ?>/rooms.php" method="GET" class="search-form">
            <div class="form-group">
                <label for="check_in">Check-in Date</label>
                <input type="date" name="check_in" id="check_in" class="form-control" required value="<?php echo htmlspecialchars($checkin); ?>">
            </div>
            <div class="form-group">
                <label for="check_out">Check-out Date</label>
                <input type="date" name="check_out" id="check_out" class="form-control" required value="<?php echo htmlspecialchars($checkout); ?>">
            </div>
            <div class="form-group">
                <label for="guests">Guests</label>
                <select name="guests" id="guests" class="form-control">
                    <option value="1" <?php echo ($guests == 1) ? 'selected' : ''; ?>>1 Guest</option>
                    <option value="2" <?php echo ($guests == 2) ? 'selected' : ''; ?>>2 Guests</option>
                    <option value="3" <?php echo ($guests == 3) ? 'selected' : ''; ?>>3 Guests</option>
                    <option value="4" <?php echo ($guests == 4) ? 'selected' : ''; ?>>4 Guests</option>
                </select>
            </div>
            <div class="form-group">
                <label for="type">Room Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="">All Room Types</option>
                    <option value="Deluxe Double" <?php echo ($roomType == 'Deluxe Double') ? 'selected' : ''; ?>>Deluxe Double</option>
                    <option value="Superior Triple" <?php echo ($roomType == 'Superior Triple') ? 'selected' : ''; ?>>Superior Triple</option>
                    <option value="Family Suite" <?php echo ($roomType == 'Family Suite') ? 'selected' : ''; ?>>Family Suite with Kitchen</option>
                    <option value="Deluxe King" <?php echo ($roomType == 'Deluxe King') ? 'selected' : ''; ?>>Deluxe King with Terrace</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height: 48px;">
                Filter Rooms
            </button>
        </form>
    </div>

    <!-- Results Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="color: var(--primary); font-size: 2rem; font-weight: 800;">Available Rooms in Hiriketiya</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">
                Showing <?php echo count($rooms); ?> room types for stay dates 
                <strong><?php echo date('M d, Y', strtotime($checkin)); ?></strong> to 
                <strong><?php echo date('M d, Y', strtotime($checkout)); ?></strong> (<?php echo calculateNights($checkin, $checkout); ?> nights)
            </p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>/rooms.php" class="btn btn-sm btn-outline">Reset Filters</a>
        </div>
    </div>

    <!-- Rooms List Grid -->
    <?php if (empty($rooms)): ?>
        <div style="background: #fff; padding: 50px 20px; text-align: center; border-radius: var(--radius-md); border: 1px solid var(--border);">
            <div style="font-size: 40px; margin-bottom: 15px;">🏖️</div>
            <h3 style="color: var(--primary); margin-bottom: 10px;">No Rooms Found Matching Your Criteria</h3>
            <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 20px;">Try adjusting your dates or guest capacity to find available accommodations at Alice House.</p>
            <a href="<?php echo BASE_URL; ?>/rooms.php" class="btn btn-primary">View All Rooms</a>
        </div>
    <?php else: ?>
        <div class="rooms-grid">
            <?php foreach ($rooms as $room): ?>
                <?php 
                    $isAvailable = checkRoomAvailability($room['id'], $checkin, $checkout);
                    $ratingInfo = getRoomRatingSummary($room['id']);
                    $imagePath = BASE_URL . '/assets/images/' . $room['primary_image'];
                    if (!file_exists(__DIR__ . '/assets/images/' . $room['primary_image'])) {
                        $imagePath = BASE_URL . '/uploads/rooms/' . $room['primary_image'];
                    }
                    $nights = calculateNights($checkin, $checkout);
                    $totalStayPrice = $room['price_per_night'] * $nights;
                ?>
                <div class="room-card" style="<?php echo (!$isAvailable) ? 'opacity: 0.85;' : ''; ?>">
                    <div class="room-image-wrap">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>">
                        <div class="room-badge"><?php echo htmlspecialchars($room['room_type']); ?></div>
                        
                        <?php if ($isAvailable): ?>
                            <span class="badge badge-available" style="position: absolute; top: 14px; right: 14px;">Available</span>
                        <?php else: ?>
                            <span class="badge badge-unavailable" style="position: absolute; top: 14px; right: 14px;">Booked on Dates</span>
                        <?php endif; ?>

                        <div class="room-price-tag">
                            $<?php echo number_format($room['price_per_night'], 0); ?>
                            <small>/ night</small>
                        </div>
                    </div>

                    <div class="room-content">
                        <h3 class="room-title">
                            <a href="<?php echo BASE_URL; ?>/room_details.php?id=<?php echo $room['id']; ?>&check_in=<?php echo urlencode($checkin); ?>&check_out=<?php echo urlencode($checkout); ?>&guests=<?php echo $guests; ?>">
                                <?php echo htmlspecialchars($room['room_name']); ?>
                            </a>
                        </h3>
                        
                        <div class="room-meta">
                            <div class="room-meta-item">👥 Capacity: <?php echo $room['capacity']; ?> Guests</div>
                            <div class="room-meta-item">🛏️ <?php echo htmlspecialchars($room['bed_type']); ?></div>
                            <div class="room-meta-item">📐 <?php echo htmlspecialchars($room['room_size']); ?></div>
                        </div>

                        <p class="room-desc"><?php echo htmlspecialchars($room['description']); ?></p>

                        <div class="room-amenities">
                            <?php 
                                $amenities = array_slice(array_map('trim', explode(',', $room['facilities'])), 0, 4);
                                foreach ($amenities as $am): 
                            ?>
                                <span class="amenity-pill">✓ <?php echo htmlspecialchars($am); ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div style="background: var(--bg-sand); padding: 10px 14px; border-radius: var(--radius-sm); margin-bottom: 16px; font-size: 0.88rem;">
                            <strong><?php echo $nights; ?> night<?php echo ($nights > 1 ? 's' : ''); ?>:</strong> 
                            <span style="color: var(--primary); font-weight: 700;">$<?php echo number_format($totalStayPrice, 2); ?></span>
                            <span class="price-lkr">≈ LKR <?php echo number_format($totalStayPrice * 310); ?> total</span>
                        </div>

                        <div class="room-footer">
                            <div>
                                <?php echo renderStars($ratingInfo['avg']); ?>
                                <small style="color: var(--text-muted); display: block;"><?php echo $ratingInfo['count']; ?> verified reviews</small>
                            </div>
                            
                            <?php if ($isAvailable): ?>
                                <a href="<?php echo BASE_URL; ?>/book.php?room_id=<?php echo $room['id']; ?>&check_in=<?php echo urlencode($checkin); ?>&check_out=<?php echo urlencode($checkout); ?>&guests=<?php echo $guests; ?>" class="btn btn-sm btn-accent">
                                    Book Now
                                </a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>/room_details.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-outline">
                                    View Dates
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
