<?php
/**
 * URL Validator Class
 * PHP 8.0+ Required
 */

class UrlValidator
{
    /**
     * Checks if URL is valid
     */
    public function isValidUrl(string $url): bool
    {
        // Empty URL check
        if (empty(trim($url))) {
            return false;
        }
        
        // Basic check with PHP's filter_var function
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Parse URL
        $parsedUrl = parse_url($url);
        
        // Check for required components
        if (!isset($parsedUrl['scheme']) || !isset($parsedUrl['host'])) {
            return false;
        }
        
        // Only allow HTTP and HTTPS protocols
        if (!in_array(strtolower($parsedUrl['scheme']), ['http', 'https'])) {
            return false;
        }
        
        // Check host validity
        if (!$this->isValidHost($parsedUrl['host'])) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Checks if host is valid
     */
    private function isValidHost(string $host): bool
    {
        // Empty host check
        if (empty($host)) {
            return false;
        }
        
        // IP address check
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return true;
        }
        
        // Domain name check
        if (filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return true;
        }
        
        // Manual domain check (for older PHP versions)
        return $this->isValidDomain($host);
    }
    
    /**
     * Manually checks if domain name is valid
     */
    private function isValidDomain(string $domain): bool
    {
        // Basic character check
        if (!preg_match('/^[a-zA-Z0-9.-]+$/', $domain)) {
            return false;
        }
        
        // Length check
        if (strlen($domain) > 253) {
            return false;
        }
        
        // Length check for each label
        $labels = explode('.', $domain);
        foreach ($labels as $label) {
            if (strlen($label) > 63 || strlen($label) < 1) {
                return false;
            }
            
            // Label cannot start or end with hyphen
            if (str_starts_with($label, '-') || str_ends_with($label, '-')) {
                return false;
            }
        }
        
        // Must have at least one dot (for TLD)
        if (count($labels) < 2) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Checks if URL is accessible
     */
    public function isUrlAccessible(string $url, int $timeout = 10): bool
    {
        if (!$this->isValidUrl($url)) {
            return false;
        }
        
        $context = stream_context_create([
            'http' => [
                'method' => 'HEAD',
                'header' => [
                    'User-Agent: Mozilla/5.0 (compatible; SitemapGenerator/1.0)',
                ],
                'timeout' => $timeout,
                'follow_location' => true,
                'max_redirects' => 3
            ]
        ]);
        
        $headers = @get_headers($url, 1, $context);
        
        if ($headers === false) {
            return false;
        }
        
        // Check HTTP status code
        $statusLine = $headers[0] ?? '';
        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $statusLine, $matches);
        $statusCode = (int)($matches[1] ?? 0);
        
        // 2xx and 3xx codes are considered successful
        return $statusCode >= 200 && $statusCode < 400;
    }
    
    /**
     * Cleans and normalizes URL
     */
    public function normalizeUrl(string $url): string
    {
        $url = trim($url);
        
        // Add https if no protocol
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        
        // Remove trailing slash (except root)
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';
        
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }
        
        $normalizedUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        
        if (isset($parsedUrl['port']) && $parsedUrl['port'] != 80 && $parsedUrl['port'] != 443) {
            $normalizedUrl .= ':' . $parsedUrl['port'];
        }
        
        $normalizedUrl .= $path;
        
        if (isset($parsedUrl['query'])) {
            $normalizedUrl .= '?' . $parsedUrl['query'];
        }
        
        return $normalizedUrl;
    }
    
    /**
     * Checks if URL is allowed by robots.txt
     */
    public function isAllowedByRobots(string $url, string $userAgent = '*'): bool
    {
        $parsedUrl = parse_url($url);
        $robotsUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/robots.txt';
        
        $robotsContent = @file_get_contents($robotsUrl);
        
        if ($robotsContent === false) {
            // If robots.txt doesn't exist, assume allowed
            return true;
        }
        
        // Simple robots.txt parser
        $lines = explode("\n", $robotsContent);
        $currentUserAgent = null;
        $disallowedPaths = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (str_starts_with($line, 'User-agent:')) {
                $currentUserAgent = trim(substr($line, 11));
            } elseif (str_starts_with($line, 'Disallow:') && 
                     ($currentUserAgent === '*' || $currentUserAgent === $userAgent)) {
                $disallowedPath = trim(substr($line, 9));
                if (!empty($disallowedPath)) {
                    $disallowedPaths[] = $disallowedPath;
                }
            }
        }
        
        $urlPath = $parsedUrl['path'] ?? '/';
        
        foreach ($disallowedPaths as $disallowedPath) {
            if (str_starts_with($urlPath, $disallowedPath)) {
                return false;
            }
        }
        
        return true;
    }
}