<?php
/**
 * Modern Sitemap Generator
 * PHP 8.0+ Required
 */

require_once 'classes/SitemapGenerator.php';
require_once 'classes/UrlValidator.php';

$generator = new SitemapGenerator();
$validator = new UrlValidator();

$sitemap = null;
$error = null;

if ($_POST['action'] ?? '' === 'generate') {
    $baseUrl = trim($_POST['base_url'] ?? '');
    $maxDepth = (int)($_POST['max_depth'] ?? 3);
    $changeFreq = $_POST['change_freq'] ?? 'weekly';
    $priority = (float)($_POST['priority'] ?? 0.5);
    
    if ($validator->isValidUrl($baseUrl)) {
        try {
            $sitemap = $generator->generateSitemap($baseUrl, $maxDepth, $changeFreq, $priority);
        } catch (Exception $e) {
            $error = 'Error creating sitemap: ' . $e->getMessage();
        }
    } else {
        $error = 'Invalid URL format!';
    }
}

if (($_POST['action'] ?? '') === 'download' && isset($_POST['sitemap_content']) && !empty($_POST['sitemap_content'])) {
    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="sitemap.xml"');
    echo $_POST['sitemap_content'];
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title>Free Sitemap Generator - Create XML Sitemaps Online | Modern SEO Tool</title>
    <meta name="description" content="Generate XML sitemaps for your website instantly with our free online sitemap generator. Improve your SEO, help search engines crawl your site better. Fast, reliable, and easy to use.">
    <meta name="keywords" content="sitemap generator, XML sitemap, SEO tools, website sitemap, search engine optimization, free sitemap creator, online sitemap generator">
    <meta name="author" content="Modern Sitemap Generator">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Free Sitemap Generator - Create XML Sitemaps Online">
    <meta property="og:description" content="Generate XML sitemaps for your website instantly. Improve your SEO and help search engines crawl your site better with our free online tool.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:site_name" content="Modern Sitemap Generator">
    <meta property="og:locale" content="en_US">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Free Sitemap Generator - Create XML Sitemaps Online">
    <meta name="twitter:description" content="Generate XML sitemaps for your website instantly. Improve your SEO and help search engines crawl your site better.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🗺️</text></svg>">
    
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="assets/js/script.js" as="script">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></noscript>
    
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "Modern Sitemap Generator",
        "description": "Free online XML sitemap generator tool for websites. Create sitemaps instantly to improve SEO and search engine crawling.",
        "url": "<?php echo 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>",
        "applicationCategory": "SEO Tool",
        "operatingSystem": "Web Browser",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock"
        },
        "featureList": [
            "Generate XML sitemaps",
            "Customizable crawl depth",
            "Change frequency settings",
            "Priority configuration",
            "Instant download",
            "Free to use"
        ],
        "author": {
            "@type": "Organization",
            "name": "Modern Sitemap Generator"
        },
        "datePublished": "<?php echo date('Y-m-d'); ?>",
        "dateModified": "<?php echo date('Y-m-d'); ?>",
        "inLanguage": "en-US",
        "isAccessibleForFree": true,
        "browserRequirements": "Requires JavaScript. Requires HTML5."
    }
    </script>
