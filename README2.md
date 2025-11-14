# Task Management App

A simple task manager where you can create, organize, and track your tasks. Works both as a regular website and as an API.

## What Can It Do?

**User Stuff:**
- Sign up and log in
- Reset your password if you forget it
- Secure login (your password is safe)

**Task Stuff:**
- Create tasks with titles and descriptions
- Mark tasks as "Not Started", "In Progress", or "Completed"
- Set priorities (Low, Medium, High)
- Filter tasks by status
- Each user only sees their own tasks
- Quick stats on your dashboard

**Tech Stuff:**
- Regular web interface for humans
- JSON API for developers
- Works with both

## What You Need

- PHP 8.0 or newer
- MySQL or similar database
- Composer (PHP package manager)
- An email service if you want password resets to work

## Getting Started

### 1. Download and Install
```bash
# Clone the project
git clone <your-repo-url>
cd <project-folder>

# Install dependencies
composer install
```

### 2. Set Up Database

Open `config/app_local.php` and add your database info:
```php
'Datasources' => [
    'default' => [
        'host' => 'localhost',
        'username' => 'your_username',
        'password' => 'your_password',
        'database' => 'your_database',
    ],
],
```

### 3. Create Database Tables
import schema.sql to your database

### 4. Run It!
```bash
bin/cake server
```

Open your browser and go to: `http://localhost:8765`

## Using the API

### How It Works

You can use the API by:
- Adding `.json` to any URL: `/tasks.json`
- Sending JSON in your requests
- Setting the right headers

### Example Requests

**Get all your tasks:**
```bash
curl http://localhost:8765/tasks.json
```

**Create a new task:**
```bash
curl -X POST http://localhost:8765/tasks/add.json \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Buy groceries",
    "description": "Milk, eggs, bread",
    "status": "not_started",
    "priority": "medium"
  }'
```

**Update a task:**
```bash
curl -X PUT http://localhost:8765/tasks/edit/1.json \
  -H "Content-Type: application/json" \
  -d '{"status": "completed"}'
```

**Delete a task:**
```bash
curl -X DELETE http://localhost:8765/tasks/delete/1.json
```

### API Responses

Everything comes back as JSON:

**Success:**
```json
{
  "success": true,
  "message": "Task has been created",
  "data": {
    "task": {...}
  }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Task not found"
}
```

## Built With

- **CakePHP** - The PHP framework that does the heavy lifting
- **MySQL** - Where we store everything
- **PHP 8** - The language
- **Composer** - Manages our PHP packages

## Project Layout
```
src/
  Controller/     → Where the logic happens
  Model/          → Database stuff
  View/           → HTML templates
templates/        → More HTML templates
webroot/          → CSS, JavaScript, images
config/           → Settings and configuration
```

## Things to Know

**Good Stuff:**
- Passwords are encrypted automatically
- You only see your own tasks
- Protected against common attacks (SQL injection, XSS)
- Works on phone browsers

**Limitations:**
- API uses cookies (not ideal for mobile apps)
- No rate limiting on API calls
- Can't share tasks with other users
- No task categories or tags yet
- No due dates or reminders

## Ideas for Later

Things that would be cool to add:
- Better API authentication (JWT tokens)
- Share tasks with teammates
- Add due dates and get reminders
- Attach files to tasks
- Search through tasks
- Mobile app
- Calendar view
- Subtasks

## Need Help?

Having issues? Here's what usually works:
- Check your database connection in `config/app_local.php`
- Make sure `tmp/` and `logs/` folders are writable
- Clear cache: `bin/cake cache clear_all`
- Check logs in `logs/` folder

## License

Do whatever you want with it!

## Credits

Made with CakePHP and caffeine ☕
