
-- Music Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(1, 1, 'Festival de la Médina', 'Join us for a vibrant celebration of traditional and modern music in the heart of Tunis. Enjoy performances from local and international artists.', '2025-06-15 12:00:00', '2025-06-17 23:00:00', 'Medina of Tunis', '../assets/images/music-festival.jpg', 5000, 'in-person', 'published', CURRENT_TIMESTAMP),
(2, 1, 'Carthage International Jazz Festival', 'Experience the smooth sounds of jazz at the historical Carthage Amphitheatre.', '2025-05-10 19:00:00', '2025-05-10 22:00:00', 'Carthage Amphitheatre, Tunis', '../assets/images/classical.jpg', 800, 'in-person', 'published', CURRENT_TIMESTAMP),
(1, 1, 'Festival International de la Musique Symphonique', 'Enjoy a night of classical symphonic music under the stars.', '2025-05-20 18:00:00', '2025-05-20 21:00:00', 'Carthage, Tunis', '../assets/images/jazz.jpg', 1000, 'in-person', 'published', CURRENT_TIMESTAMP),
(2, 1, 'Tunisian Rock Fest', 'A dynamic rock festival with performances from Tunisia’s best rock bands.', '2025-07-05 20:00:00', '2025-07-05 23:30:00', 'Sousse Beach', '../assets/images/rock.jpg', 15000, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Technology Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `online_link`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(1, 2, 'Web Development Bootcamp', 'Intensive 2-day bootcamp covering web development technologies.', '2025-05-15 09:00:00', '2025-05-16 17:00:00', 'Tech Hub, Tunis', NULL, '../assets/images/webdev.jpg', 100, 'in-person', 'published', CURRENT_TIMESTAMP),
(2, 2, 'AI Future Conference', 'Discover the future of AI with keynotes and workshops.', '2025-06-05 10:00:00', '2025-06-07 18:00:00', NULL, 'https://zoom.us/j/ai-conference', '../assets/images/ai.jpg', 500, 'online', 'published', CURRENT_TIMESTAMP),
(1, 2, 'Cybersecurity Summit', 'Learn about digital threats and protection strategies.', '2025-05-25 09:00:00', '2025-05-26 17:00:00', 'Convention Center, Tunis', NULL, '../assets/images/cybersecurity.jpg', 300, 'in-person', 'published', CURRENT_TIMESTAMP),
(2, 2, 'Blockchain Workshop', 'Hands-on workshop about blockchain beyond cryptocurrency.', '2025-05-12 13:00:00', '2025-05-12 16:00:00', NULL, 'https://zoom.us/j/blockchain-workshop', '../assets/images/blockchain.jpg', 200, 'online', 'published', CURRENT_TIMESTAMP),
(1, 2, 'Mobile App Development Hackathon', 'Build a mobile app in 48 hours and win prizes!', '2025-06-20 09:00:00', '2025-06-22 18:00:00', 'Developer Campus, Sfax', NULL, '../assets/images/mobile-dev.jpg', 150, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Business Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(1, 3, 'Startup Networking Mixer', 'Connect with entrepreneurs, investors, and experts.', '2025-05-08 18:00:00', '2025-05-08 21:00:00', 'Innovation Hub, Tunis', '../assets/images/networking.jpg', 120, 'in-person', 'published', CURRENT_TIMESTAMP),
(2, 3, 'Women in Business Leadership Summit', 'Celebrating and empowering women in business.', '2025-05-30 09:00:00', '2025-05-31 17:00:00', 'Grand Hotel, Tunis', '../assets/images/women-business.jpg', 250, 'in-person', 'published', CURRENT_TIMESTAMP),
(1, 3, 'Digital Marketing Conference', 'Insights on SEO, social media, and advertising.', '2025-06-10 09:00:00', '2025-06-11 17:00:00', 'Marketing Center, Tunis', '../assets/images/marketing.jpg', 400, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Arts & Culture Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(2, 4, 'Exposition d\'Art Moderne', 'Provocative works from contemporary artists.', '2025-05-05 10:00:00', '2025-06-05 18:00:00', 'Musée d\'Art Moderne, Tunis', '../assets/images/modern-art.jpg', 500, 'in-person', 'published', CURRENT_TIMESTAMP),
(1, 4, 'Festival International du Film de Carthage', 'Celebrating independent cinema.', '2025-06-01 11:00:00', '2025-06-07 23:00:00', 'Carthage, Tunis', '../assets/images/film-festival.jpg', 800, 'in-person', 'published', CURRENT_TIMESTAMP),
(2, 4, 'Spectacle de Danse: Evolution du Rythme', 'A showcase of dance styles tracing movement evolution.', '2025-05-18 19:00:00', '2025-05-18 21:30:00', 'Centre des Arts, Tunis', '../assets/images/dance.jpg', 350, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Sports Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(1, 5, 'Marathon de la Charité', 'Annual charity marathon for runners of all levels.', '2025-05-22 07:00:00', '2025-05-22 14:00:00', 'Tunis City Center', '../assets/images/marathon.jpg', 3000, 'in-person', 'published', CURRENT_TIMESTAMP),
(2, 5, 'Tournoi de Basketball', 'Exciting local basketball tournament.', '2025-06-12 09:00:00', '2025-06-14 18:00:00', 'Stade Olympique, Tunis', '../assets/images/basketball.jpg', 1200, 'in-person', 'published', CURRENT_TIMESTAMP),
(1, 5, 'Yoga dans le Parc', 'Morning yoga sessions suitable for all skill levels.', '2025-05-09 07:00:00', '2025-05-09 08:30:00', 'Parc du Belvédère, Tunis', '../assets/images/yoga.jpg', 100, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Food & Drink Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(2, 6, 'Festival International de la Gastronomie', 'Sample cuisines from over 50 vendors, live shows.', '2025-05-28 11:00:00', '2025-05-30 22:00:00', 'Place Mohamed Ali, Tunis', '../assets/images/food-festival.jpg', 5000, 'in-person', 'published', CURRENT_TIMESTAMP);


INSERT INTO `users`(`id`, `username`, `email`, `password_hash`, `full_name`, `profile_image`, `created_at`, `is_organizer`) 
VALUES 
        ('5','chaima_ayed','chaima@gmail.com','3456','Chaima Ayed','/uploads/userimages/chimou.jpg','[value-7]','1'),

        ('6','manar_messaoudi','manar@gmail.com','8456','Manar Messaoudi','/uploads/userimages/manou.jpg','[value-7]','1'),

        ('8','mahdi_baya','mahdi@gmail.com','3556','Mahdi Baya','/uploads/userimages/mahdi.jpg','[value-7]','0'),

        ('7','youssef_benameur','youssef@gmail.com','3656','Youssef Ben Ameur','/uploads/userimages/yucef.jpg','[value-7]','0'),

        ('9','koussay_jbeli','koussay@gmail.com','3456','Koussay Jbeli','/uploads/userimages/koussay.jpg','[value-7]','0'),

        ('10','Amjed_H','amjed@gmail.com','3456','Amjed Houssaini','/uploads/userimages/amjed.jpg','[value-7]','0'),

        ('11','Oussama_alouch','oussama@gmail.com','3456','Oussama Allouche','/uploads/userimages/oussama.jpg','[value-7]','0')