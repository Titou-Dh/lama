-- Mock data for LAMA event platform
-- Created on April 24, 2025

-- First, let's insert some categories
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Music', 'Concerts, festivals, and other music-related events'),
(2, 'Technology', 'Tech conferences, workshops, hackathons, and meetups'),
(3, 'Business', 'Networking events, conferences, and business workshops'),
(4, 'Arts & Culture', 'Exhibitions, performances, and cultural events'),
(5, 'Sports', 'Sporting events, tournaments, and fitness activities'),
(6, 'Food & Drink', 'Food festivals, cooking classes, and tasting events'),
(7, 'Education', 'Seminars, workshops, and learning opportunities'),
(8, 'Health & Wellness', 'Yoga sessions, meditation retreats, and health seminars'),
(9, 'Charity & Causes', 'Fundraisers and awareness events'),
(10, 'Gaming', 'Gaming tournaments, conventions, and meetups');

-- Let's assume we have some organizers (users) in the system
-- If you need to insert users first, you can adjust this script accordingly
SET @organizer1 = 1; -- Replace with an actual user ID from your system
SET @organizer2 = 2; -- Replace with an actual user ID from your system

-- Now let's insert mock events for each category with a mix of dates (past, present, future)

-- Music Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer1, 1, 'Summer Music Festival', 'Join us for the biggest music festival of the summer featuring top artists from around the world. Experience three days of non-stop music across five stages with camping options available.', '2025-06-15 12:00:00', '2025-06-17 23:00:00', 'Central Park, New York', '../assets/images/music-festival.jpg', 5000, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 1, 'Classical Orchestra Night', 'Experience the magic of classical music with our world-renowned orchestra performing masterpieces by Mozart, Beethoven, and Bach.', '2025-05-10 19:00:00', '2025-05-10 22:00:00', 'Symphony Hall, Boston', '../assets/images/classical.jpg', 800, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 1, 'Jazz in the Park', 'Relax and enjoy smooth jazz performances under the stars. Bring your picnic blankets and refreshments for a perfect evening.', '2025-05-20 18:00:00', '2025-05-20 21:00:00', 'Golden Gate Park, San Francisco', '../assets/images/jazz.jpg', 1000, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 1, 'Rock Revival Tour', 'The ultimate rock experience featuring classic and new rock bands. Get ready to headbang all night long!', '2025-07-05 20:00:00', '2025-07-05 23:30:00', 'Madison Square Garden, New York', '../assets/images/rock.jpg', 15000, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Technology Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `online_link`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer1, 2, 'Web Development Bootcamp', 'Intensive 2-day bootcamp covering the latest in web development technologies. Learn HTML, CSS, JavaScript, and modern frameworks from industry experts.', '2025-05-15 09:00:00', '2025-05-16 17:00:00', 'Tech Hub, Seattle', NULL, '../assets/images/webdev.jpg', 100, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 2, 'AI Future Conference', 'Discover the future of artificial intelligence with keynotes from leading researchers and practical workshops on implementing AI in business.', '2025-06-05 10:00:00', '2025-06-07 18:00:00', NULL, 'https://zoom.us/j/ai-conference', '../assets/images/ai.jpg', 500, 'online', 'published', CURRENT_TIMESTAMP),
(@organizer1, 2, 'Cybersecurity Summit', 'Protect your business in the digital age. Learn about the latest threats and security solutions from cybersecurity experts.', '2025-05-25 09:00:00', '2025-05-26 17:00:00', 'Convention Center, Chicago', NULL, '../assets/images/cybersecurity.jpg', 300, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 2, 'Blockchain Workshop', 'Hands-on workshop about blockchain technology and its applications beyond cryptocurrency.', '2025-05-12 13:00:00', '2025-05-12 16:00:00', NULL, 'https://zoom.us/j/blockchain-workshop', '../assets/images/blockchain.jpg', 200, 'online', 'published', CURRENT_TIMESTAMP),
(@organizer1, 2, 'Mobile App Development Hackathon', 'Build an innovative mobile app in 48 hours and compete for amazing prizes! Open to developers of all skill levels.', '2025-06-20 09:00:00', '2025-06-22 18:00:00', 'Developer Campus, San Jose', NULL, '../assets/images/mobile-dev.jpg', 150, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Business Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer1, 3, 'Startup Networking Mixer', 'Connect with fellow entrepreneurs, investors, and industry experts in a casual setting. Perfect for making new business connections.', '2025-05-08 18:00:00', '2025-05-08 21:00:00', 'Innovation Hub, Austin', '../assets/images/networking.jpg', 120, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 3, 'Women in Business Leadership Summit', 'Celebrating and empowering women in business with inspiring keynotes, panel discussions, and workshops on leadership.', '2025-05-30 09:00:00', '2025-05-31 17:00:00', 'Grand Hotel, Miami', '../assets/images/women-business.jpg', 250, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 3, 'Digital Marketing Conference', 'Stay ahead of the digital marketing curve with insights on SEO, social media, content marketing, and advertising strategies.', '2025-06-10 09:00:00', '2025-06-11 17:00:00', 'Marketing Center, Las Vegas', '../assets/images/marketing.jpg', 400, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Arts & Culture Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer2, 4, 'Modern Art Exhibition', 'Explore provocative works from contemporary artists pushing the boundaries of modern expression. Exhibition includes interactive installations and guided tours.', '2025-05-05 10:00:00', '2025-06-05 18:00:00', 'Metropolitan Museum, New York', '../assets/images/modern-art.jpg', 500, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 4, 'International Film Festival', 'Celebrating independent cinema from around the world with screenings, director Q&As, and workshops.', '2025-06-01 11:00:00', '2025-06-07 23:00:00', 'Cinema Center, Los Angeles', '../assets/images/film-festival.jpg', 800, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 4, 'Dance Performance: Rhythm Evolution', 'A spectacular showcase of dance styles from around the world, tracing the evolution of rhythm and movement.', '2025-05-18 19:00:00', '2025-05-18 21:30:00', 'Performing Arts Center, Chicago', '../assets/images/dance.jpg', 350, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Sports Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer1, 5, 'Marathon for Charity', 'Run for a cause in our annual charity marathon. Multiple distance options available for runners of all levels.', '2025-05-22 07:00:00', '2025-05-22 14:00:00', 'Downtown, Boston', '../assets/images/marathon.jpg', 3000, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 5, 'Basketball Tournament', 'Local teams compete in an exciting basketball tournament. Food and entertainment available for spectators.', '2025-06-12 09:00:00', '2025-06-14 18:00:00', 'Sports Arena, Chicago', '../assets/images/basketball.jpg', 1200, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 5, 'Yoga in the Park', 'Start your day with energizing yoga sessions in the park, suitable for all skill levels from beginners to advanced.', '2025-05-09 07:00:00', '2025-05-09 08:30:00', 'Central Park, New York', '../assets/images/yoga.jpg', 100, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Food & Drink Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer2, 6, 'International Food Festival', 'Sample cuisines from around the world with over 50 food vendors, cooking demonstrations, and cultural performances.', '2025-05-28 11:00:00', '2025-05-30 22:00:00', 'City Park, San Francisco', '../assets/images/food-festival.jpg', 2000, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 6, 'Wine Tasting Experience', 'Discover exceptional wines from local vineyards paired with gourmet appetizers in an elegant setting.', '2025-05-15 18:00:00', '2025-05-15 21:00:00', 'Grand Hotel, Napa Valley', '../assets/images/wine.jpg', 80, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 6, 'Craft Beer Festival', 'Celebrate the art of brewing with tastings from over 30 craft breweries, food pairings, and live music.', '2025-06-08 12:00:00', '2025-06-08 19:00:00', 'Brewery District, Portland', '../assets/images/beer.jpg', 500, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Education Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `online_link`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer1, 7, 'Language Learning Workshop', 'Accelerate your language learning with proven techniques and practical conversation exercises.', '2025-05-16 10:00:00', '2025-05-16 16:00:00', NULL, 'https://zoom.us/j/language-workshop', '../assets/images/language.jpg', 50, 'online', 'published', CURRENT_TIMESTAMP),
(@organizer2, 7, 'Science Fair for Kids', 'Inspire young minds with hands-on science experiments, demonstrations, and interactive learning stations.', '2025-05-24 09:00:00', '2025-05-24 17:00:00', 'Science Center, Houston', NULL, '../assets/images/science-fair.jpg', 300, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 7, 'Financial Literacy Seminar', 'Learn essential skills for managing personal finances, investing, and planning for retirement.', '2025-06-03 18:00:00', '2025-06-03 21:00:00', 'Community Center, Atlanta', NULL, '../assets/images/finance.jpg', 150, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Health & Wellness Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `online_link`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer2, 8, 'Mindfulness Meditation Retreat', 'Escape the daily stress with guided meditation sessions, mindful practices, and relaxation techniques.', '2025-05-26 09:00:00', '2025-05-28 16:00:00', 'Wellness Resort, Santa Fe', NULL, '../assets/images/meditation.jpg', 60, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 8, 'Healthy Cooking Class', 'Learn to prepare nutritious and delicious meals with a professional chef. Ingredients and recipes included.', '2025-05-11 14:00:00', '2025-05-11 17:00:00', 'Culinary Institute, New York', NULL, '../assets/images/cooking.jpg', 30, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 8, 'Virtual Fitness Challenge', 'Join our 30-day fitness challenge with daily workouts, nutrition guidance, and community support.', '2025-05-01 00:00:00', '2025-05-30 23:59:59', NULL, 'https://fitness-challenge.com/join', '../assets/images/fitness.jpg', 1000, 'online', 'published', CURRENT_TIMESTAMP);

