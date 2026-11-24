<?php
/**
 * Login Page - Guest & Admin Authentication
 * Alice House Hiriketiya
 */
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(BASE_URL . '/admin/index.php');
    } else {
        redirect(BASE_URL . '/my_bookings.php');
    }
}

$pageTitle = "Login – Alice House Hiriketiya";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);

            setFlash('success', "Welcome back, {$user['full_name']}!");

            if ($user['role'] === 'admin') {
                redirect(BASE_URL . '/admin/index.php');
            } else {
                $redirectUrl = $_SESSION['redirect_after_login'] ?? (BASE_URL . '/my_bookings.php');
                unset($_SESSION['redirect_after_login']);
                redirect($redirectUrl);
            }
        } else {
            $error = "Invalid email address or password.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 30px; padding-bottom: 60px;">
    <div class="auth-box">
        <div class="auth-header">
            <h2>Welcome Back</h2>
            <p style="color: var(--text-muted); font-size: 0.92rem;">Sign in to your Alice House account.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Quick Demo Credentials Banner -->
        <div style="background: var(--ocean-light); border: 1px solid rgba(15, 118, 110, 0.2); padding: 14px; border-radius: var(--radius-sm); margin-bottom: 20px; font-size: 0.85rem;">
            <strong style="color: var(--ocean); display: block; margin-bottom: 6px;">💡 Demo Accounts for Testing:</strong>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" class="btn btn-sm btn-outline" onclick="fillCredentials('admin@alicehouse.com', 'admin123')">
                    👑 Admin: admin@alicehouse.com
                </button>
                <button type="button" class="btn btn-sm btn-outline" onclick="fillCredentials('guest@gmail.com', 'guest123')">
                    👤 Guest: guest@gmail.com
                </button>
            </div>
        </div>

        <form method="POST" action="<?php echo BASE_URL; ?>/login.php">
            <div class="form-group" style="margin-bottom: 18px;">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; height: 46px;">
                Sign In
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
            Don't have an account yet? <a href="<?php echo BASE_URL; ?>/register.php" style="font-weight: 700; color: var(--primary);">Register Now</a>
        </div>
    </div>
</div>

<script>
function fillCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
