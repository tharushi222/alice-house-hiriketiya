# Alice House Hiriketiya – Accommodation Booking System 🏝️

A web-based booking system for **Alice House Hiriketiya**, a homestay in Dikwella, Sri Lanka (9.4/10 on [Booking.com](https://www.booking.com/Share-aINqUA)). Guests can browse rooms, check availability and book directly. The host manages everything from an admin panel.

## Features

**Guests**
- Room catalog with photos, amenities and prices in USD and LKR
- Availability search by dates, guests and room type (overlap detection)
- Live cost calculator and instant booking with a unique reference (`AH-YYYYMM-XXXX`)
- Register / login, "My Bookings" page with cancellation, room reviews
- Contact form and Google Map

**Admin**
- Dashboard with revenue, bookings, rooms and guest stats
- Room management (add, edit, delete, photo upload, status toggle)
- Booking management (search, filter, update booking and payment status)
- User list and review moderation

## Tech Stack

| Layer | Technology |
| :--- | :--- |
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Backend | PHP 8.x (PDO, sessions, bcrypt) |
| Database | MySQL / MariaDB (6 tables: `users`, `rooms`, `bookings`, `payments`, `reviews`, `inquiries`) |
| Server | Apache (XAMPP) |

## Setup (XAMPP)

1. Copy the project folder to `C:\xampp\htdocs\alice_house` (or run `deploy_to_xampp.bat`).
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin/`, go to **Import**, and import `db_structure.sql` (creates `alice_house_db` with sample data).
4. Visit `http://localhost/alice_house/`.

## Demo Accounts

| Role | Email | Password |
| :--- | :--- | :--- |
| Admin | `admin@alicehouse.com` | `admin123` |
| Guest | `guest@gmail.com` | `guest123` |
| Guest | `elena@travelworld.com` | `guest123` |

## Project Structure

```
alice_house/
├── index.php, rooms.php, room_details.php   # Browse & search rooms
├── book.php, my_bookings.php                # Booking & guest dashboard
├── register.php, login.php, logout.php      # Authentication
├── about.php, contact.php                   # Info & inquiries
├── db_structure.sql                         # Database schema + seed data
├── includes/                                # db, auth, functions, header, footer
├── assets/                                  # css, js, images
├── uploads/rooms/                           # Uploaded room photos
└── admin/                                   # Admin panel (dashboard, rooms, bookings, users, reviews)
```

## Reference
- Booking.com listing: https://www.booking.com/Share-aINqUA
- Location: Wedagedara, Dodampahala, Dikwella, Matara District, Sri Lanka
