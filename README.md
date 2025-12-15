# Email Approval System

A secure, modern web application for managing email copy approvals with clients. Clients receive unique portal links to review and approve/deny email content without needing to create accounts.

## Features

### Client Portal
- **Secure Access** - 64-character cryptographically secure tokens (no login required)
- **Campaign Organization** - Content organized by campaigns (Auto Insurance, Mortgage, Debt, etc.)
- **One-at-a-Time Review** - Review subject lines and email bodies individually
- **Three Actions** - Approve, Deny, or Deny with Feedback
- **History View** - See all past approval decisions with feedback comments
- **Mobile Responsive** - Works on all devices

### Admin Portal
- **Secure Login** - Username/password with bcrypt hashing
- **Dashboard** - Overview of pending approvals across all clients
- **Client Management** - Create clients, generate/regenerate portal links
- **Campaign Management** - Organize content by campaign per client
- **Rich Text Editor** - TinyMCE editor for email body content
- **Revision Tracking** - Track versions and resubmit denied content
- **Email Notifications** - Get alerted when clients approve/deny content

## Tech Stack

- **Backend**: PHP 8.2+
- **Database**: MySQL/MariaDB
- **Frontend**: Tailwind CSS + Alpine.js
- **Rich Text**: TinyMCE
- **Email**: Mailgun API
- **Web Server**: Nginx

## Installation

### Prerequisites

- PHP 8.2 or higher
- MySQL 8.0+ or MariaDB 10.5+
- Composer
- Nginx
- Mailgun account (for email notifications)

### Step 1: Clone the Repository

```bash
cd /var/www/your-domain
git clone https://github.com/yourusername/email-approval.git
cd email-approval
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Create Database

Connect to MySQL as root and run:

```sql
CREATE DATABASE email_approval CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'email_approval_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON email_approval.* TO 'email_approval_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 4: Configure Environment

Copy the environment template:

```bash
cp env.template .env
chmod 660 .env
```

Edit `.env` with your settings:

```bash
nano .env
```

Required settings:
- `DB_HOST` - Database host (usually `localhost`)
- `DB_NAME` - Database name (`email_approval`)
- `DB_USER` - Database user
- `DB_PASSWORD` - Database password
- `MAILGUN_API_KEY` - Your Mailgun API key
- `MAILGUN_DOMAIN` - Your Mailgun sending domain
- `MAILGUN_FROM_EMAIL` - From email address
- `ADMIN_EMAIL` - Email to receive notifications
- `APP_URL` - Your application URL (e.g., `https://emailapproval.yourdomain.com`)

### Step 5: Initialize Database

Run the setup script to create tables and admin user:

```bash
php database/setup.php
```

**Important**: Save the generated admin password! It will only be shown once.

### Step 6: Configure Nginx

Create nginx config at `/etc/nginx/sites-available/emailapproval.conf`:

```nginx
server {
    listen 80;
    server_name emailapproval.yourdomain.com;
    root /var/www/your-domain/email-approval/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }

    location ~ /(\.env|composer\.(json|lock)|vendor/) {
        deny all;
        return 404;
    }
}
```

Enable the site and reload nginx:

