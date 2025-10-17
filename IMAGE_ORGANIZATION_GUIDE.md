# Image Organization Guide for Sri Lanka Travel MVC Project

## Directory Structure

```
/Applications/XAMPP/xamppfiles/htdocs/test/public/images/
├── drivers/
│   ├── profiles/          # Driver profile photos
│   └── destinations/      # Destination hero images
├── guides/
│   └── profiles/          # Guide profile photos  
├── icons/                 # UI icons and arrows
└── common/               # Shared images across the site
```

## Image Paths for Views

### In Your PHP Views, use these paths:

#### **Driver Profile Images:**
```php
<img src="/test/public/images/drivers/profiles/driver-jane-smith-123.png" alt="Jane Smith">
<img src="/test/public/images/drivers/profiles/driver-john-doe-112.png" alt="John Doe">
<img src="/test/public/images/drivers/profiles/driver-saman-perera-84.png" alt="Saman Perera">
```

#### **Destination Images (Hero Section):**
```php
<div class="destination-bg" style="background-image:url('/test/public/images/drivers/destinations/img01.jpg');"></div>
<div class="destination-bg" style="background-image:url('/test/public/images/drivers/destinations/img02.jpg');"></div>
<div class="destination-bg" style="background-image:url('/test/public/images/drivers/destinations/img03.jpg');"></div>
<div class="destination-bg" style="background-image:url('/test/public/images/drivers/destinations/img04.jpg');"></div>
```

#### **Icons:**
```php
<img src="/test/public/images/icons/arrow-2-225.svg" alt="Arrow" class="arrow-icon">
<img src="/test/public/images/icons/node-134.png" alt="Node">
```

#### **Guide Profile Images (when you add them):**
```php
<img src="/test/public/images/guides/profiles/guide-name.png" alt="Guide Name">
```

## Database Image URLs

### Update your database records to use the new paths:

#### **For Drivers Table:**
```sql
UPDATE drivers SET image_url = '/test/public/images/drivers/profiles/driver-jane-smith-123.png' WHERE name = 'Jane Smith';
UPDATE drivers SET image_url = '/test/public/images/drivers/profiles/driver-john-doe-112.png' WHERE name = 'John Doe';
UPDATE drivers SET image_url = '/test/public/images/drivers/profiles/driver-saman-perera-84.png' WHERE name = 'Saman Perera';
```

#### **For Guides Table:**
```sql
UPDATE guides SET image_url = '/test/public/images/guides/profiles/guide-saman-perera.png' WHERE name = 'Saman Perera';
-- Add more as you upload guide photos
```

## Adding New Images

### **For Driver Images:**
- Upload to: `/Applications/XAMPP/xamppfiles/htdocs/test/public/images/drivers/profiles/`
- Naming convention: `driver-firstname-lastname-id.png`
- URL format: `/test/public/images/drivers/profiles/driver-firstname-lastname-id.png`

### **For Guide Images:**
- Upload to: `/Applications/XAMPP/xamppfiles/htdocs/test/public/images/guides/profiles/`
- Naming convention: `guide-firstname-lastname-id.png`
- URL format: `/test/public/images/guides/profiles/guide-firstname-lastname-id.png`

### **For Common Images (logos, backgrounds, etc.):**
- Upload to: `/Applications/XAMPP/xamppfiles/htdocs/test/public/images/common/`
- URL format: `/test/public/images/common/filename.ext`

## Benefits of This Structure

1. **Organized**: Images are categorized by purpose
2. **Scalable**: Easy to add new categories
3. **Maintainable**: Clear naming conventions
4. **Performance**: Direct access through public folder
5. **SEO Friendly**: Descriptive file names and paths
6. **Version Control**: Easy to track image changes

## Notes

- All image paths start with `/test/public/images/`
- Keep original images in components folder as backup
- Optimize images for web (compress, resize) before uploading
- Use appropriate formats: PNG for transparency, JPG for photos, SVG for icons
