<?php
/**
 * AgroSmart Market - Image Setup Script
 * 
 * This script creates sample images for the marketplace
 */

// Directory structure
$directories = [
    'public/images',
    'public/images/categories',
    'public/uploads/products',
    'public/uploads/users'
];

// Create directories if they don't exist
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir<br>";
    } else {
        echo "Directory already exists: $dir<br>";
    }
}

// Function to create a simple colored placeholder image
function create_placeholder_image($filename, $width, $height, $color, $text = '') {
    $image = imagecreatetruecolor($width, $height);
    
    // Convert hex color to RGB
    $rgb = sscanf($color, "#%02x%02x%02x");
    $background = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
    
    // Fill the background
    imagefill($image, 0, 0, $background);
    
    // Add text if provided
    if (!empty($text)) {
        $white = imagecolorallocate($image, 255, 255, 255);
        $font_size = 5;
        $font = 1; // built-in font
        
        // Calculate text position to center it
        $text_width = imagefontwidth($font_size) * strlen($text);
        $text_height = imagefontheight($font_size);
        $x = ($width - $text_width) / 2;
        $y = ($height - $text_height) / 2;
        
        imagestring($image, $font, $x, $y, $text, $white);
    }
    
    // Save the image
    imagejpeg($image, $filename, 90);
    imagedestroy($image);
    
    echo "Created image: $filename<br>";
}

// Create hero background
create_placeholder_image('public/images/hero-bg.jpg', 1920, 800, '#4CAF50', 'AgroSmart Hero Background');

// Create CTA background
create_placeholder_image('public/images/cta-bg.jpg', 1920, 600, '#388E3C', 'CTA Background');

// Create about image
create_placeholder_image('public/images/farming-in-zambia.jpg', 800, 600, '#7DC582', 'Farming in Zambia');

// Create farmer illustration
create_placeholder_image('public/images/farmer-illustration.png', 600, 600, '#212529', 'Farmer Illustration');

// Create category images
$categories = [
    'vegetables' => '#4CAF50',
    'fruits' => '#FF9800',
    'dairy_eggs' => '#2196F3',
    'grains' => '#FFC107'
];

foreach ($categories as $category => $color) {
    create_placeholder_image("public/images/{$category}.jpg", 400, 300, $color, ucfirst($category));
    create_placeholder_image("public/images/categories/{$category}.jpg", 400, 300, $color, ucfirst($category));
}

// Create product images
$products = [
    'tomatoes' => '#E53935',
    'corn' => '#FFC107',
    'green_beans' => '#43A047',
    'mangoes' => '#FB8C00',
    'red_onions' => '#D32F2F',
    'eggs' => '#FFF9C4',
    'avocados' => '#558B2F'
];

foreach ($products as $product => $color) {
    create_placeholder_image("public/uploads/products/{$product}.jpg", 400, 300, $color, str_replace('_', ' ', ucfirst($product)));
}

// Create a default product image
create_placeholder_image('public/images/default-product.jpg', 400, 300, '#BDBDBD', 'Default Product');

echo "<p>Sample images have been created successfully. <a href='index.php'>Go to Homepage</a></p>";
?>
