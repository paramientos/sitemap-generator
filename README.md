# Modern Sitemap Generator

🚀 **Modern and user-friendly sitemap.xml generator developed with PHP 8.0+**

## ✨ Features

- **Modern Design**: Responsive and user-friendly interface
- **Fast Crawling**: Advanced web crawling algorithm
- **Customizable**: Depth, priority and change frequency settings
- **Secure**: URL validation and security checks
- **Easy to Use**: One-click sitemap generation and download
- **PHP 8.0+ Support**: Uses modern PHP features

## 🛠️ Installation

### Requirements

- PHP 8.0 or higher
- `allow_url_fopen` must be enabled
- Web server (Apache, Nginx) or PHP built-in server

### Quick Start

1. **Clone the project:**
   ```bash
   git clone <repository-url>
   cd sitemap-generator
   ```

2. **Run with PHP development server:**
   ```bash
   php -S localhost:8080
   ```

3. **Open in your browser:**
   ```
   http://localhost:8080
   ```

## 📁 Project Structure

```
sitemap-generator/
├── index.php              # Main application file
├── classes/
│   ├── SitemapGenerator.php # Sitemap generator class
│   └── UrlValidator.php     # URL validator class
├── assets/
│   ├── css/
│   │   └── style.css       # Modern CSS styles
│   └── js/
│       └── script.js       # JavaScript functions
└── README.md              # This file
```

## 🎯 Usage

1. **Enter Website URL**: Enter the URL of the website you want to create a sitemap for
2. **Configure Parameters**:
   - **Maximum Depth**: How many levels deep to crawl (1-5)
   - **Change Frequency**: How often pages are updated
   - **Priority**: Importance level of pages (0.1-1.0)
3. **Click "Generate Sitemap" button**
4. **Download or copy the result**

## ⚙️ Feature Details

### SitemapGenerator Class

- **Smart Crawling**: Internal links are automatically detected
- **Duplicate Control**: Same URLs are not added twice
- **Robots.txt Support**: Crawling according to robots.txt rules
- **Error Handling**: Inaccessible pages are gracefully skipped

### UrlValidator Class

- **Comprehensive Validation**: URL format and accessibility check
- **Domain Control**: Valid domain name validation
- **Security**: Filtering of malicious URLs

### Modern Interface

- **Responsive Design**: Perfect view on all devices
- **Real-time Validation**: Instant URL validation
- **Loading States**: Loading animations for user experience
- **Copy to Clipboard**: One-click sitemap copying

## 🔧 Configuration

### PHP Settings

Make sure the following settings are active in the `php.ini` file:

```ini
allow_url_fopen = On
max_execution_time = 300
memory_limit = 256M
```

### Security

- Set `display_errors = Off` in production environment
- Prefer using HTTPS
- Consider rate limiting implementation

## 🚀 Advanced Usage

### Programmatic Usage

```php
require_once 'classes/SitemapGenerator.php';

$generator = new SitemapGenerator();
$sitemap = $generator->generateSitemap(
    'https://example.com',
    3,              // Max depth
    'weekly',       // Change frequency
    0.8             // Priority
);

echo $sitemap;
```

### URL Validation

```php
require_once 'classes/UrlValidator.php';

$validator = new UrlValidator();

if ($validator->isValidUrl('https://example.com')) {
    echo "URL is valid!";
}

if ($validator->isUrlAccessible('https://example.com')) {
    echo "URL is accessible!";
}
```

## 🎨 Customization

### CSS Variables

You can change the color theme by editing the CSS variables in the `assets/css/style.css` file:

```css
:root {
    --primary-color: #6366f1;
    --secondary-color: #10b981;
    --error-color: #ef4444;
    /* ... other variables */
}
```

### JavaScript Customization

You can add additional functions by editing the `assets/js/script.js` file.

## 🐛 Troubleshooting

### Common Issues

1. **"allow_url_fopen" error**:
   ```
   Solution: Set allow_url_fopen = On in php.ini
   ```

2. **Timeout errors**:
   ```
   Solution: Increase max_execution_time value
   ```

3. **Memory limit errors**:
   ```
   Solution: Increase memory_limit value
   ```

## 📝 License

This project is licensed under the MIT License.

## 🤝 Contributing

1. Fork it
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Create a Pull Request

## 📞 Support

You can open an issue for your questions or contact us.

---

**Modern Sitemap Generator** - Modern and user-friendly sitemap generator developed with PHP 8.0+! 🚀