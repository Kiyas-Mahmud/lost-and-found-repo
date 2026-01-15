-- Additional Seed Data for Lost & Found Platform
-- Run this to add more test data without dropping existing database

-- Additional Test Student Users (if they don't exist yet)
INSERT IGNORE INTO users (full_name, email, student_id, phone, password_hash, role, account_status) VALUES
('Sarah Williams', 'sarah.williams@university.edu', 'STU004', '9876543213', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE'),
('David Brown', 'david.brown@university.edu', 'STU005', '9876543214', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE'),
('Emily Davis', 'emily.davis@university.edu', 'STU006', '9876543215', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE'),
('Robert Wilson', 'robert.wilson@university.edu', 'STU007', '9876543216', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE'),
('Lisa Anderson', 'lisa.anderson@university.edu', 'STU008', '9876543217', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT', 'ACTIVE');

-- Sample Items (Lost & Found Posts)
INSERT INTO items (posted_by, category_id, location_id, item_type, title, description, event_date, contact_info, image_path, current_status, post_type) VALUES
-- LOST items
(2, 3, 1, 'LOST', 'Lost iPhone 13 Pro', 'Black iPhone 13 Pro with cracked screen protector. Has a red case with university sticker on back.', '2024-01-15 14:30:00', '9876543210', 'uploads/phone_1.jpg', 'OPEN', 'LOST'),
(3, 2, 2, 'LOST', 'Lost Brown Leather Wallet', 'Brown leather wallet containing student ID and some cash. Lost near the canteen area.', '2024-01-16 12:00:00', '9876543211', 'uploads/wallet_1.jpg', 'OPEN', 'LOST'),
(4, 4, 3, 'LOST', 'Lost Car Keys with BMW Keychain', 'Set of car keys with BMW keychain and 3 other keys. Lost in Computer Lab.', '2024-01-17 09:15:00', '9876543212', 'uploads/keys_1.jpg', 'OPEN', 'LOST'),
(5, 5, 4, 'LOST', 'Lost Blue Backpack', 'Nike blue backpack with laptop compartment. Contains textbooks and notebook.', '2024-01-18 11:20:00', '9876543213', 'uploads/bag_1.jpg', 'OPEN', 'LOST'),
(6, 1, 5, 'LOST', 'Lost Student ID Card', 'University student ID card for Sarah Williams. Lost in Building B corridor.', '2024-01-19 15:45:00', '9876543214', 'uploads/id_1.jpg', 'OPEN', 'LOST'),

-- FOUND items
(2, 2, 1, 'FOUND', 'Found Black Wallet', 'Found a black leather wallet near library entrance. Contains multiple cards.', '2024-01-14 16:00:00', '9876543210', 'uploads/wallet_2.jpg', 'OPEN', 'FOUND'),
(3, 7, 6, 'FOUND', 'Found Laptop Charger', 'MacBook charger found in parking lot. White color with magnetic connector.', '2024-01-15 08:30:00', '9876543211', 'uploads/charger_1.jpg', 'OPEN', 'FOUND'),
(4, 8, 7, 'FOUND', 'Found Red Jacket', 'Red Nike jacket found at Sports Complex. Size M with university logo.', '2024-01-16 18:00:00', '9876543212', 'uploads/jacket_1.jpg', 'OPEN', 'FOUND'),
(5, 6, 8, 'FOUND', 'Found Calculus Textbook', 'Calculus textbook with notes inside. Found in Auditorium after lecture.', '2024-01-17 14:00:00', '9876543213', 'uploads/book_1.jpg', 'OPEN', 'FOUND'),
(6, 9, 9, 'FOUND', 'Found Silver Watch', 'Silver wristwatch with black strap. Found near Hostel Block 1.', '2024-01-18 07:30:00', '9876543214', 'uploads/watch_1.jpg', 'OPEN', 'FOUND'),
(7, 3, 10, 'FOUND', 'Found Samsung Phone', 'Samsung Galaxy S21 with blue case. Found in Hostel Block 2 common room.', '2024-01-19 20:00:00', '9876543215', 'uploads/phone_2.jpg', 'OPEN', 'FOUND'),
(8, 4, 11, 'FOUND', 'Found House Keys', 'Set of 4 keys with yellow keyring. Found in Garden Area.', '2024-01-20 10:30:00', '9876543216', 'uploads/keys_2.jpg', 'OPEN', 'FOUND'),
(2, 10, 12, 'FOUND', 'Found Student Papers', 'Stack of assignment papers with name partially visible. Found at Main Gate.', '2024-01-21 13:00:00', '9876543217', 'uploads/papers_1.jpg', 'OPEN', 'FOUND'),
(3, 3, 1, 'FOUND', 'Found AirPods', 'Apple AirPods Pro in charging case. Found in Library study area.', '2024-01-22 11:00:00', '9876543210', 'uploads/airpods_1.jpg', 'OPEN', 'FOUND'),
(4, 5, 2, 'FOUND', 'Found Gym Bag', 'Black Adidas gym bag with towel and water bottle inside. Found in Canteen.', '2024-01-23 15:30:00', '9876543211', 'uploads/bag_2.jpg', 'OPEN', 'FOUND');

-- Claims (Some pending, some approved, some rejected)
INSERT INTO claims (item_id, claimed_by, claim_status, proof_answer_1, proof_answer_2, admin_note, created_at) VALUES
-- PENDING Claims (need review)
(6, 3, 'PENDING', 'Black leather with zipper pocket', 'Visa card ending in 4532 and library card', NULL, '2024-01-15 09:00:00'),
(7, 4, 'PENDING', '85W MagSafe 2', 'Left side of parking lot near entrance', NULL, '2024-01-16 10:00:00'),
(9, 5, 'PENDING', 'Calculus 3rd Edition by James Stewart', 'Yellow highlighter marks on Chapter 3', NULL, '2024-01-17 15:30:00'),
(10, 6, 'PENDING', 'Casio brand with date display', 'Small scratch on the glass face', NULL, '2024-01-18 11:00:00'),
(11, 7, 'PENDING', 'Blue Spigen case with card holder', 'Lock screen has photo of a dog', NULL, '2024-01-19 14:00:00'),

-- APPROVED Claims
(1, 2, 'APPROVED', 'iPhone 13 Pro 256GB Space Gray', 'Red Spigen case with university sticker', 'Verified via IMEI number. Item returned.', '2024-01-16 16:00:00'),
(8, 3, 'APPROVED', 'Red Nike with white stripes, Size M', 'Receipt in pocket from campus bookstore', 'Owner provided purchase receipt.', '2024-01-17 10:00:00'),

-- REJECTED Claims  
(6, 5, 'REJECTED', 'Brown wallet', 'Contains some cash', 'Description does not match. Wallet is black not brown.', '2024-01-15 11:00:00'),
(10, 8, 'REJECTED', 'Generic watch', 'Found yesterday', 'Insufficient details provided.', '2024-01-18 13:00:00');

-- Reports (Some open, some resolved)
INSERT INTO reports (item_id, reported_by, report_reason, report_status, admin_response, created_at) VALUES
-- OPEN Reports
(1, 3, 'This post has been up for too long without update', 'OPEN', NULL, '2024-01-20 10:00:00'),
(5, 4, 'The description is too vague and might be a spam post', 'OPEN', NULL, '2024-01-21 14:00:00'),
(12, 5, 'Duplicate post - same item posted twice', 'OPEN', NULL, '2024-01-22 09:00:00'),
(13, 6, 'Suspicious post with fake contact information', 'OPEN', NULL, '2024-01-23 11:30:00'),

-- RESOLVED Reports
(2, 4, 'Inappropriate description language', 'RESOLVED', 'Contacted poster. Description has been edited.', '2024-01-19 08:00:00'),
(3, 5, 'Post contains duplicate information', 'RESOLVED', 'Post reviewed. No violation found.', '2024-01-20 16:00:00');
