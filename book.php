<?php
/**
 * Booking Page - Secure Reservation Processing
 * Alice House Hiriketiya
 */
require_once __DIR__ . '/includes/auth.php';

// Guest must be logged in to make a booking
requireLogin();

$db = getDB();
$user = currentUser();

// Fetch room details
$roomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
$stmt = $db->prepare("SELECT * FROM rooms WHERE id = :id AND status != 'unavailable'");
$stmt->execute([':id' => $roomId]);
$room = $stmt->fetch();

if (!$room) {
    setFlash('danger', 'The selected room is invalid or unavailable.');
    redirect(BASE_URL . '/rooms.php');
}

$pageTitle = "Confirm Reservation – " . $room['room_name'];

// Capture and validate dates
$checkin = isset($_GET['check_in']) && !empty($_GET['check_in']) ? sanitize($_GET['check_in']) : date('Y-m-d');
$checkout = isset($_GET['check_out']) && !empty($_GET['check_out']) ? sanitize($_GET['check_out']) : date('Y-m-d', strtotime('+2 days'));
$guests = isset($_GET['guests']) && !empty($_GET['guests']) ? min((int)$_GET['guests'], $room['capacity']) : 1;

// Validate date logic
if (strtotime($checkout) <= strtotime($checkin)) {
    $checkout = date('Y-m-d', strtotime($checkin . ' +1 day'));
}

$nights = calculateNights($checkin, $checkout);
$totalPrice = $nights * $room['price_per_night'];

// Check availability
$isAvailable = checkRoomAvailability($roomId, $checkin, $checkout);