</head>
<body>
    <div class="container">
        <header class="header" role="banner">
            <div class="header-content">
                <h1><i class="fas fa-sitemap" aria-hidden="true"></i> Sitemap Generator</h1>
                <p>Modern and fast sitemap.xml generator for better SEO</p>
            </div>
        </header>

        <main class="main" role="main">
            <section class="form-section" aria-labelledby="form-heading">
                <h2 id="form-heading" class="visually-hidden">Generate Your Sitemap</h2>
                <form method="POST" class="sitemap-form">
                    <input type="hidden" name="action" value="generate">
                    
                    <div class="form-group">
                        <label for="base_url">
                            <i class="fas fa-globe" aria-hidden="true"></i> Website URL
                        </label>
                        <input type="url" id="base_url" name="base_url" 
                               placeholder="https://example.com" 
                               value="<?php echo htmlspecialchars($_POST['base_url'] ?? ''); ?>" 
                               aria-describedby="url-help"
                               required>
                        <small id="url-help" class="form-help">Enter the full URL of your website including https://</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="max_depth">
                                <i class="fas fa-layer-group"></i> Maximum Depth
                                <span class="info-tooltip" title="Determines how many levels deep the sitemap crawling will go. Level 1 only scans the homepage, higher levels also scan sub-pages.">ℹ️</span>
                            </label>
                            <select id="max_depth" name="max_depth">
                                <option value="1" <?php echo ($_POST['max_depth'] ?? 3) == 1 ? 'selected' : ''; ?>>1 Level</option>
                                <option value="2" <?php echo ($_POST['max_depth'] ?? 3) == 2 ? 'selected' : ''; ?>>2 Levels</option>
                                <option value="3" <?php echo ($_POST['max_depth'] ?? 3) == 3 ? 'selected' : ''; ?>>3 Levels</option>
                                <option value="4" <?php echo ($_POST['max_depth'] ?? 3) == 4 ? 'selected' : ''; ?>>4 Levels</option>
                                <option value="5" <?php echo ($_POST['max_depth'] ?? 3) == 5 ? 'selected' : ''; ?>>5 Levels</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="change_freq">
                                <i class="fas fa-clock"></i> Change Frequency
                                <span class="info-tooltip" title="Tells search engines how often pages are updated. This information is important for SEO.">ℹ️</span>
                            </label>
                            <select id="change_freq" name="change_freq">
                                <option value="always" <?php echo ($_POST['change_freq'] ?? 'weekly') == 'always' ? 'selected' : ''; ?>>Always</option>
                                <option value="hourly" <?php echo ($_POST['change_freq'] ?? 'weekly') == 'hourly' ? 'selected' : ''; ?>>Hourly</option>
                                <option value="daily" <?php echo ($_POST['change_freq'] ?? 'weekly') == 'daily' ? 'selected' : ''; ?>>Daily</option>
                                <option value="weekly" <?php echo ($_POST['change_freq'] ?? 'weekly') == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                                <option value="monthly" <?php echo ($_POST['change_freq'] ?? 'weekly') == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                <option value="yearly" <?php echo ($_POST['change_freq'] ?? 'weekly') == 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                                <option value="never" <?php echo ($_POST['change_freq'] ?? 'weekly') == 'never' ? 'selected' : ''; ?>>Never</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="priority">
                                <i class="fas fa-star"></i> Priority
                                <span class="info-tooltip" title="Determines the importance level of pages (between 0.1-1.0). 1.0 is the highest priority, 0.1 is the lowest priority.">ℹ️</span>
                            </label>
                            <select id="priority" name="priority">
                                <option value="0.1" <?php echo ($_POST['priority'] ?? 0.5) == 0.1 ? 'selected' : ''; ?>>0.1</option>
                                <option value="0.3" <?php echo ($_POST['priority'] ?? 0.5) == 0.3 ? 'selected' : ''; ?>>0.3</option>
                                <option value="0.5" <?php echo ($_POST['priority'] ?? 0.5) == 0.5 ? 'selected' : ''; ?>>0.5</option>
                                <option value="0.8" <?php echo ($_POST['priority'] ?? 0.5) == 0.8 ? 'selected' : ''; ?>>0.8</option>
                                <option value="1.0" <?php echo ($_POST['priority'] ?? 0.5) == 1.0 ? 'selected' : ''; ?>>1.0</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cog fa-spin" style="display: none;"></i>
                        <i class="fas fa-magic"></i>
                        Generate Sitemap
                    </button>
                </form>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($sitemap): ?>
                <div class="result-section">
                    <div class="result-header">
                        <h3><i class="fas fa-check-circle"></i> Sitemap Generated Successfully!</h3>
                        <p class="result-description">
                            <i class="fas fa-info-circle"></i> 
                            Your sitemap.xml has been generated successfully. Review the content below and download when ready.
                        </p>
                    </div>
                    
                    <div class="sitemap-preview">
                        <div class="preview-header">
                            <h4><i class="fas fa-file-code"></i> Sitemap Content Preview</h4>
                            <div class="preview-stats">
                                <?php 
                                $urlCount = substr_count($sitemap, '<url>');
                                echo "<span class='stat'><i class='fas fa-link'></i> {$urlCount} URLs found</span>";
                                ?>
                            </div>
                        </div>
                        <pre id="sitemap-content"><code><?php echo htmlspecialchars($sitemap); ?></code></pre>
                    </div>
                    
                    <div class="result-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="download">
                            <input type="hidden" name="sitemap_content" value="<?php echo htmlspecialchars($sitemap); ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-download"></i> Download sitemap.xml
                            </button>
                        </form>
                        <button onclick="copySitemap()" class="btn btn-secondary">
                            <i class="fas fa-copy"></i> Copy to Clipboard
                        </button>
                        <button onclick="generateNew()" class="btn btn-outline">
                            <i class="fas fa-redo"></i> Generate New
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </main>

        <footer class="footer" role="contentinfo">
            <p>&copy; <?php echo date('Y'); ?> Modern Sitemap Generator - Free XML Sitemap Generator Tool</p>
            <nav aria-label="Footer navigation">
                <ul class="footer-links">
                    <li><a href="#" rel="noopener">Privacy Policy</a></li>
                    <li><a href="#" rel="noopener">Terms of Service</a></li>
                    <li><a href="#" rel="noopener">Contact</a></li>
                </ul>
            </nav>
        </footer>
    </div>

    <script src="assets/js/script.js" defer></script>
</body>
</html>