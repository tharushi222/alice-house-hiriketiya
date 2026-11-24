<?php
/**
 * Contact & Inquiries Page
 * Alice House Hiriketiya
 */
$pageTitle = "Contact & Location – Alice House Hiriketiya";
require_once __DIR__ . '/includes/header.php';

$db = getDB();
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? 'General Inquiry');
    $message = sanitize($_POST['message'] ?? '');

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $db->prepare("INSERT INTO inquiries (name, email, phone, subject, message) VALUES (:name, :email, :phone, :sub, :msg)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':sub' => $subject,
            ':msg' => $message
        ]);
        $success = true;
        setFlash('success', 'Thank you! Your message has been sent to Sasith & the Alice House team. We will reply shortly.');
    } else {
        setFlash('danger', 'Please fill in all required fields.');
    }
}
?>

<div class="container" style="padding-top: 40px; padding-bottom: 70px;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;">
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a> &rsaquo; 
        <span style="color: var(--text-dark);">Contact & Location</span>
    </div>

    <div style="text-align: center; max-width: 650px; margin: 0 auto 40px;">
        <span class="section-subtitle">Get in Touch</span>
        <h1 style="color: var(--primary); font-size: 2.3rem; font-weight: 800; margin-bottom: 12px;">We'd Love to Hear From You</h1>
        <p style="color: var(--text-muted); font-size: 1rem;">Have questions about room availability, scooter rentals, airport transfers, or surf conditions in Hiriketiya? Drop us a message!</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
        <!-- Contact Information & Details -->
        <div style="background: #fff; padding: 35px; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
            <h3 style="color: var(--primary); font-size: 1.4rem; margin-bottom: 20px;">Property Information</h3>
            
            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="font-size: 24px; color: var(--ocean);">📍</div>
                <div>
                    <strong style="color: var(--primary);">Address</strong>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 4px;">Wedagedara, Dodampahala, Dikwella, Matara District, Southern Province, Sri Lanka</p>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="font-size: 24px; color: var(--ocean);">📞</div>
                <div>
                    <strong style="color: var(--primary);">Phone & WhatsApp</strong>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 4px;">
                        <a href="https://wa.me/94771234567" target="_blank" style="color: var(--primary); font-weight: 600;">+94 77 123 4567</a> (Sasith)
                    </p>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="font-size: 24px; color: var(--ocean);">✉️</div>
                <div>
                    <strong style="color: var(--primary);">Email Address</strong>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 4px;">info@alicehousehiriketiya.com</p>
                </div>
            </div>

            <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                <div style="font-size: 24px; color: var(--ocean);">🕒</div>
                <div>
                    <strong style="color: var(--primary);">Check-in & Check-out</strong>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 4px;">Check-in: 2:00 PM – 10:00 PM | Check-out: 11:00 AM</p>
                </div>
            </div>

            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border);">
                <a href="https://www.booking.com/Share-aINqUA" target="_blank" rel="noopener noreferrer" class="booking-badge-btn" style="width: 100%; justify-content: center; height: 44px;">
                    ★ View Official Booking.com Listing ↗
                </a>
            </div>
        </div>

        <!-- Contact Form -->
        <div style="background: #fff; padding: 35px; border-radius: var(--radius-md); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
            <h3 style="color: var(--primary); font-size: 1.4rem; margin-bottom: 20px;">Send a Direct Message</h3>

            <form method="POST" action="<?php echo BASE_URL; ?>/contact.php">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="name">Your Name *</label>
                    <input type="text" name="name" id="name" class="form-control" required placeholder="Kasun / Traveler">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="email">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" required placeholder="you@domain.com">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="phone">Phone / WhatsApp</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="+94 ...">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" class="form-control" placeholder="Room availability, airport taxi, etc.">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="message">Message *</label>
                    <textarea name="message" id="message" rows="4" class="form-control" required placeholder="Write your inquiry or question here..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; height: 46px;">
                    Send Message to Alice House
                </button>
            </form>
        </div>
    </div>

    <!-- Map Embed -->
    <div style="margin-top: 50px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-md);">
        <iframe 
            src="https://maps.google.com/maps?q=Hiriketiya%20Beach,%20Dikwella,%20Sri%20Lanka&t=&z=14&ie=UTF8&iwloc=&output=embed" 
            style="width: 100%; height: 400px; border: none;"
            allowfullscreen="" 
            loading="lazy">
        </iframe>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
