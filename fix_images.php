<?php
/**
 * AgroSmart Market - Quick Image Fix Script
 * 
 * This script creates sample placeholder images for the marketplace
 */

// Basic error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to create a colored placeholder image
function create_colored_image($path, $width, $height, $color, $text = '') {
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Parse color
    list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
    $bgColor = imagecolorallocate($image, $r, $g, $b);
    
    // Fill background
    imagefill($image, 0, 0, $bgColor);
    
    // Add text if specified
    if (!empty($text)) {
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $font = 3; // built-in font
        
        // Center text
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $centerX = ($width - $textWidth) / 2;
        $centerY = ($height - $textHeight) / 2;
        
        imagestring($image, $font, $centerX, $centerY, $text, $textColor);
    }
    
    // Create directory if it doesn't exist
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir<br>";
    }
    
    // Save image
    imagejpeg($image, $path, 90);
    imagedestroy($image);
    
    echo "Created image: $path<br>";
}

// Create sample images
$images = [
    // Hero and background images
    'public/images/hero-bg.jpg' => ['width' => 1920, 'height' => 800, 'color' => '#4CAF50', 'text' => 'Hero Background'],
    'public/images/cta-bg.jpg' => ['width' => 1600, 'height' => 600, 'color' => '#388E3C', 'text' => 'Call to Action'],
    'public/images/about-bg.jpg' => ['width' => 1200, 'height' => 800, 'color' => '#8BC34A', 'text' => 'About Background'],
    
    // Category images
    'public/images/categories/vegetables.jpg' => ['width' => 400, 'height' => 300, 'color' => '#4CAF50', 'text' => 'Vegetables'],
    'public/images/categories/fruits.jpg' => ['width' => 400, 'height' => 300, 'color' => '#FF9800', 'text' => 'Fruits'],
    'public/images/categories/grains.jpg' => ['width' => 400, 'height' => 300, 'color' => '#FFC107', 'text' => 'Grains'],
    'public/images/categories/dairy.jpg' => ['width' => 400, 'height' => 300, 'color' => '#2196F3', 'text' => 'Dairy'],
    
    // Product images
    'public/uploads/products/tomatoes.jpg' => ['width' => 400, 'height' => 300, 'color' => '#E53935', 'text' => 'Tomatoes'],
    'public/uploads/products/corn.jpg' => ['width' => 400, 'height' => 300, 'color' => '#FFD54F', 'text' => 'Corn'],
    'public/uploads/products/potatoes.jpg' => ['width' => 400, 'height' => 300, 'color' => '#8D6E63', 'text' => 'Potatoes'],
    'public/uploads/products/apples.jpg' => ['width' => 400, 'height' => 300, 'color' => '#C62828', 'text' => 'Apples'],
    'public/uploads/products/bananas.jpg' => ['width' => 400, 'height' => 300, 'color' => '#FBC02D', 'text' => 'Bananas'],
    'public/uploads/products/eggs.jpg' => ['width' => 400, 'height' => 300, 'color' => '#FFF9C4', 'text' => 'Eggs'],
    'public/uploads/products/milk.jpg' => ['width' => 400, 'height' => 300, 'color' => '#ECEFF1', 'text' => 'Milk'],
    
    // Default images
    'public/images/default-product.jpg' => ['width' => 400, 'height' => 300, 'color' => '#BDBDBD', 'text' => 'Product Image'],
    'public/images/default-user.jpg' => ['width' => 300, 'height' => 300, 'color' => '#607D8B', 'text' => 'User']
];

// Create all images
foreach ($images as $path => $config) {
    create_colored_image(
        $path,
        $config['width'],
        $config['height'],
        $config['color'],
        $config['text']
    );
}

// Create sample user profile images
for ($i = 1; $i <= 5; $i++) {
    create_colored_image(
        "public/uploads/users/user{$i}.jpg",
        300,
        300,
        sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
        "User {$i}"
    );
}

// Success message
echo "<h2>Image Generation Complete!</h2>";
echo "<p>All sample images have been created successfully.</p>";
echo "<p><a href='index.php' class='btn btn-primary'>Go to Homepage</a></p>";
?>
