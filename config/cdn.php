<?php
/**
 * CDN Configuration
 * 
 * This file contains configuration for Content Delivery Network integration
 * Supports multiple CDN providers and local fallback
 */

// Define base paths
define('STATIC_BASE_PATH', dirname(__DIR__) . '/public/');
define('STATIC_BASE_URL', '/AgroSmart Market/public/');

// CDN Configuration
$cdn_config = [
    // Enable/disable CDN
    'enabled' => false, // Set to true to enable CDN usage
    
    // CDN provider - 'local', 'cloudfront', 'cloudflare', 'bunny', 'custom'
    'provider' => 'local',
    
    // Provider-specific configurations
    'providers' => [
        // Amazon CloudFront
        'cloudfront' => [
            'domain' => 'https://d123456abcdef.cloudfront.net',
            'distribution_id' => '', // Your CloudFront distribution ID
            'key_pair_id' => '',     // For signed URLs (optional)
            'private_key' => ''      // For signed URLs (optional)
        ],
        
        // Cloudflare
        'cloudflare' => [
            'domain' => 'https://agrosmart-market.pages.dev',
            'zone_id' => '',         // Your Cloudflare zone ID
            'api_key' => ''          // Your Cloudflare API key (for cache purging)
        ],
        
        // BunnyCDN
        'bunny' => [
            'domain' => 'https://agrosmart-market.b-cdn.net',
            'storage_zone' => '',    // Your storage zone name
            'api_key' => '',         // Your API key (for cache purging)
            'pull_zone' => ''        // Your pull zone name
        ],
        
        // Custom CDN
        'custom' => [
            'domain' => 'https://cdn.agrosmart-market.com',
            'mapping' => [
                // Map local directories to CDN paths
                'css'    => '/styles/',
                'js'     => '/scripts/',
                'images' => '/img/',
                'fonts'  => '/fonts/'
            ]
        ]
    ],
    
    // Cache settings
    'cache' => [
        'max_age' => 2592000,        // Default cache time (30 days)
        'versioning' => true,        // Add version parameter to URLs
        'version_param' => 'v',      // Version parameter name
        'version' => '1.0.0',        // Current version
        'auto_versioning' => true    // Automatically add file modification time as version
    ],
    
    // Asset types to serve from CDN
    'asset_types' => [
        'css'  => true,
        'js'   => true,
        'images' => true,
        'fonts' => true,
        'uploads' => false  // Set to true to serve user uploads from CDN
    ],
    
    // Path mappings
    'paths' => [
        // Map local paths to CDN directories
        'css' => [
            'local_path' => STATIC_BASE_PATH . 'css/',
            'url_path' => 'css/'
        ],
        'js' => [
            'local_path' => STATIC_BASE_PATH . 'js/',
            'url_path' => 'js/'
        ],
        'images' => [
            'local_path' => STATIC_BASE_PATH . 'images/',
            'url_path' => 'images/'
        ],
        'fonts' => [
            'local_path' => STATIC_BASE_PATH . 'fonts/',
            'url_path' => 'fonts/'
        ],
        'uploads' => [
            'local_path' => STATIC_BASE_PATH . 'uploads/',
            'url_path' => 'uploads/'
        ]
    ]
];

/**
 * Get a CDN URL for an asset
 * 
 * @param string $path Relative path to the asset (e.g., 'css/style.css')
 * @param string $type Asset type (css, js, images, fonts, uploads)
 * @return string Full URL to the asset
 */
