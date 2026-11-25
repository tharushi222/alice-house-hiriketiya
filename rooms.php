<?php
/**
 * Room Management - Admin Panel
 * Alice House Hiriketiya
 */
$adminTitle = "Rooms & Accommodation Management";
require_once __DIR__ . '/includes/admin_header.php';

$db = getDB();

// Handle Delete Room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_room') {
    $roomId = (int)$_POST['room_id'];
    $stmtDel = $db->prepare("DELETE FROM rooms WHERE id = :id");
    $stmtDel->execute([':id' => $roomId]);
    setFlash('success', 'Room has been deleted successfully.');
    redirect(BASE_URL . '/admin/rooms.php');
}

// Handle Status Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $roomId = (int)$_POST['room_id'];
    $newStatus = sanitize($_POST['new_status']);
    $stmtUpd = $db->prepare("UPDATE rooms SET status = :status WHERE id = :id");
    $stmtUpd->execute([':status' => $newStatus, ':id' => $roomId]);
    setFlash('success', 'Room status updated.');
    redirect(BASE_URL . '/admin/rooms.php');
}

// Handle Add New Room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_room') {
    $roomName = sanitize($_POST['room_name']);
    $roomType = sanitize($_POST['room_type']);
    $price = (float)$_POST['price_per_night'];
    $capacity = (int)$_POST['capacity'];
    $bedType = sanitize($_POST['bed_type']);
    $roomSize = sanitize($_POST['room_size']);
    $facilities = sanitize($_POST['facilities']);
    $description = sanitize($_POST['description']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $primaryImage = 'double-room.jpg'; // default fallback

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['image']['name']);
        $uniqueName = time() . '_' . $fileName;
        $targetPath = __DIR__ . '/../../uploads/rooms/' . $uniqueName;
        
        if (move_uploaded_file($fileTmp, $targetPath)) {
            $primaryImage = $uniqueName;
        }
    } elseif (!empty($_POST['preset_image'])) {
        $primaryImage = sanitize($_POST['preset_image']);
    }

    $galleryJson = json_encode([$primaryImage, 'garden.jpg', 'breakfast.jpg']);

    $stmtInsert = $db->prepare("INSERT INTO rooms 
        (room_name, room_type, description, price_per_night, capacity, bed_type, room_size, primary_image, gallery, facilities, status, featured) 
        VALUES 
        (:name, :type, :desc, :price, :cap, :bed, :size, :img, :gal, :fac, 'available', :feat)");

    $stmtInsert->execute([
        ':name' => $roomName,
        ':type' => $roomType,
        ':desc' => $description,
        ':price' => $price,
        ':cap' => $capacity,
        ':bed' => $bedType,
        ':size' => $roomSize,
        ':img' => $primaryImage,
        ':gal' => $galleryJson,
        ':fac' => $facilities,
        ':feat' => $featured
    ]);

    setFlash('success', "Room '{$roomName}' created successfully.");
    redirect(BASE_URL . '/admin/rooms.php');
}

// Fetch all rooms
$rooms = $db->query("SELECT * FROM rooms ORDER BY id DESC")->fetchAll();
?>

