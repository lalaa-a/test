# Email Configuration Guide

## Setting up Email for OTP Verification

The application now uses PHPMailer for sending emails. To configure email sending, update the following constants in `app/config/config.php`:

```php
// Email Configuration
define('SMTP_HOST', 'smtp.gmail.com');           // Your SMTP server
define('SMTP_USERNAME', 'your-email@gmail.com'); // Your email address
define('SMTP_PASSWORD', 'your-app-password');    // Your app password (not regular password)
define('SMTP_PORT', 587);                        // SMTP port (587 for TLS, 465 for SSL)
define('SMTP_ENCRYPTION', 'tls');                // 'tls' or 'ssl'
define('FROM_EMAIL', 'noreply@travelapp.com');   // Sender email
define('FROM_NAME', 'Travel App');               // Sender name
```

## Gmail Setup (Recommended for Development)

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate an App Password**:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a password for "Mail"
   - Use this 16-character password as `SMTP_PASSWORD`

3. **Update config.php**:
   ```php
   define('SMTP_HOST', 'smtp.gmail.com');
   define('SMTP_USERNAME', 'your-gmail@gmail.com');
   define('SMTP_PASSWORD', 'your-16-char-app-password');
   define('SMTP_PORT', 587);
   define('SMTP_ENCRYPTION', 'tls');
   ```

## Other SMTP Providers

### Outlook/Hotmail
```php
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');
```

### Yahoo
```php
define('SMTP_HOST', 'smtp.mail.yahoo.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');
```

### Custom SMTP Server
Replace the values with your SMTP server details provided by your email hosting service.

## Testing Email Functionality

1. Update the configuration in `config.php`
2. Restart your web server
3. Test the registration flow - OTP emails should be sent successfully

## Troubleshooting

- **Connection failed**: Check SMTP host, port, and encryption settings
- **Authentication failed**: Verify username/password (use app password for Gmail)
- **Emails not received**: Check spam folder, verify sender email is valid
- **SSL certificate errors**: Some SMTP servers may require different SSL settings

## Security Notes

- Never commit real email credentials to version control
- Use app passwords instead of regular passwords
- Consider using environment variables for production deployments