-- Charity & Causes Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer1, 9, 'Fundraising Gala for Children''s Hospital', 'An elegant evening of dinner, auctions, and entertainment to raise funds for the local children''s hospital.', '2025-06-18 18:00:00', '2025-06-18 22:00:00', 'Grand Ballroom, Chicago', '../assets/images/gala.jpg', 200, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 9, 'Beach Cleanup Day', 'Join our community effort to clean up local beaches and protect marine life. Equipment and refreshments provided.', '2025-05-17 09:00:00', '2025-05-17 13:00:00', 'Sunrise Beach, Miami', '../assets/images/cleanup.jpg', 100, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 9, 'Charity 5K Run/Walk', 'Participate in our annual 5K run/walk to support homeless shelters in the community. All ages welcome.', '2025-06-07 08:00:00', '2025-06-07 12:00:00', 'Riverside Park, Austin', '../assets/images/charity-run.jpg', 500, 'in-person', 'published', CURRENT_TIMESTAMP);

-- Gaming Events
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `online_link`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer2, 10, 'Esports Tournament', 'Compete in our major esports tournament featuring popular games and impressive prize pools.', '2025-05-29 10:00:00', '2025-05-31 20:00:00', 'Gaming Arena, Las Vegas', NULL, '../assets/images/esports.jpg', 400, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 10, 'Board Game Night', 'Enjoy a fun evening of classic and modern board games with fellow enthusiasts. Games provided or bring your favorite!', '2025-05-14 18:00:00', '2025-05-14 22:00:00', 'Game Café, Portland', NULL, '../assets/images/boardgames.jpg', 50, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 10, 'Virtual Reality Experience Day', 'Try the latest VR technology with games, simulations, and interactive experiences for all ages.', '2025-06-09 10:00:00', '2025-06-09 18:00:00', 'Tech Museum, San Francisco', NULL, '../assets/images/vr.jpg', 200, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer1, 10, 'Online Gaming Marathon', 'Join gamers worldwide in a 24-hour gaming marathon to raise funds for charity. Stream, play, donate!', '2025-06-15 12:00:00', '2025-06-16 12:00:00', NULL, 'https://twitch.tv/gaming-marathon', '../assets/images/gaming-marathon.jpg', 5000, 'online', 'published', CURRENT_TIMESTAMP);

