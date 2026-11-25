<?php
/**
 * Room Edit - Admin Panel
 * Alice House Hiriketiya
 */
$adminTitle = "Edit Room Accommodation";
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();
$roomId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("SELECT * FROM rooms WHERE id = :id");
$stmt->execute([':id' => $roomId]);
$room = $stmt->fetch();

if (!$room) {
    setFlash('danger', 'Room not found.');
    redirect(BASE_URL . '/admin/rooms.php');
}

// Handle Update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomName = sanitize($_POST['room_name']);
    $roomType = sanitize($_POST['room_type']);
    $price = (float)$_POST['price_per_night'];
    $capacity = (int)$_POST['capacity'];
    $bedType = sanitize($_POST['bed_type']);
    $roomSize = sanitize($_POST['room_size']);
    $facilities = sanitize($_POST['facilities']);
    $description = sanitize($_POST['description']);
    $status = sanitize($_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $primaryImage = $room['primary_image'];

    // Check if new photo uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image']['name']);
        $uniqueName = time() . '_' . $fileName;
        $targetPath = __DIR__ . '/../../uploads/rooms/' . $uniqueName;
        
        if (move_uploaded_file($fileTmp, $targetPath)) {
            $primaryImage = $uniqueName;
        }
    }

    $stmtUpd = $db->prepare("UPDATE rooms SET 
        room_name = :name,
        room_type = :type,
        price_per_night = :price,
        capacity = :cap,
        bed_type = :bed,
        room_size = :size,
        facilities = :fac,
        description = :desc,
        status = :status,
        featured = :feat,
        primary_image = :img
        WHERE id = :id");

    $stmtUpd->execute([
        ':name' => $roomName,
        ':type' => $roomType,
        ':price' => $price,
        ':cap' => $capacity,
        ':bed' => $bedType,
        ':size' => $roomSize,
        ':fac' => $facilities,
        ':desc' => $description,
        ':status' => $status,
        ':feat' => $featured,
        ':img' => $primaryImage,
        ':id' => $roomId
    ]);

    setFlash('success', "Room '{$roomName}' updated successfully.");
    redirect(BASE_URL . '/admin/rooms.php');
}

$img = BASE_URL . '/assets/images/' . $room['primary_image'];
if (!file_exists(__DIR__ . '/../../assets/images/' . $room['primary_image'])) {
    $img = BASE_URL . '/uploads/rooms/' . $room['primary_image'];
}
?>

<div style="background: #fff; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm); max-width: 900px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border);">
        <h3 style="color: var(--primary); font-size: 1.3rem;">Edit: <?php echo htmlspecialchars($room['room_name']); ?></h3>
        <a href="<?php echo BASE_URL; ?>/admin/rooms.php" class="btn btn-sm btn-outline">&larr; Back to Rooms</a>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>/admin/room_edit.php?id=<?php echo $roomId; ?>" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label for="room_name">Room Title *</label>
                <input type="text" name="room_name" id="room_name" class="form-control" required value="<?php echo htmlspecialchars($room['room_name']); ?>">
            </div>
            <div class="form-group">
                <label for="room_type">Room Category *</label>
                <select name="room_type" id="room_type" class="form-control" required>
                    <option value="Deluxe Double" <?php echo ($room['room_type'] === 'Deluxe Double') ? 'selected' : ''; ?>>Deluxe Double</option>
                    <option value="Superior Triple" <?php echo ($room['room_type'] === 'Superior Triple') ? 'selected' : ''; ?>>Superior Triple</option>
                    <option value="Family Suite" <?php echo ($room['room_type'] === 'Family Suite') ? 'selected' : ''; ?>>Family Suite with Kitchen</option>
                    <option value="Deluxe King" <?php echo ($room['room_type'] === 'Deluxe King') ? 'selected' : ''; ?>>Deluxe King with Terrace</option>
                    <option value="Standard Double" <?php echo ($room['room_type'] === 'Standard Double') ? 'selected' : ''; ?>>Standard Double</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label for="price_per_night">Price per Night ($ USD) *</label>
                <input type="number" step="0.01" name="price_per_night" id="price_per_night" class="form-control" required value="<?php echo $room['price_per_night']; ?>">
            </div>
            <div class="form-group">
                <label for="capacity">Max Guests *</label>
                <input type="number" name="capacity" id="capacity" class="form-control" min="1" max="10" required value="<?php echo $room['capacity']; ?>">
            </div>
            <div class="form-group">
                <label for="bed_type">Bed Setup *</label>
                <input type="text" name="bed_type" id="bed_type" class="form-control" required value="<?php echo htmlspecialchars($room['bed_type']); ?>">
            </div>
            <div class="form-group">
                <label for="room_size">Room Size</label>
                <input type="text" name="room_size" id="room_size" class="form-control" value="<?php echo htmlspecialchars($room['room_size']); ?>">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="facilities">Room Facilities (Comma-separated)</label>
            <input type="text" name="facilities" id="facilities" class="form-control" required value="<?php echo htmlspecialchars($room['facilities']); ?>">
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="description">Room Description *</label>
            <textarea name="description" id="description" rows="4" class="form-control" required><?php echo htmlspecialchars($room['description']); ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: center; margin-bottom: 20px; background: var(--bg-sand); padding: 16px; border-radius: 8px;">
            <div>
                <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px;">Current Photo:</label>
                <img src="<?php echo $img; ?>" alt="Room Photo" style="width: 140px; height: 90px; object-fit: cover; border-radius: 6px;">
            </div>
            <div class="form-group">
                <label for="image">Replace Room Photo (Optional)</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div class="form-group">
                <label for="status">Room Availability Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="available" <?php echo ($room['status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                    <option value="maintenance" <?php echo ($room['status'] === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="unavailable" <?php echo ($room['status'] === 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                </select>
            </div>
            <div style="display: flex; align-items: center; padding-top: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="featured" value="1" <?php echo ($room['featured']) ? 'checked' : ''; ?>>
                    <span>Featured on Home Page</span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Update Room Details</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
