# LAMA - Docker Implementation

## Overview
This guide explains how to run the LAMA application using Docker with your existing database.

## Prerequisites
- Docker Desktop installed and running
- Docker Compose (included with Docker Desktop)

## Database Implementation

The database implementation includes:
- **MySQL 5.7** container with your `lama_dev.sql` automatically imported
- **PHP 8.0.9** with Apache and necessary MySQL extensions
- **Volume persistence** for database data
- **Network configuration** for container communication

## Quick Start

### Using Windows
```batch
# Start the application
docker.bat up

# Stop the application
docker.bat down

# View logs
docker.bat logs
```

### Using Linux/Mac
```bash
# Make script executable
chmod +x docker.sh

# Start the application
./docker.sh up

# Stop the application
./docker.sh down

# View logs
./docker.sh logs
```

### Manual Docker Compose Commands
```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Build and start
docker-compose up --build

# View logs
docker-compose logs -f
```

## Access Information

- **Web Application**: http://localhost
- **Database**: localhost:3306
  - Database: `lama_dev`
  - Username: `root`
  - Password: `root`

## Database Features

### Automatic Import
- Your `lama_dev.sql` file is automatically imported when the database container starts for the first time
- Data persists between container restarts using Docker volumes

### Tables Included
- `users` - User management with organizer capabilities
- `events` - Event information with full-text search
- `categories` - Event categorization (18 predefined categories)
- `tickets` - Ticket management for events
- `orders` & `order_items` - Order processing
- `reviews` - Event reviews and ratings
- `user_preferences` - User category preferences
- `faqs` - Event FAQ system
- `promo_codes` - Promotional code system
- `testers` - Testing account management

### Key Features
- Full-text search on events (title, description, location)
- Foreign key constraints for data integrity
- User preference system for personalized recommendations
- Complete e-commerce functionality (orders, tickets, promos)

## Development vs Production

### Local Development (XAMPP)
The database configuration automatically detects if you're running locally and switches to:
- Host: `localhost`
- Password: `` (empty)

### Docker Environment
- Host: `db` (container name)
- Password: `root`

## Troubleshooting

### Container Issues
```bash
# Check container status
docker-compose ps

# View specific service logs
docker-compose logs db
docker-compose logs web

# Restart services
docker-compose restart
```

### Database Issues
```bash
# Access database directly
docker-compose exec db mysql -u root -p lama_dev

# Reset database (WARNING: This will delete all data)
docker-compose down -v
docker-compose up -d
```

### Rebuilding from Scratch
```bash
# Complete rebuild
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

## File Structure
```
├── lama_dev.sql          # Database export (auto-imported)
├── compose.yaml          # Docker Compose configuration
├── Dockerfile            # PHP/Apache container definition
├── config/database.php   # Database connection (Docker-aware)
├── docker.sh             # Linux/Mac management script
├── docker.bat            # Windows management script
└── .env                  # Environment variables
```

## Notes
- Database data persists in Docker volume `db_data`
- First startup may take longer due to database import
- The application automatically detects Docker vs local environment
- All original functionality remains intact
