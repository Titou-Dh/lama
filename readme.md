# Project Structure

This document provides an overview of the project directory structure for improved clarity.

```plaintext
/project-root
├── config              # Configuration files (database, constants, environment variables)
├── controller          # PHP controllers (handling logic)
├── model               # PHP models (database interactions)
├── view                # Views (organized for better structure)
│   ├── layouts         # Common layouts (header, footer, sidebar)
│   ├── pages           # Page views (home.php, about.php, contact.php, etc.)
│   ├── partials        # Reusable components (navbar.php, footer.php, sidebar.php)
│   ├── styles          # CSS stylesheets
│   ├── scripts         # JavaScript files
│   └── assets          # Images, fonts, icons
├── script              # Custom scripts (database migrations, utilities)
├── public              # Publicly accessible files (index.php, .htaccess, etc.)
├── routes              # Route definitions (optional, for better URL handling)
├── vendor              # Composer dependencies (if using Composer)
├── .htaccess           # Apache configuration (optional)
├── index.php           # Main entry point
└── composer.json       # Composer configuration (if applicable)
```

## Directory Details

- **config**: Contains configuration files such as database settings and environment variables.
- **controller**: Manages the business logic of the application.
- **model**: Handles interactions with the database.
- **view**: Contains templates and assets for the user interface:
  - **layouts**: Common page wrappers (header, footer, sidebar).
  - **pages**: Individual page templates (home, about, contact, etc.).
  - **partials**: Reusable UI components (navbar, footer, etc.).
  - **styles**: CSS stylesheets.
  - **scripts**: JavaScript files.
  - **assets**: Images, fonts, and icons.
- **script**: Holds custom scripts like utilities and database migration tools.
- **public**: Files that are accessible publicly, including the main index file.
- **routes**: Optional additional route definitions.
- **vendor**: Third party packages managed via Composer.
- **.htaccess**, **index.php**, and **composer.json**: Core configuration and entry point files.

This layout enhances readability and maintainability for both development and future updates.


## Git Workflow

Follow these steps to contribute to the project:

1. **Create a new branch**:
    ```bash
    git checkout -b feat/feature-name   # For new features
    # OR
    git checkout -b fix/bug-name        # For bug fixes
    ```

2. **Make your changes** to the codebase

3. **Stage and commit your changes**:
    ```bash
    git add .                           # Stage all changes
    git commit -m "Description of changes"
    ```

4. **Push your branch to the remote repository**:
    ```bash
    git push origin feat/feature-name   # Replace with your branch name
    ```

5. **Create a Pull Request**:
    - Go to the repository on GitHub/GitLab
    - Click "New Pull Request"
    - Select your branch as the source
    - Add a descriptive title and details about your changes
    - Submit the Pull Request

6. **Wait for review** - An administrator will review your changes before merging