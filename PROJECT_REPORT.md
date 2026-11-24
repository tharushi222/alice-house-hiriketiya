# Alice House Hiriketiya – Accommodation Booking Management System 🏝️
## Academic Project Report (Maximum 5-Page Specification)

---

### PAGE 1: TITLE & EXECUTIVE SUMMARY

#### Project Title:
**Alice House Hiriketiya – Dynamic Accommodation Booking Management System**

* **Real-World Case Study:** Booking.com Listing (Rating: 9.4/10 "Exceptional")
* **Location:** Wedagedara, Dodampahala, Dikwella, Matara District, Southern Province, Sri Lanka
* **Target Business:** Boutique homestay & self-catering villa guesthouse

#### 1. Executive Summary
The tourism and hospitality sector in Sri Lanka's southern coastal belt—particularly around the world-famous horseshoe surfing haven of Hiriketiya Beach—has experienced rapid growth in boutique homestays. **Alice House Hiriketiya** is an acclaimed, family-run accommodation located in Wedagedara, Dodampahala, Dikwella, situated just 10 to 12 minutes walk from the beach.

While Alice House maintains an exceptional 9.4/10 rating on Booking.com, relying solely on third-party OTAs incurs hefty 15%–20% commission deductions and restricts direct guest communications. This project delivers a standalone, dynamic, web-based Accommodation Booking Management System tailored specifically to Alice House Hiriketiya.

#### 2. Problem Statement & Objectives
* **Challenges:** High OTA commissions, risk of manual double-bookings, lack of dynamic live costing in USD & LKR, manual room inventory updates.
* **Objectives:** 
  1. Build a dynamic web booking engine with conflict detection.
  2. Provide guest authentication and a self-service reservation portal.
  3. Empower guesthouse hosts with a comprehensive Admin Dashboard.
  4. Ensure 100% compatibility with local XAMPP environments (Apache, PHP, MySQL).

#### 3. Scope of the System
* **Public Guest Portal:** Hotel overview, room catalog with high-res photos and facilities, date availability search, dynamic cost calculator, reservation checkout, guest account management, and verified reviews.
* **Admin Management Console:** Analytical KPI dashboard (Revenue, Bookings, Active Rooms, Registered Guests), Room CRUD with photo uploads, reservation workflow management, and review moderation.

---

### PAGE 2: SYSTEM ARCHITECTURE & TECHNOLOGIES

#### 4. System Architecture
The system employs a classic **3-Tier MVC-oriented Web Architecture**:
1. **Presentation Tier:** HTML5, Responsive CSS3 (custom variables, flexbox, grid), Vanilla JavaScript ES6 (date validation, dynamic price calculator, gallery viewer).
2. **Application Tier:** PHP 8.2 with PDO prepared statements, session-based authentication guards, and business validation rules.
3. **Data Tier:** MySQL / MariaDB (InnoDB engine) with normalized relational tables, foreign key constraints, and indexing.

#### 5. Technology Stack Summary
* **HTML5:** Semantic, accessible form fields and layout structure.
* **CSS3:** Custom tropical boutique styling tailored to Sri Lankan coastal aesthetic; fully responsive across mobile, tablet, and desktop.
* **JavaScript (ES6):** Client-side date logic (`checkout > checkin >= today`), live subtotal and night calculations, gallery thumbnail switcher.
* **PHP 8.2 (PDO):** Secure backend processing, Bcrypt password hashing, session management, and SQL injection prevention.
* **MySQL / MariaDB:** Relational database storage with ACID compliance.
* **XAMPP Local Server:** Cross-platform local execution on Windows.

#### 6. Core Modules
* **Guest Authentication:** Registration, Login, Session handling, Profile access.
* **Room Availability Search Engine:** Real-time date overlapping query (`check_in < req_checkout AND check_out > req_checkin`).
* **Dynamic Cost Calculation Engine:** Real-time calculation of nights, rates, and LKR conversions.
* **Admin Property Governance:** Comprehensive room CRUD, booking approvals, status toggles, and user moderation.

---

### PAGE 3: DATABASE ARCHITECTURE & RELATIONAL DESIGN

#### 7. Entity-Relationship (ER) Schema
The relational database `alice_house_db` is normalized up to 3NF:
* `users` 1 ─── * `bookings` * ─── 1 `rooms`
* `users` 1 ─── * `reviews` * ─── 1 `rooms`
* `bookings` 1 ─── 1 `payments`

#### 8. Data Dictionary