function cdn_url($path, $type = null) {
    global $cdn_config;
    
    // If CDN is disabled, return local URL
    if (!$cdn_config['enabled']) {
        return STATIC_BASE_URL . $path;
    }
    
    // Determine asset type if not provided
    if ($type === null) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, ['css', 'scss', 'less'])) {
            $type = 'css';
        } elseif (in_array($extension, ['js', 'mjs'])) {
            $type = 'js';
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'])) {
            $type = 'images';
        } elseif (in_array($extension, ['woff', 'woff2', 'ttf', 'eot', 'otf'])) {
            $type = 'fonts';
        } else {
            $type = 'uploads';
        }
    }
    
    // Check if this asset type should be served from CDN
    if (!isset($cdn_config['asset_types'][$type]) || !$cdn_config['asset_types'][$type]) {
        return STATIC_BASE_URL . $path;
    }
    
    // Get provider configuration
    $provider = $cdn_config['provider'];
    $provider_config = $cdn_config['providers'][$provider] ?? null;
    
    if (!$provider_config) {
        return STATIC_BASE_URL . $path;
    }
    
    // Build the CDN URL
    $url = '';
    
    switch ($provider) {
        case 'cloudfront':
        case 'cloudflare':
        case 'bunny':
            $url = $provider_config['domain'] . '/' . $path;
            break;
            
        case 'custom':
            // Check if there's a custom mapping for this asset type
            $path_prefix = $provider_config['mapping'][$type] ?? '';
            $url = $provider_config['domain'] . $path_prefix . $path;
            break;
            
        default:
            // Local fallback
            $url = STATIC_BASE_URL . $path;
            break;
    }
    
    // Add versioning if enabled
    if ($cdn_config['cache']['versioning']) {
        $version = $cdn_config['cache']['version'];
        
        // Add auto-versioning if enabled (use file modification time)
        if ($cdn_config['cache']['auto_versioning']) {
            // Find the local file path
            $local_path = '';
            foreach ($cdn_config['paths'] as $path_type => $path_config) {
                if (strpos($path, $path_config['url_path']) === 0) {
                    $local_path = str_replace(
                        $path_config['url_path'],
                        $path_config['local_path'],
                        $path
                    );
                    break;
                }
            }
            
            // If file exists, use its modification time as version
            if ($local_path && file_exists($local_path)) {
                $version = filemtime($local_path);
            }
        }
        
        // Add version parameter
        $separator = strpos($url, '?') !== false ? '&' : '?';
        $url .= $separator . $cdn_config['cache']['version_param'] . '=' . $version;
    }
    
    return $url;
}

/**
 * Output an HTML tag for an asset with CDN URL
 * 
 * @param string $path Relative path to the asset
 * @param string $type Asset type (css, js)
 * @return string HTML tag
 */
function cdn_asset($path, $type = null) {
    // Determine asset type if not provided
    if ($type === null) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, ['css', 'scss', 'less'])) {
            $type = 'css';
        } elseif (in_array($extension, ['js', 'mjs'])) {
            $type = 'js';
        } else {
            return ''; // Unsupported asset type
        }
    }
    
    $url = cdn_url($path, $type);
    
    // Generate the appropriate HTML tag
    switch ($type) {
        case 'css':
            return '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">';
            
        case 'js':
            return '<script src="' . htmlspecialchars($url) . '"></script>';
            
        default:
            return '';
    }
}

/**
 * Output an image tag with CDN URL
 * 
 * @param string $path Relative path to the image
 * @param string $alt Alt text
 * @param string $attributes Additional HTML attributes
 * @return string HTML img tag
 */
function cdn_image($path, $alt = '', $attributes = '') {
    $url = cdn_url($path, 'images');
    return '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" ' . $attributes . '>';
}

/**
 * Purge a file from the CDN cache
 * 
 * @param string $path Relative path to the file
 * @return bool Success
 */
function cdn_purge($path) {
    global $cdn_config;
    
    // If CDN is disabled, return true
    if (!$cdn_config['enabled']) {
        return true;
    }
    
    // Get provider configuration
    $provider = $cdn_config['provider'];
    $provider_config = $cdn_config['providers'][$provider] ?? null;
    
    if (!$provider_config) {
        return false;
    }
    
    // Implementation depends on the provider
    switch ($provider) {
        case 'cloudfront':
            // CloudFront implementation would go here
            // Requires AWS SDK
            return false;
            
        case 'cloudflare':
            // Cloudflare implementation would go here
            // Uses Cloudflare API
            return false;
            
        case 'bunny':
            // BunnyCDN implementation would go here
            // Uses BunnyCDN API
            return false;
            
        default:
            // No purging needed for local
            return true;
    }
}

// Return the configuration for use in other files
return $cdn_config;
