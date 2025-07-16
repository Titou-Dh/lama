@echo off

if "%1"=="up" (
    echo Starting LAMA application...
    docker-compose up -d
    echo Application is running at http://localhost
    echo Database is available at localhost:3306
) else if "%1"=="down" (
    echo Stopping LAMA application...
    docker-compose down
) else if "%1"=="build" (
    echo Building LAMA application...
    docker-compose build
) else if "%1"=="rebuild" (
    echo Rebuilding LAMA application...
    docker-compose down
    docker-compose build --no-cache
    docker-compose up -d
) else if "%1"=="logs" (
    docker-compose logs -f
) else if "%1"=="db-logs" (
    docker-compose logs -f db
) else if "%1"=="web-logs" (
    docker-compose logs -f web
) else if "%1"=="restart" (
    docker-compose restart
) else (
    echo Usage: docker.bat {up^|down^|build^|rebuild^|logs^|db-logs^|web-logs^|restart}
    echo.
    echo Commands:
    echo   up       - Start the application
    echo   down     - Stop the application
    echo   build    - Build the Docker images
    echo   rebuild  - Rebuild from scratch
    echo   logs     - Show all logs
    echo   db-logs  - Show database logs
    echo   web-logs - Show web server logs
    echo   restart  - Restart all services
)
