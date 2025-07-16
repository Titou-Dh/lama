#!/bin/bash

# Docker management script for LAMA project

case "$1" in
    "up")
        echo "Starting LAMA application..."
        docker-compose up -d
        echo "Application is running at http://localhost"
        echo "Database is available at localhost:3306"
        ;;
    "down")
        echo "Stopping LAMA application..."
        docker-compose down
        ;;
    "build")
        echo "Building LAMA application..."
        docker-compose build
        ;;
    "rebuild")
        echo "Rebuilding LAMA application..."
        docker-compose down
        docker-compose build --no-cache
        docker-compose up -d
        ;;
    "logs")
        docker-compose logs -f
        ;;
    "db-logs")
        docker-compose logs -f db
        ;;
    "web-logs")
        docker-compose logs -f web
        ;;
    "restart")
        docker-compose restart
        ;;
    *)
        echo "Usage: $0 {up|down|build|rebuild|logs|db-logs|web-logs|restart}"
        echo ""
        echo "Commands:"
        echo "  up       - Start the application"
        echo "  down     - Stop the application"
        echo "  build    - Build the Docker images"
        echo "  rebuild  - Rebuild from scratch"
        echo "  logs     - Show all logs"
        echo "  db-logs  - Show database logs"
        echo "  web-logs - Show web server logs"
        echo "  restart  - Restart all services"
        exit 1
        ;;
esac
