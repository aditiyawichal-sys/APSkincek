-- Seed Users (password is 'password123')
INSERT INTO users (id, name, email, phone, password, role) VALUES 
('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Admin User', 'admin@skincheck.local', '081234567890', '$2y$10$eVHEY/TRCD2M4P/TGQD2ZunVfOfnqwSn2yb9y0kwsH6pv.QGIvysy', 'admin'),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'John Doe', 'user@skincheck.local', '089876543210', '$2y$10$eVHEY/TRCD2M4P/TGQD2ZunVfOfnqwSn2yb9y0kwsH6pv.QGIvysy', 'user');

-- Seed Products
INSERT INTO products (id, name, brand, category, description) VALUES
(1, 'Gentle Skin Cleanser', 'Cetaphil', 'cleanser', 'A mild, non-irritating cleanser that leaves skin feeling soft and smooth.'),
(2, 'UV Defender Sunscreen SPF 50', 'L''Oreal', 'sunscreen', 'Daily anti-aging sunscreen with broad spectrum SPF 50.'),
(3, 'Moisturizing Cream', 'CeraVe', 'moisturizer', 'A rich, non-greasy, fast-absorbing moisturizing cream for normal to dry skin.');

-- Seed Skin Scans
INSERT INTO skin_scans (id, user_id, scan_type, image_url, status) VALUES
(1, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'face', 'uploads/sample_face.jpg', 'completed'),
(2, 'b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'sunscreen', 'uploads/sample_uv.jpg', 'completed');

-- Seed Analysis Results
INSERT INTO analysis_results (scan_id, acne_score, hydration_level, overall_health, details) VALUES
(1, 15, 60, 75, '{"acne_spots": 3, "redness": "low", "pores": "normal"}');

-- Seed Sunscreen Results
INSERT INTO sunscreen_results (scan_id, coverage_score, missed_areas) VALUES
(2, 85, '{"forehead": "good", "nose": "missed spots", "cheeks": "excellent"}');

-- Seed Recommendations
INSERT INTO recommendations (scan_id, product_id, reason) VALUES
(1, 1, 'Recommended for your sensitive skin type and mild acne.'),
(2, 2, 'Recommended to improve sunscreen coverage on your nose area.');

-- Seed Education Contents
INSERT INTO education_contents (title, content, category, author_id, status) VALUES
('How to Apply Sunscreen', 'Apply sunscreen generously 15 minutes before sun exposure...', 'skincare', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'published'),
('Understanding Acne', 'Acne is a common skin condition that happens when hair follicles...', 'education', 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'published');

-- Seed Activity Logs
INSERT INTO activity_logs (user_id, action, description) VALUES
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'login', 'User logged in successfully.'),
('b0eebc99-9c0b-4ef8-bb6d-6bb9bd380a22', 'face_scan', 'User performed a face scan.');