<!-- Rooms List Table -->
<div style="background: #fff; padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--border); margin-bottom: 40px; box-shadow: var(--shadow-sm);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="color: var(--primary); font-size: 1.3rem;">All Rooms (<?php echo count($rooms); ?>)</h3>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Manage room pricing, availability statuses, and details.</p>
        </div>
        <a href="#add-room" class="btn btn-sm btn-accent">+ Add New Room</a>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Room Name</th>
                    <th>Type</th>
                    <th>Price / Night</th>
                    <th>Capacity</th>
                    <th>Bed Setup</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $r): 
                    $img = BASE_URL . '/assets/images/' . $r['primary_image'];
                    if (!file_exists(__DIR__ . '/../../assets/images/' . $r['primary_image'])) {
                        $img = BASE_URL . '/uploads/rooms/' . $r['primary_image'];
                    }
                ?>
                    <tr>
                        <td style="width: 80px;">
                            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($r['room_name']); ?>" style="width: 70px; height: 50px; object-fit: cover; border-radius: 6px;">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($r['room_name']); ?></strong>
                            <?php if ($r['featured']): ?>
                                <span style="background: #fef08a; color: #854d0e; font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">FEATURED</span>
                            <?php endif; ?>
                            <br>
                            <small style="color: var(--text-muted);"><?php echo htmlspecialchars($r['room_size']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($r['room_type']); ?></td>
                        <td>
                            <strong>$<?php echo number_format($r['price_per_night'], 2); ?></strong>
                            <small style="color: var(--text-muted); display: block;">≈ LKR <?php echo number_format($r['price_per_night'] * 310); ?></small>
                        </td>
                        <td>👥 <?php echo $r['capacity']; ?> Guests</td>
                        <td><?php echo htmlspecialchars($r['bed_type']); ?></td>
                        <td>
                            <form method="POST" action="<?php echo BASE_URL; ?>/admin/rooms.php" style="display: inline;">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="room_id" value="<?php echo $r['id']; ?>">
                                <select name="new_status" onchange="this.form.submit()" style="padding: 4px 8px; font-size: 0.8rem; border-radius: 4px; border: 1px solid var(--border);">
                                    <option value="available" <?php echo ($r['status'] === 'available') ? 'selected' : ''; ?>>Available</option>
                                    <option value="maintenance" <?php echo ($r['status'] === 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                                    <option value="unavailable" <?php echo ($r['status'] === 'unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <a href="<?php echo BASE_URL; ?>/admin/room_edit.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                <form method="POST" action="<?php echo BASE_URL; ?>/admin/rooms.php" onsubmit="return confirm('Are you sure you want to delete this room? This will also remove associated bookings!');">
                                    <input type="hidden" name="action" value="delete_room">
                                    <input type="hidden" name="room_id" value="<?php echo $r['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add New Room Form -->
<div id="add-room" style="background: #fff; padding: 30px; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <h3 style="color: var(--primary); font-size: 1.3rem; margin-bottom: 20px;">+ Add New Room or Accommodation</h3>

    <form method="POST" action="<?php echo BASE_URL; ?>/admin/rooms.php" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_room">

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label for="room_name">Room Title *</label>
                <input type="text" name="room_name" id="room_name" class="form-control" placeholder="e.g. Deluxe Double Room with Garden View" required>
            </div>
            <div class="form-group">
                <label for="room_type">Room Category *</label>
                <select name="room_type" id="room_type" class="form-control" required>
                    <option value="Deluxe Double">Deluxe Double</option>
                    <option value="Superior Triple">Superior Triple</option>
                    <option value="Family Suite">Family Suite with Kitchen</option>
                    <option value="Deluxe King">Deluxe King with Terrace</option>
                    <option value="Standard Double">Standard Double</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group">
                <label for="price_per_night">Price per Night (USD) *</label>
                <input type="number" step="0.01" name="price_per_night" id="price_per_night" class="form-control" placeholder="35.00" required>
            </div>
            <div class="form-group">
                <label for="capacity">Max Guests *</label>
                <input type="number" name="capacity" id="capacity" class="form-control" value="2" min="1" max="10" required>
            </div>
            <div class="form-group">
                <label for="bed_type">Bed Setup *</label>
                <input type="text" name="bed_type" id="bed_type" class="form-control" placeholder="1 Large Queen Bed" required>
            </div>
            <div class="form-group">
                <label for="room_size">Room Size</label>
                <input type="text" name="room_size" id="room_size" class="form-control" value="26 sqm">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="facilities">Room Facilities (Comma-separated)</label>
            <input type="text" name="facilities" id="facilities" class="form-control" value="Air Conditioning, High-speed Free WiFi, Private Bathroom, Hot Water Shower, Garden View, Soundproofing, Free Parking" required>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label for="description">Room Description *</label>
            <textarea name="description" id="description" rows="3" class="form-control" placeholder="Describe the room, ambiance, views, and special features..." required></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
            <div class="form-group">
                <label for="image">Upload Room Photo (JPG/PNG)</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label for="preset_image">Or Choose Preset Asset Photo</label>
                <select name="preset_image" id="preset_image" class="form-control">
                    <option value="double-room.jpg">Deluxe Double Photo</option>
                    <option value="triple-room.jpg">Superior Triple Photo</option>
                    <option value="family-room.jpg">Family Suite Photo</option>
                    <option value="king-terrace.jpg">King Terrace Photo</option>
                    <option value="garden.jpg">Garden Villa Photo</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="featured" value="1" checked>
                <span>Feature this room on the home page</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Save & Add Room</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
