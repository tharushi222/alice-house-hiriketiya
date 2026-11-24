<?php
/**
 * Home Page - Alice House Hiriketiya
 */
$pageTitle = "Welcome to Alice House Hiriketiya – Tropical Boutique Homestay";
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// Fetch featured rooms
$stmtRooms = $db->query("SELECT * FROM rooms WHERE status = 'available' ORDER BY featured DESC, price_per_night ASC LIMIT 4");
$featuredRooms = $stmtRooms->fetchAll();

// Fetch approved guest reviews
$stmtReviews = $db->query("SELECT r.*, u.full_name, rm.room_name 
                           FROM reviews r
                           JOIN users u ON r.user_id = u.id
                           JOIN rooms rm ON r.room_id = rm.id
                           WHERE r.status = 'approved'
                           ORDER BY r.id DESC LIMIT 3");
$reviews = $stmtReviews->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-badge">🌴 Top Rated Homestay in Hiriketiya • 9.4/10</div>
        <h1>Alice House Hiriketiya</h1>
        <p>Your peaceful tropical retreat nestled among swaying palms and lush nature in Dodampahala, just 10 minutes from the world-famous Hiriketiya surf bay.</p>
        <div style="display:flex; justify-content:center; gap:14px; flex-wrap:wrap;">
            <a href="<?php echo BASE_URL; ?>/rooms.php" class="btn btn-lg btn-accent">Explore Rooms & Rates</a>
            <a href="https://www.booking.com/Share-aINqUA" target="_blank" rel="noopener noreferrer" class="btn btn-lg btn-outline" style="border-color:#fff; color:#fff;">View on Booking.com ↗</a>
        </div>
    </div>
</section>

<!-- Quick Availability Search Bar -->
<div class="container search-bar-wrap">
    <div class="search-box">
        <form action="<?php echo BASE_URL; ?>/rooms.php" method="GET" class="search-form">
            <div class="form-group">
                <label for="check_in">Check-in Date</label>
                <input type="date" name="check_in" id="check_in" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label for="check_out">Check-out Date</label>
                <input type="date" name="check_out" id="check_out" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
            </div>
            <div class="form-group">
                <label for="guests">Guests</label>
                <select name="guests" id="guests" class="form-control">
                    <option value="1">1 Guest</option>
                    <option value="2" selected>2 Guests</option>
                    <option value="3">3 Guests</option>
                    <option value="4">4 Guests</option>
                </select>
            </div>
            <div class="form-group">
                <label for="type">Room Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="">All Room Types</option>
                    <option value="Deluxe Double">Deluxe Double</option>
                    <option value="Superior Triple">Superior Triple</option>
                    <option value="Family Suite">Family Suite with Kitchen</option>
                    <option value="Deluxe King">Deluxe King with Terrace</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height: 48px;">
                🔍 Check Availability
            </button>
        </form>
    </div>
</div>

<!-- Hotel Highlights Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Why Guests Love Us</span>
            <h2 class="section-title">An Authentic Tropical Homestay Experience</h2>
            <p class="section-desc">Experience genuine Sri Lankan warmth with modern comfort, tranquil garden surroundings, and immediate access to Hiriketiya's vibrant beach culture.</p>
        </div>

        <div class="features-grid">
            <div class="feature-box">
                <div class="feature-icon">🏄‍♂️</div>
                <h3 class="feature-title">Near Hiriketiya Beach</h3>
                <p class="feature-desc">A scenic 10-12 minute stroll or a 2-minute tuk-tuk ride to the pristine surf bay and popular cafes.</p>
            </div>

            <div class="feature-box">
                <div class="feature-icon">🌿</div>
                <h3 class="feature-title">Peaceful Green Sanctuary</h3>
                <p class="feature-desc">Wake up to tropical birds and monkeys in our serene garden, away from the dusty main road noises.</p>
            </div>

            <div class="feature-box">
                <div class="feature-icon">❄️</div>
                <h3 class="feature-title">Modern AC & Hot Water</h3>
                <p class="feature-desc">All rooms are equipped with whisper-quiet air conditioning, private en-suite bathrooms, and high-pressure hot water.</p>
            </div>

            <div class="feature-box">
                <div class="feature-icon">🍳</div>
                <h3 class="feature-title">Kitchen & Local Breakfast</h3>
                <p class="feature-desc">Access to self-catering kitchen facilities and delicious homemade traditional Sri Lankan breakfast on request.</p>
            </div>

            <div class="feature-box">
                <div class="feature-icon">📶</div>
                <h3 class="feature-title">High-Speed Wi-Fi</h3>
                <p class="feature-desc">Reliable fiber-optic internet connection across the rooms and terrace, perfect for remote workers and digital nomads.</p>
            </div>

            <div class="feature-box">
                <div class="feature-icon">🛵</div>
                <h3 class="feature-title">Scooters & Tuk-Tuk</h3>
                <p class="feature-desc">Hassle-free on-site scooter rentals, airport transfers, and local safari/surf trips arranged directly by host Sasith.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Rooms Section -->
<section class="section" style="background-color: #f3f5f4;">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Accommodations</span>
            <h2 class="section-title">Comfortable Rooms & Suites</h2>
            <p class="section-desc">Spotlessly clean rooms designed with thoughtful amenities, private balconies, and garden views.</p>
        </div>

        <div class="rooms-grid">
            <?php foreach ($featuredRooms as $room): ?>
                <?php 
                    $ratingInfo = getRoomRatingSummary($room['id']); 
                    $imagePath = BASE_URL . '/assets/images/' . $room['primary_image'];
                    // check if uploaded image
                    if (!file_exists(__DIR__ . '/assets/images/' . $room['primary_image'])) {
                        $imagePath = BASE_URL . '/uploads/rooms/' . $room['primary_image'];
                    }
                ?>
                <div class="room-card">
                    <div class="room-image-wrap">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($room['room_name']); ?>">
                        <div class="room-badge"><?php echo htmlspecialchars($room['room_type']); ?></div>
                        <div class="room-price-tag">
                            $<?php echo number_format($room['price_per_night'], 0); ?>
                            <small>/ night</small>
                        </div>
                    </div>
                    <div class="room-content">
                        <h3 class="room-title">
                            <a href="<?php echo BASE_URL; ?>/room_details.php?id=<?php echo $room['id']; ?>">
                                <?php echo htmlspecialchars($room['room_name']); ?>
                            </a>
                        </h3>
                        
                        <div class="room-meta">
                            <div class="room-meta-item">👥 Up to <?php echo $room['capacity']; ?> Guests</div>
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

                        <div class="room-footer">
                            <div>
                                <?php echo renderStars($ratingInfo['avg']); ?>
                                <span class="price-lkr">≈ LKR <?php echo number_format($room['price_per_night'] * 310); ?> / night</span>
                            </div>
                            <a href="<?php echo BASE_URL; ?>/room_details.php?id=<?php echo $room['id']; ?>" class="btn btn-sm btn-primary">
                                View Details & Book
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="<?php echo BASE_URL; ?>/rooms.php" class="btn btn-outline btn-lg">View All Accommodations & Rates &rarr;</a>
        </div>
    </div>
</section>

<!-- Photo Gallery Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Property Gallery</span>
            <h2 class="section-title">A Glimpse into Alice House</h2>
            <p class="section-desc">Experience the tranquil ambiance, garden scenery, and nearby Hiriketiya beach coastline.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
            <div style="position: relative; height: 220px; overflow: hidden; border-radius: 12px; box-shadow: var(--shadow-sm);">
                <img src="<?php echo BASE_URL; ?>/assets/images/exterior-day.jpg" alt="Alice House Villa Exterior" style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Villa & Tropical Garden</span>
            </div>
            <div style="position: relative; height: 220px; overflow: hidden; border-radius: 12px; box-shadow: var(--shadow-sm);">
                <img src="<?php echo BASE_URL; ?>/assets/images/exterior-night.jpg" alt="Villa Exterior Night" style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Night Ambiance & Veranda</span>
            </div>
            <div style="position: relative; height: 220px; overflow: hidden; border-radius: 12px; box-shadow: var(--shadow-sm);">
                <img src="<?php echo BASE_URL; ?>/assets/images/double-room.jpg" alt="Deluxe Canopy Bed" style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Deluxe Canopy Room</span>
            </div>
            <div style="position: relative; height: 220px; overflow: hidden; border-radius: 12px; box-shadow: var(--shadow-sm);">
                <img src="<?php echo BASE_URL; ?>/assets/images/triple-room.jpg" alt="Superior Stone-Wall Room" style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Superior Stone-Wall Suite</span>
            </div>
            <div style="position: relative; height: 220px; overflow: hidden; border-radius: 12px; box-shadow: var(--shadow-sm);">
                <img src="<?php echo BASE_URL; ?>/assets/images/king-terrace.jpg" alt="Deluxe King Room" style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Deluxe King Suite (AC)</span>
            </div>
            <div style="position: relative; height: 220px; overflow: hidden; border-radius: 12px; box-shadow: var(--shadow-sm);">
                <img src="<?php echo BASE_URL; ?>/assets/images/balcony.jpg" alt="Upper Floor Balcony" style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Private Stone Balcony</span>
            </div>
            <div style="position: relative; height: 220px; overflow: hidden; border-radius: 12px; box-shadow: var(--shadow-sm);">
                <img src="<?php echo BASE_URL; ?>/assets/images/patio-terrace.jpg" alt="Ground Veranda" style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Ground Garden Veranda</span>
            </div>
            <div style="position: relative; height: 220px; overflow: hidden; border-radius: 12px; box-shadow: var(--shadow-sm);">
                <img src="<?php echo BASE_URL; ?>/assets/images/bathroom.jpg" alt="Modern Bathroom" style="width:100%; height:100%; object-fit:cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.65); color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Modern Marble Bathroom</span>
            </div>
        </div>
    </div>
</section>

<!-- Guest Reviews Section -->
<section class="section" style="background-color: #f3f5f4;">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Real Traveler Feedback</span>
            <h2 class="section-title">What Our Guests Say</h2>
            <p class="section-desc">Rated 9.4/10 "Exceptional" by international travelers on Booking.com.</p>
        </div>

        <div class="reviews-grid">
            <?php foreach ($reviews as $rev): ?>
                <div class="review-card">
                    <div>
                        <?php echo renderStars($rev['rating']); ?>
                        <p class="review-comment">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                    </div>
                    <div class="review-author">
                        <div class="author-avatar"><?php echo strtoupper(substr($rev['full_name'], 0, 1)); ?></div>
                        <div class="author-info">
                            <h4><?php echo htmlspecialchars($rev['full_name']); ?></h4>
                            <span>Stayed in <?php echo htmlspecialchars($rev['room_name']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Location & Map Section -->
<section class="section">
    <div class="container">
        <div class="location-box">
            <div class="location-info">
                <span class="section-subtitle">Location & Neighborhood</span>
                <h3>Nestled in Hiriketiya, Dikwella</h3>
                <p>Alice House is located in Wedagedara, Dodampahala, Dikwella, Matara District, Southern Province of Sri Lanka. We are situated in a tranquil neighborhood bordered by coconut palms and tropical fauna.</p>
                <ul style="list-style: none; margin-bottom: 24px; font-size: 0.95rem; color: var(--text-dark);">
                    <li style="margin-bottom: 8px;">🏄 <strong>Hiriketiya Beach:</strong> 10-12 mins walk (famous horseshoe surf bay)</li>
                    <li style="margin-bottom: 8px;">🏖️ <strong>Dickwella Beach:</strong> 15 mins walk (long sandy beach & sunset views)</li>
                    <li style="margin-bottom: 8px;">☕ <strong>Local Cafes & Yoga:</strong> 8-10 mins walk (Dots Bay House, Smoke & Bitters)</li>
                    <li>🚉 <strong>Wewurukannala Temple & Matara:</strong> 15-25 mins drive</li>
                </ul>
                <div>
                    <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-primary">Get Directions & Inquiry</a>
                </div>
            </div>
            <div class="location-map">
                <!-- Google Maps Embed for Dikwella / Hiriketiya Beach -->
                <iframe 
                    src="https://maps.google.com/maps?q=Hiriketiya%20Beach,%20Dikwella,%20Sri%20Lanka&t=&z=14&ie=UTF8&iwloc=&output=embed" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section style="background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; padding: 60px 0; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 12px;">Ready for an Unforgettable Stay in Hiriketiya?</h2>
        <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto 25px; opacity: 0.9;">Book your room directly on our dynamic system to secure instant confirmation, personalized host support, and best rates.</p>
        <a href="<?php echo BASE_URL; ?>/rooms.php" class="btn btn-lg btn-accent">Check Live Availability & Book Direct</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
