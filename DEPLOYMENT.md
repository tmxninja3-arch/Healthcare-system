# DEPLOYMENT INSTRUCTIONS

## ⚠️ IMPORTANT: Configure Base Path

The system uses a base path variable to handle routing. If your setup is different from the default, you need to update ONE file:

### File to Edit: `includes/header.php`

**Line 3:**
```php
$base_path = '/healthcare_system';
```

### Common Configurations:

**1. If installed in htdocs root:**
```php
$base_path = '/healthcare_system';  // ✓ Default (XAMPP/WAMP)
```
Access: `http://localhost/healthcare_system/`

**2. If installed in a subdirectory:**
```php
$base_path = '/myproject/healthcare_system';
```
Access: `http://localhost/myproject/healthcare_system/`

**3. If installed directly in htdocs (no subfolder):**
```php
$base_path = '';  // Empty string
```
Access: `http://localhost/`

**4. For production domain:**
```php
$base_path = '';  // Usually empty for production
```
Access: `http://yourdomain.com/`

---

## Quick Setup Steps:

1. **Extract files** to web server directory
   - XAMPP: `C:/xampp/htdocs/healthcare_system/`
   - WAMP: `C:/wamp64/www/healthcare_system/`

2. **Import database**
   - Open phpMyAdmin
   - Import `database_setup.sql`

3. **Check database config** in `config/db.php`
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Add your password if needed
   define('DB_NAME', 'healthcare_db');
   ```

4. **Verify base path** in `includes/header.php`
   - Default is `/healthcare_system`
   - Change if your folder structure is different

5. **Start Apache & MySQL**

6. **Access the system**
   - Go to: `http://localhost/healthcare_system/`
   - You should see the dashboard!

---

## Troubleshooting:

### "Not Found" Error
- Check the `$base_path` in `includes/header.php`
- Make sure it matches your actual folder location

### "Database connection failed"
- Check credentials in `config/db.php`
- Make sure MySQL is running
- Verify database `healthcare_db` exists

### CSS not loading
- The `$base_path` variable handles CSS paths automatically
- Clear browser cache if needed

### Links not working
- All navigation uses the `$base_path` variable
- Internal page links use relative paths (should work automatically)

---

## File Structure Requirements:

```
Your web root (htdocs or www)
└── healthcare_system/          ← Folder name can be different
    ├── config/
    ├── css/
    ├── patients/
    ├── visits/
    ├── reports/
    ├── includes/
    └── index.php
```

The key is to update `$base_path` to match whatever your folder is called and where it's located!