| Table | Primary Key | Foreign Keys | Key Attributes | Description |
| :--- | :--- | :--- | :--- | :--- |
| **users** | `id` | - | `full_name`, `email` (UK), `phone`, `password`, `role` | User accounts (Guests & Admins) |
| **rooms** | `id` | - | `room_name`, `room_type`, `price_per_night`, `capacity`, `bed_type`, `status` | Accommodation inventory & details |
| **bookings** | `id` | `user_id`, `room_id` | `booking_reference` (UK), `check_in`, `check_out`, `total_price`, `status` | Reservation records |
| **payments** | `id` | `booking_id` | `amount`, `payment_method`, `payment_status` | Payment transaction records |
| **reviews** | `id` | `room_id`, `user_id` | `rating` (1-5), `comment`, `status` | Verified guest ratings & feedback |
| **inquiries** | `id` | - | `name`, `email`, `phone`, `subject`, `message` | Customer contact inquiries |

#### 9. Integrity Constraints
* `ON DELETE CASCADE` on all foreign key constraints ensures zero orphaned records.
* Unique index on `users.email` and `bookings.booking_reference`.

---

### PAGE 4: IMPLEMENTATION & USER INTERFACE WORKFLOWS

#### 10. Functional Workflows
* **Workflow A: Room Search & Discovery:** Guest enters dates and guest count. The system checks availability and presents real-time badges (*Available* or *Booked on Dates*).
* **Workflow B: Direct Reservation Checkout:** Guest selects a room, reviews amenities, notes special requests (e.g. scooter rental), and submits the booking. A unique booking reference (e.g. `AH-202611-1042`) is generated with instant confirmation.
* **Workflow C: Guest Booking Management:** Guest accesses "My Bookings" to view reservation status (*Pending*, *Confirmed*, *Cancelled*, *Completed*) or cancel reservations.
* **Workflow D: Admin Operations:** Admin views KPI metrics, manages room inventory (Add, Edit, Delete, Toggle Availability), and updates reservation statuses.

#### 11. Interface & Page Inventory
1. `index.php`: Home page with hero banner, search bar, highlights, gallery, reviews, and map.
2. `rooms.php`: Rooms catalog with date, price, and category filters.
3. `room_details.php`: Room profile, interactive photo gallery, amenities, and live booking widget.
4. `book.php`: Secure checkout with cost breakdown and guest pre-fill.
5. `my_bookings.php`: Guest reservation portal with cancellation handling.
6. `admin/index.php`: Property dashboard with KPI revenue and booking statistics.
7. `admin/rooms.php`: Room CRUD table and Add Room form with photo upload.
8. `admin/bookings.php`: Reservation management with search and status updater.

---

### PAGE 5: TESTING, SECURITY & CONCLUSION

#### 12. Verification & Testing Matrix

| Test ID | Test Scenario | Expected Result | Result |
| :--- | :--- | :--- | :--- |
| **TC-01** | Guest registration | Account created, Bcrypt hashed password, session started. | **PASSED** |
| **TC-02** | Duplicate email registration | System rejects duplicate with friendly error alert. | **PASSED** |
| **TC-03** | Date constraints validation | Checkout must be greater than Check-in, Check-in >= Today. | **PASSED** |
| **TC-04** | Double-booking prevention | Dates overlapping with confirmed booking rejected. | **PASSED** |
| **TC-05** | Dynamic price calculation | JS live subtotal matches server-side computation. | **PASSED** |
| **TC-06** | Guest booking cancellation | Guest cancels booking; status updates to 'cancelled'. | **PASSED** |
| **TC-07** | Admin authorization guard | Non-admin redirected from `/admin/` with access denied. | **PASSED** |

#### 13. Security Implementations
* **SQL Injection Prevention:** 100% PDO prepared statements with parameter binding.
* **XSS Sanitization:** All user inputs escaped using `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
* **Session Protection:** Session fixation prevented via `session_regenerate_id(true)`.
* **Password Security:** One-way Bcrypt cryptographic hashing (`PASSWORD_BCRYPT`).

#### 14. Conclusion & Recommendations
The Alice House Hiriketiya Accommodation Booking Management System successfully transitions the property from reliance on high-commission OTAs into an independent, direct-booking business. 

**Future Enhancements:**
1. Integration with online payment gateways (Stripe / PayHere Sri Lanka).
2. Two-way iCal synchronization with Booking.com and Airbnb.
3. Automated WhatsApp reservation alerts.
