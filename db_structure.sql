-- ==============================================================
-- Alice House Hiriketiya – Accommodation Booking Management System
-- Database Schema & Initial Seed Data
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `alice_house_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `alice_house_db`;

-- Drop existing tables if re-importing
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `inquiries`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(30) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('guest', 'admin') NOT NULL DEFAULT 'guest',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: rooms
-- --------------------------------------------------------
CREATE TABLE `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_name` VARCHAR(150) NOT NULL,
  `room_type` VARCHAR(60) NOT NULL,
  `description` TEXT NOT NULL,
  `price_per_night` DECIMAL(10,2) NOT NULL,
  `capacity` INT NOT NULL DEFAULT 2,
  `bed_type` VARCHAR(80) NOT NULL,
  `room_size` VARCHAR(40) DEFAULT '25 sqm',
  `primary_image` VARCHAR(255) NOT NULL,
  `gallery` TEXT NULL,
  `facilities` TEXT NOT NULL,
  `status` ENUM('available', 'maintenance', 'unavailable') NOT NULL DEFAULT 'available',
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: bookings
-- --------------------------------------------------------
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_reference` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `room_id` INT NOT NULL,
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `guests` INT NOT NULL DEFAULT 1,
  `total_nights` INT NOT NULL,
  `price_per_night` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `special_requests` TEXT NULL,
  `status` ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
  `payment_status` ENUM('unpaid', 'paid', 'refunded') NOT NULL DEFAULT 'unpaid',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bookings_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: payments
-- --------------------------------------------------------
CREATE TABLE `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(60) NOT NULL DEFAULT 'Cash on Arrival',
  `payment_status` ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'completed',
  `transaction_reference` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: reviews
-- --------------------------------------------------------
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT NULL,
  `room_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `rating` INT NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `status` ENUM('approved', 'pending') NOT NULL DEFAULT 'approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reviews_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: inquiries (Contact form messages)
-- --------------------------------------------------------
CREATE TABLE `inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `subject` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'replied') NOT NULL DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================
-- Seed Data
-- ==============================================================

-- 1. Insert Users (Password for admin: 'admin123', Password for guests: 'guest123')
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`) VALUES
(1, 'Sasith Fernando (Admin)', 'admin@alicehouse.com', '+94 77 123 4567', '$2y$10$ksk.1EeB1u547agmt4Rz7eY6JJSKxNLBJjuWaXTn4JGrYN6tk5fQG', 'admin'),
(2, 'John Doe', 'guest@gmail.com', '+94 71 987 6543', '$2y$10$DHOFkh.e4tk5uyShWWP2Z.jTA12kfsKXsEfIXN2SZTSjuzP09BSC.', 'guest'),
(3, 'Elena Rostova', 'elena@travelworld.com', '+44 7911 123456', '$2y$10$DHOFkh.e4tk5uyShWWP2Z.jTA12kfsKXsEfIXN2SZTSjuzP09BSC.', 'guest');

-- 2. Insert Rooms (Based on Alice House Hiriketiya Booking.com listing)
INSERT INTO `rooms` (`id`, `room_name`, `room_type`, `description`, `price_per_night`, `capacity`, `bed_type`, `room_size`, `primary_image`, `gallery`, `facilities`, `status`, `featured`) VALUES
(1, 'Deluxe Double Room with Garden View', 'Deluxe Double', 'Designed for couples and solo adventurers, this air-conditioned room features an authentic four-poster canopy bed with mosquito netting, private modern en-suite bathroom with hot water shower, soundproofing, and private entrance. Step outside onto your private patio surrounded by peaceful tropical trees and birds chirping.', 30.00, 2, '1 Large Queen Bed', '24 sqm', 'double-room.jpg', '["double-room.jpg", "deluxe-double-night.jpg", "deluxe-double-detail.jpg", "bathroom.jpg", "patio-terrace.jpg"]', 'Air Conditioning, Four-Poster Canopy Bed, Mosquito Net, High-speed Free WiFi, Private Modern Bathroom, Hot Water Shower, Work Desk & Chair, Garden Veranda Access, Free Private Parking', 'available', 1),

(2, 'Superior Triple Room with Balcony', 'Superior Triple', 'A bright, airy tropical suite featuring an authentic stone accent wall, sloped wood ceiling, one queen bed and one single bed, private en-suite bathroom, and a spacious private stone balcony overlooking the serene green hills and coconut palms of Dodampahala. Perfect for small families or surfing buddies.', 42.00, 3, '1 Queen Bed + 1 Single Bed', '32 sqm', 'triple-room.jpg', '["triple-room.jpg", "balcony.jpg", "bathroom.jpg", "exterior-day.jpg", "exterior-night.jpg"]', 'Air Conditioning, Private Stone Balcony with Nature View, Sloped Wood Ceiling, High-speed Free WiFi, Private Modern Bathroom, Hot Water Shower, Work Desk, Ceiling Fan, Free Parking', 'available', 1),