-- Add some draft and past events for testing
INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `capacity`, `event_type`, `status`, `created_at`) VALUES
(@organizer1, 2, 'Future Tech Expo (Draft)', 'Preview of upcoming technology innovations and products.', '2025-07-10 09:00:00', '2025-07-12 18:00:00', 'Convention Center, Dallas', '../assets/images/tech-expo.jpg', 1000, 'in-person', 'draft', CURRENT_TIMESTAMP),
(@organizer2, 5, 'Tennis Tournament (Draft)', 'Amateur tennis tournament with divisions for all skill levels.', '2025-07-18 08:00:00', '2025-07-20 19:00:00', 'Tennis Club, Miami', '../assets/images/tennis.jpg', 200, 'in-person', 'draft', CURRENT_TIMESTAMP),
(@organizer1, 1, 'Past Music Concert', 'A night of unforgettable music with top artists.', '2025-03-15 20:00:00', '2025-03-15 23:00:00', 'Music Hall, Nashville', '../assets/images/concert.jpg', 600, 'in-person', 'published', CURRENT_TIMESTAMP),
(@organizer2, 4, 'Past Art Exhibition', 'Featuring works from emerging local artists.', '2025-02-10 10:00:00', '2025-03-10 18:00:00', 'Community Gallery, Portland', '../assets/images/art-local.jpg', 300, 'in-person', 'published', CURRENT_TIMESTAMP);