// Handle POST Booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postCheckin = sanitize($_POST['check_in']);
    $postCheckout = sanitize($_POST['check_out']);
    $postGuests = (int)$_POST['guests'];
    $specialRequests = sanitize($_POST['special_requests'] ?? '');
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'Cash on Arrival');

    // Re-verify availability
    if (!checkRoomAvailability($roomId, $postCheckin, $postCheckout)) {
        setFlash('danger', 'Unfortunately, this room has just been reserved for the selected dates. Please choose different dates.');
        redirect(BASE_URL . '/room_details.php?id=' . $roomId);
    }

    $calcNights = calculateNights($postCheckin, $postCheckout);
    $finalTotal = $calcNights * $room['price_per_night'];
    $bookingRef = generateBookingReference();

    try {
        $db->beginTransaction();

        // Insert booking
        $stmtBook = $db->prepare("INSERT INTO bookings 
            (booking_reference, user_id, room_id, check_in, check_out, guests, total_nights, price_per_night, total_price, special_requests, status, payment_status)
            VALUES 
            (:ref, :user_id, :room_id, :check_in, :check_out, :guests, :total_nights, :price_per_night, :total_price, :requests, 'confirmed', 'unpaid')");

        $stmtBook->execute([
            ':ref' => $bookingRef,
            ':user_id' => $user['id'],
            ':room_id' => $roomId,
            ':check_in' => $postCheckin,
            ':check_out' => $postCheckout,
            ':guests' => $postGuests,
            ':total_nights' => $calcNights,
            ':price_per_night' => $room['price_per_night'],
            ':total_price' => $finalTotal,
            ':requests' => $specialRequests
        ]);

        $bookingId = $db->lastInsertId();

        // Insert initial payment entry
        $stmtPay = $db->prepare("INSERT INTO payments 
            (booking_id, amount, payment_method, payment_status, transaction_reference) 
            VALUES 
            (:booking_id, :amount, :method, 'pending', :ref)");
        
        $stmtPay->execute([
            ':booking_id' => $bookingId,
            ':amount' => $finalTotal,
            ':method' => $paymentMethod,
            ':ref' => 'PAY-' . strtoupper(substr(uniqid(), -6))
        ]);

        $db->commit();

        setFlash('success', "🎉 Your booking ({$bookingRef}) has been successfully confirmed! We look forward to hosting you at Alice House Hiriketiya.");
        redirect(BASE_URL . '/my_bookings.php');

    } catch (Exception $e) {
        $db->rollBack();
        setFlash('danger', 'Failed to complete booking: ' . $e->getMessage());
    }
}

require_once __DIR__ . '/includes/header.php';

$imagePath = BASE_URL . '/assets/images/' . $room['primary_image'];
if (!file_exists(__DIR__ . '/assets/images/' . $room['primary_image'])) {
    $imagePath = BASE_URL . '/uploads/rooms/' . $room['primary_image'];
}
?>

<div class="container" style="padding-top: 35px; padding-bottom: 70px;">
    <div class="container-narrow">
        <h1 style="color: var(--primary); font-size: 2rem; font-weight: 800; margin-bottom: 8px;">Complete Your Reservation</h1>
        <p style="color: var(--text-muted); margin-bottom: 30px;">Review your details and finalize your booking with instant confirmation.</p>

        <?php if (!$isAvailable): ?>
            <div class="alert alert-danger">
                ⚠️ This room is currently unavailable for <?php echo htmlspecialchars($checkin); ?> to <?php echo htmlspecialchars($checkout); ?>. Please select alternate dates.
            </div>
        <?php endif; ?>

        <div style="background: #fff; border-radius: var(--radius-md); border: 1px solid var(--border); overflow: hidden; box-shadow: var(--shadow-md); margin-bottom: 30px;">
            <!-- Room Header Summary -->
            <div style="display: flex; gap: 20px; padding: 24px; border-bottom: 1px solid var(--border); background: var(--bg-sand); align-items: center; flex-wrap: wrap;">
                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>" style="width: 140px; height: 95px; object-fit: cover; border-radius: 8px;">
                <div style="flex: 1;">
                    <span class="badge badge-available" style="margin-bottom: 4px;"><?php echo htmlspecialchars($room['room_type']); ?></span>
                    <h3 style="color: var(--primary); font-size: 1.25rem; margin-bottom: 4px;"><?php echo htmlspecialchars($room['room_name']); ?></h3>
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        <span>🛏️ <?php echo htmlspecialchars($room['bed_type']); ?></span> • 
                        <span>👥 Max <?php echo $room['capacity']; ?> Guests</span> • 
                        <span>$<?php echo number_format($room['price_per_night'], 2); ?> / night</span>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <form method="POST" action="<?php echo BASE_URL; ?>/book.php?room_id=<?php echo $roomId; ?>" style="padding: 30px;">
                <input type="hidden" id="price_per_night" value="<?php echo $room['price_per_night']; ?>">
                <input type="hidden" name="total_price" id="total_price_input" value="<?php echo $totalPrice; ?>">

                <h4 style="color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">1. Stay Dates & Guests</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
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
                            <?php for ($g = 1; $g <= $room['capacity']; $g++): ?>
                                <option value="<?php echo $g; ?>" <?php echo ($g == $guests) ? 'selected' : ''; ?>>
                                    <?php echo $g; ?> <?php echo ($g == 1 ? 'Guest' : 'Guests'); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <h4 style="color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">2. Guest Details</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" readonly style="background: #f3f4f6;">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background: #f3f4f6;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="special_requests">Special Requests / Estimated Arrival Time</label>
                    <textarea name="special_requests" id="special_requests" rows="3" class="form-control" placeholder="e.g. Scooter rental needed, flight arrival time, early check-in or dietary preferences..."></textarea>
                </div>

                <h4 style="color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 8px;">3. Payment Method</h4>
                <div class="form-group" style="margin-bottom: 24px;">
                    <select name="payment_method" class="form-control">
                        <option value="Cash on Arrival (USD / LKR)">💵 Pay at Property / Cash on Arrival (USD or LKR)</option>
                        <option value="Credit / Debit Card on Arrival">💳 Card on Arrival (Visa / Mastercard)</option>
                        <option value="Bank Transfer">🏦 Direct Bank Transfer</option>
                    </select>
                </div>

                <!-- Live Price Calculation Box -->
                <div class="cost-breakdown" style="margin-bottom: 25px;">
                    <div class="cost-row">
                        <span>Nightly Rate</span>
                        <span>$<?php echo number_format($room['price_per_night'], 2); ?></span>
                    </div>
                    <div class="cost-row">
                        <span>Duration</span>
                        <span id="calc_nights"><?php echo $nights; ?> night<?php echo ($nights > 1 ? 's' : ''); ?></span>
                    </div>
                    <div class="cost-row">
                        <span>Taxes & Service</span>
                        <span style="color: var(--success); font-weight: bold;">FREE</span>
                    </div>
                    <div class="cost-row total">
                        <span>Total Due</span>
                        <span id="calc_total">$<?php echo number_format($totalPrice, 2); ?></span>
                    </div>
                    <div style="text-align: right; margin-top: 4px;">
                        <span id="calc_total_lkr" style="color: var(--text-muted); font-size: 0.9rem;">
                            ≈ LKR <?php echo number_format($totalPrice * 310); ?>
                        </span>
                    </div>
                </div>

                <?php if ($isAvailable): ?>
                    <button type="submit" class="btn btn-accent btn-lg" style="width: 100%;">
                        Confirm & Reserve Alice House Now
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-primary" style="width: 100%;" disabled>
                        Dates Overlap Existing Reservation
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <div style="text-align: center;">
            <a href="<?php echo BASE_URL; ?>/room_details.php?id=<?php echo $roomId; ?>" style="color: var(--text-muted); font-size: 0.9rem;">&larr; Back to Room Details</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
