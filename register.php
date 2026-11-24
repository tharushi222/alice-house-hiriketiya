<?php
/**
 * Guest Registration Page
 * Alice House Hiriketiya
 */
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirect(BASE_URL . '/my_bookings.php');
}

$pageTitle = "Guest Registration – Alice House Hiriketiya";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($fullName)) $errors[] = "Full Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email address is required.";
    if (empty($phone)) $errors[] = "Phone number is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters long.";
    if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $db = getDB();
        // Check if email already exists
        $stmtCheck = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmtCheck->execute([':email' => $email]);
        if ($stmtCheck->fetch()) {
            $errors[] = "This email address is already registered. Please sign in instead.";
        } else {
            // Hash password and insert
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmtInsert = $db->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (:name, :email, :phone, :pass, 'guest')");
            $stmtInsert->execute([
                ':name' => $fullName,
                ':email' => $email,
                ':phone' => $phone,
                ':pass' => $hash
            ]);

            $newUserId = $db->lastInsertId();

            // Auto-login user
            loginUser([
                'id' => $newUserId,
                'full_name' => $fullName,
                'email' => $email,
                'role' => 'guest'
            ]);

            setFlash('success', "Welcome to Alice House, {$fullName}! Your account has been created.");
            
            $redirectUrl = $_SESSION['redirect_after_login'] ?? (BASE_URL . '/rooms.php');
            unset($_SESSION['redirect_after_login']);
            redirect($redirectUrl);
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 30px; padding-bottom: 60px;">
    <div class="auth-box">
        <div class="auth-header">
            <h2>Create Your Account</h2>
            <p style="color: var(--text-muted); font-size: 0.92rem;">Register to easily book rooms, track reservations, and access special guest benefits.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="display: block;">
                <ul style="margin-left: 20px;">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>/register.php">
            <div class="form-group" style="margin-bottom: 16px;">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" placeholder="e.g. Kasun Perera" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="yourname@gmail.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="phone">Phone / WhatsApp Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" placeholder="+94 77 123 4567" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label for="password">Password (Minimum 6 characters)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; height: 46px;">
                Create Account & Continue
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
            Already have an account? <a href="<?php echo BASE_URL; ?>/login.php" style="font-weight: 700; color: var(--primary);">Sign In</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