```bash
sudo ln -s /etc/nginx/sites-available/emailapproval.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 7: Set Permissions

```bash
chown -R www-data:www-data /var/www/your-domain/email-approval
chmod -R 755 /var/www/your-domain/email-approval
chmod 660 /var/www/your-domain/email-approval/.env
```

### Step 8: SSL Certificate (Optional but Recommended)

```bash
sudo certbot --nginx -d emailapproval.yourdomain.com
```

## Usage

### Admin Workflow

1. **Login** - Go to `/admin` and login with your credentials
2. **Add Client** - Click "+ Add Client" and enter client name and email
3. **Create Campaign** - Click on client, then "+ Add Campaign"
4. **Add Content** - Add subject lines and email bodies for approval
5. **Send Link** - Click "Send Link to Client" to email them their portal URL
6. **Monitor** - Check dashboard for pending approvals and recent activity

### Client Workflow

1. **Receive Link** - Client receives email with unique portal URL
2. **View Campaigns** - See all campaigns with pending items count
3. **Review Content** - Review subject lines and email bodies one at a time
4. **Take Action** - Approve, Deny, or Deny with Feedback
5. **View History** - See all past decisions in the History tab

### Revision Workflow

When a client denies content with feedback:
1. Admin receives email notification with feedback
2. Admin edits the content and clicks "Resubmit"
3. Content revision number increments
4. Content status resets to "Pending"
5. Client can review the revised content

## File Structure

```
email-approval/
├── public/
│   ├── index.php           # Application entry point
│   └── .htaccess           # URL rewriting (Apache fallback)
├── src/
│   ├── Config/
│   │   └── Database.php    # Database connection
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── ApiController.php
│   │   └── ClientController.php
│   ├── Models/
│   │   ├── AdminUser.php
│   │   ├── ApprovalHistory.php
│   │   ├── Campaign.php
│   │   ├── Client.php
│   │   ├── EmailBody.php
│   │   └── SubjectLine.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   └── MailgunService.php
│   └── views/
│       ├── admin/          # Admin portal views
│       ├── client/         # Client portal views
│       ├── emails/         # Email templates
│       └── layouts/        # Layout templates
├── database/
│   ├── schema.sql          # Database schema
│   └── setup.php           # Setup script
├── .env                    # Environment config (not in git)
├── .gitignore
├── composer.json
├── env.template            # Environment template
└── README.md
```

## Security Features

| Feature | Description |
|---------|-------------|
| Secure Tokens | 64-character cryptographically random client access tokens |
| Password Hashing | bcrypt with cost factor 12 |
| CSRF Protection | Tokens on all forms |
| XSS Prevention | HTML escaping and HTMLPurifier for rich text |
| SQL Injection | PDO prepared statements exclusively |
| Session Security | HTTP-only, secure cookies, session regeneration |
| Rate Limiting | Login attempt throttling (5 attempts per 15 minutes) |
| Sensitive Files | .env protected from web access |

## Environment Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `email_approval` |
| `DB_USER` | Database user | `email_approval_user` |
| `DB_PASSWORD` | Database password | `secure_password` |
| `MAILGUN_API_KEY` | Mailgun API key | `key-xxxxx` |
| `MAILGUN_DOMAIN` | Mailgun domain | `yourdomain.com` |
| `MAILGUN_FROM_EMAIL` | From email | `noreply@yourdomain.com` |
| `MAILGUN_FROM_NAME` | From name | `Email Approval System` |
| `ADMIN_EMAIL` | Admin notification email | `admin@yourdomain.com` |
| `APP_URL` | Application URL | `https://emailapproval.yourdomain.com` |
| `APP_NAME` | Application name | `Email Approval Portal` |
| `APP_ENV` | Environment | `production` |
| `APP_DEBUG` | Debug mode | `false` |
| `SESSION_LIFETIME` | Session timeout (seconds) | `7200` |

## Troubleshooting

### 500 Internal Server Error
- Check PHP error logs: `tail -f /var/log/php8.2-fpm.log`
- Enable debug mode: Set `APP_DEBUG=true` in `.env`
- Verify file permissions

### Database Connection Failed
- Verify credentials in `.env`
- Check MySQL is running: `systemctl status mysql`
- Test connection: `mysql -u email_approval_user -p email_approval`

### Emails Not Sending
- Verify Mailgun credentials in `.env`
- Check Mailgun dashboard for errors
- Verify domain is verified in Mailgun

### Autoloader Issues
- Regenerate autoloader: `composer dump-autoload -o`
- Verify directory names match PSR-4 (capitalized)

## License

MIT License

## Support

For issues and feature requests, please open a GitHub issue.
