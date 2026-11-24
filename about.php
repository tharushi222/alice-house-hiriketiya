<?php
/**
 * About Alice House & Host Story
 */
$pageTitle = "About Alice House – Tropical Homestay in Hiriketiya";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 70px;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;">
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a> &rsaquo; 
        <span style="color: var(--text-dark);">About Alice House Hiriketiya</span>
    </div>

    <!-- Header Banner -->
    <div style="text-align: center; max-width: 750px; margin: 0 auto 50px;">
        <span class="section-subtitle">Our Story & Hospitality</span>
        <h1 style="color: var(--primary); font-size: 2.5rem; font-weight: 800; margin-bottom: 15px;">Welcome to Alice House Hiriketiya</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.7;">A tranquil family-run homestay nestled in lush tropical nature, where authentic Sri Lankan hospitality meets modern comfort.</p>
    </div>

    <!-- Two-column story -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; margin-bottom: 60px;">
        <div>
            <h2 style="color: var(--primary); font-size: 1.8rem; margin-bottom: 16px;">Peaceful Retreat near the Surf</h2>
            <p style="color: var(--text-dark); line-height: 1.8; margin-bottom: 16px;">
                Alice House Hiriketiya was founded with a simple vision: to offer international travelers, surfers, and families a peaceful, genuine home away from home on the southern coast of Sri Lanka. 
            </p>
            <p style="color: var(--text-dark); line-height: 1.8; margin-bottom: 16px;">
                Located in Wedagedara, Dodampahala, Dikwella, our property is tucked away in a quiet green pocket just 10 to 12 minutes walk from the world-famous horseshoe surf bay of Hiriketiya. Here, you wake up to birds singing and monkeys rustling through coconut palms rather than loud roadside traffic.
            </p>
            <p style="color: var(--text-dark); line-height: 1.8;">
                Our host Sasith and the family personally ensure that every guest is treated like family—from cooking hearty traditional Sri Lankan breakfasts to arranging scooter rentals, airport shuttles, and local surf spot tips.
            </p>
        </div>
        <div style="border-radius: var(--radius-md); overflow: hidden; box-shadow: var(--shadow-lg);">
            <img src="<?php echo BASE_URL; ?>/assets/images/garden.jpg" alt="Alice House Garden" style="width: 100%; height: 380px; object-fit: cover;">
        </div>
    </div>

    <!-- Property Features Grid -->
    <div style="background: #fff; border-radius: var(--radius-md); border: 1px solid var(--border); padding: 40px; margin-bottom: 60px; box-shadow: var(--shadow-sm);">
        <h2 style="color: var(--primary); text-align: center; margin-bottom: 35px; font-size: 1.8rem;">What Makes Us Special</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 30px;">
            <div>
                <div style="font-size: 30px; margin-bottom: 10px;">🌴</div>
                <h4 style="color: var(--primary); margin-bottom: 8px;">Tropical Garden</h4>
                <p style="font-size: 0.92rem; color: var(--text-muted);">Spacious green sun terrace with comfortable seating, outdoor dining, and relaxing tropical greenery.</p>
            </div>
            <div>
                <div style="font-size: 30px; margin-bottom: 10px;">⭐</div>
                <h4 style="color: var(--primary); margin-bottom: 8px;">9.4/10 Rated on Booking.com</h4>
                <p style="font-size: 0.92rem; color: var(--text-muted);">Renowned across international booking platforms for cleanliness, comfort, and extraordinary family hospitality.</p>
            </div>
            <div>
                <div style="font-size: 30px; margin-bottom: 10px;">🍳</div>
                <h4 style="color: var(--primary); margin-bottom: 8px;">Self-Catering & Breakfast</h4>
                <p style="font-size: 0.92rem; color: var(--text-muted);">Shared or private kitchen options, plus authentic homemade hoppers, roti, and fresh tropical fruit platters.</p>
            </div>
            <div>
                <div style="font-size: 30px; margin-bottom: 10px;">🏄</div>
                <h4 style="color: var(--primary); margin-bottom: 8px;">Surfer-Friendly</h4>
                <p style="font-size: 0.92rem; color: var(--text-muted);">Secure board storage, outdoor rinse shower, surf lesson recommendations, and scooter rack rentals.</p>
            </div>
        </div>
    </div>

    <!-- Host Card & Booking CTA -->
    <div style="background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; border-radius: var(--radius-md); padding: 40px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 25px;">
        <div style="max-width: 600px;">
            <h3 style="font-size: 1.8rem; margin-bottom: 10px;">Experience Alice House Hiriketiya</h3>
            <p style="opacity: 0.9; font-size: 1rem;">Book your stay directly with us for guaranteed best rates, flexible cancellation, and personal host care.</p>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>/rooms.php" class="btn btn-lg btn-accent">Browse Rooms & Book</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