(3, 'Two-Bedroom Family Suite with Kitchen', 'Family Suite', 'Spacious family accommodation with full self-catering kitchen facilities, comfortable double beds, private dining area, modern bathroom, and direct garden veranda access. Ideal for longer stays, digital nomads, and families visiting Hiriketiya beach.', 55.00, 4, '2 Large Double Beds', '45 sqm', 'family-room.jpg', '["family-room.jpg", "patio-terrace.jpg", "bathroom.jpg", "exterior-day.jpg", "exterior-night.jpg"]', 'Air Conditioning, Modern Wooden Double Bed, Wood Panel Ceiling, High-speed Free WiFi, Private Bathroom, Hot Water, Ground Garden Veranda, Dining Table, Refrigerator, Free Parking', 'available', 1),

(4, 'Deluxe King Room with Outdoor Terrace', 'Deluxe King', 'An exquisite sanctuary offering an extra-large king bed, open-air private sun terrace, soothing garden views, rain shower bathroom, and dedicated workspace. Only a gentle 10-minute walk through tropical greenery to the famous Hiriketiya bay.', 35.00, 2, '1 Extra-Large King Bed', '28 sqm', 'king-terrace.jpg', '["king-terrace.jpg", "balcony.jpg", "bathroom.jpg", "patio-terrace.jpg", "exterior-day.jpg"]', 'Air Conditioning, Luxury Wooden King Bed, Wood Panel Ceiling, Private Terrace/Balcony, High-speed Free WiFi, Luxury Rain Shower, Hot Water, Mini Fridge, Work Desk, Free Parking', 'available', 1);

-- 3. Insert Sample Bookings
INSERT INTO `bookings` (`id`, `booking_reference`, `user_id`, `room_id`, `check_in`, `check_out`, `guests`, `total_nights`, `price_per_night`, `total_price`, `special_requests`, `status`, `payment_status`) VALUES
(1, 'AH-202611-101', 2, 1, '2026-11-20', '2026-11-24', 2, 4, 30.00, 120.00, 'We will arrive around 2:00 PM by tuk-tuk. Please arrange scooter rental.', 'confirmed', 'paid'),
(2, 'AH-202611-102', 3, 2, '2026-11-25', '2026-11-28', 3, 3, 42.00, 126.00, 'Would love Sri Lankan breakfast prepared on the first morning!', 'confirmed', 'paid'),
(3, 'AH-202611-103', 2, 3, '2026-12-01', '2026-12-05', 4, 4, 55.00, 220.00, 'Quiet room preferred for remote working.', 'pending', 'unpaid');

-- 4. Insert Sample Payments
INSERT INTO `payments` (`id`, `booking_id`, `amount`, `payment_method`, `payment_status`, `transaction_reference`) VALUES
(1, 1, 120.00, 'Cash on Arrival', 'completed', 'TXN-AH-89341'),
(2, 2, 126.00, 'Credit Card (Visa)', 'completed', 'TXN-AH-98214');

-- 5. Insert Sample Reviews
INSERT INTO `reviews` (`id`, `booking_id`, `room_id`, `user_id`, `rating`, `comment`, `status`) VALUES
(1, 1, 1, 2, 5, 'Sasith and his family were the most welcoming hosts in Sri Lanka! The room was spotless, AC was ice cold, and breakfast was unbelievable. Hiriketiya beach is just a short pleasant walk away. 10/10!', 'approved'),
(2, 2, 2, 3, 5, 'Very peaceful place away from the noisy main street, but close enough to walk to the surf break. Clean modern bathroom with great hot water pressure. Highly recommended!', 'approved'),
(3, NULL, 4, 2, 5, 'Outstanding hospitality. The garden is lush with peacocks and monkeys passing through in the mornings. Loved the home-cooked Sri Lankan string hoppers and papaya juice!', 'approved');

-- 6. Insert Sample Contact Inquiry
INSERT INTO `inquiries` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`) VALUES
(1, 'Michael Brown', 'michael.b@surfquest.com', '+61 400 123 456', 'Surfboard storage and long stay discount', 'Hi Sasith, we are planning a 2-week surf trip to Hiriketiya in January. Do you offer monthly/weekly discounts and safe board storage?', 'read');
