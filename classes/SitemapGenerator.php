<?php
/**
 * Modern Sitemap Generator Class
 * PHP 8.0+ Required
 */

class SitemapGenerator
{
    private array $visitedUrls = [];
    private array $sitemapUrls = [];
    private int $maxDepth;
    private string $changeFreq;
    private float $priority;
    private string $baseUrl;
    
    public function generateSitemap(string $baseUrl, int $maxDepth = 3, string $changeFreq = 'weekly', float $priority = 0.5): string
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->maxDepth = $maxDepth;
        $this->changeFreq = $changeFreq;
        $this->priority = $priority;
        $this->visitedUrls = [];
        $this->sitemapUrls = [];
        
        // Add main URL
        $this->addUrl($this->baseUrl, 1.0, $changeFreq);
        
        // Crawl URLs
        $this->crawlUrl($this->baseUrl, 1);
        
        return $this->generateXml();
    }
    
    private function crawlUrl(string $url, int $depth): void
    {
        if ($depth > $this->maxDepth || in_array($url, $this->visitedUrls)) {
            return;
        }
        
        $this->visitedUrls[] = $url;
        
        try {
            $html = $this->fetchUrl($url);
            if ($html === false) {
                return;
            }
            
            $links = $this->extractLinks($html, $url);
            
            foreach ($links as $link) {
                if ($this->isValidInternalUrl($link)) {
                    $this->addUrl($link, $this->calculatePriority($depth), $this->changeFreq);
                    $this->crawlUrl($link, $depth + 1);
                }
            }
        } catch (Exception $e) {
            // Continue silently on error
        }
    }
    
    private function fetchUrl(string $url): string|false
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (compatible; SitemapGenerator/1.0)',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                ],
                'timeout' => 10,
                'follow_location' => true,
                'max_redirects' => 3
            ]
        ]);
        
        return @file_get_contents($url, false, $context);
    }
    
    private function extractLinks(string $html, string $baseUrl): array
    {
        $links = [];
        
        // Find href="..." pattern
        preg_match_all('/href=["\']([^"\'>]+)["\']/', $html, $matches);
        
        foreach ($matches[1] as $link) {
            $absoluteUrl = $this->makeAbsoluteUrl($link, $baseUrl);
            if ($absoluteUrl) {
                $links[] = $absoluteUrl;
            }
        }
        
        return array_unique($links);
    }
    
    private function makeAbsoluteUrl(string $url, string $baseUrl): ?string
    {
        // If already absolute URL
        if (preg_match('/^https?:\/\//', $url)) {
            return $url;
        }
        
        // Skip anchor or javascript links
        if (str_starts_with($url, '#') || str_starts_with($url, 'javascript:') || str_starts_with($url, 'mailto:')) {
            return null;
        }
        
        // Convert relative URL to absolute URL
        $parsedBase = parse_url($baseUrl);
        $scheme = $parsedBase['scheme'] ?? 'https';
        $host = $parsedBase['host'] ?? '';
        
        if (str_starts_with($url, '//')) {
            return $scheme . ':' . $url;
        }
        
        if (str_starts_with($url, '/')) {
            return $scheme . '://' . $host . $url;
        }
        
        // Relative path
        $basePath = dirname($parsedBase['path'] ?? '/');
        if ($basePath === '.') {
            $basePath = '/';
        }
        
        return $scheme . '://' . $host . rtrim($basePath, '/') . '/' . $url;
    }
    
    private function isValidInternalUrl(string $url): bool
    {
        $parsedUrl = parse_url($url);
        $parsedBase = parse_url($this->baseUrl);
        
        // Same domain check
        if (($parsedUrl['host'] ?? '') !== ($parsedBase['host'] ?? '')) {
            return false;
        }
        
        // File extension check (skip images, css, js etc.)
        $path = $parsedUrl['path'] ?? '/';
        $excludeExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.css', '.js', '.pdf', '.zip', '.rar'];
        
        foreach ($excludeExtensions as $ext) {
            if (str_ends_with(strtolower($path), $ext)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function addUrl(string $url, float $priority, string $changeFreq): void
    {
        $cleanUrl = strtok($url, '#'); // Remove fragments
        
        if (!in_array($cleanUrl, array_column($this->sitemapUrls, 'url'))) {
            $this->sitemapUrls[] = [
                'url' => $cleanUrl,
                'lastmod' => date('Y-m-d'),
                'changefreq' => $changeFreq,
                'priority' => number_format($priority, 1)
            ];
        }
    }
    
    private function calculatePriority(int $depth): float
    {
        return max(0.1, $this->priority - (($depth - 1) * 0.1));
    }
    
    private function generateXml(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($this->sitemapUrls as $urlData) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($urlData['url']) . "</loc>\n";
            $xml .= "    <lastmod>" . $urlData['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $urlData['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $urlData['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    public function getFoundUrls(): array
    {
        return $this->sitemapUrls;
    }
    
    public function getUrlCount(): int
    {
        return count($this->sitemapUrls);
    }
}