<?php
/**
 * AgroSmart Market - Basic Placeholder Image Creator
 * 
 * This script creates simple HTML/CSS placeholder files instead of using GD
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

// Create a simple HTML placeholder file
function create_html_placeholder($filename, $width, $height, $color, $text) {
    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>$text</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: $color;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        div {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div>$text</div>
</body>
</html>
HTML;
    
    // Create directory if it doesn't exist
    $dir = dirname($filename);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir<br>";
    }
    
    // Write the HTML file
    file_put_contents($filename, $html);
    echo "Created placeholder: $filename<br>";
}

// Create a simple note file for images
function create_text_note($filename, $message) {
    // Create directory if it doesn't exist
    $dir = dirname($filename);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Write the note
    file_put_contents($filename, $message);
    echo "Created note: $filename<br>";
}

// Create empty image files for browsers to recognize
function create_empty_file($filename) {
    // Create directory if it doesn't exist
    $dir = dirname($filename);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Touch the file to create it
    file_put_contents($filename, '');
    echo "Created empty file: $filename<br>";
}

// List of images to create
$images = [
    // Core images
    'public/images/hero-bg.jpg',
    'public/images/cta-bg.jpg',
    'public/images/farming-in-zambia.jpg',
    'public/images/farmer-illustration.png',
    'public/images/default-product.jpg',
    
    // Category images
    'public/images/categories/vegetables.jpg',
    'public/images/categories/fruits.jpg',
    'public/images/categories/dairy.jpg',
    'public/images/categories/grains.jpg',
    
    // Product images
    'public/uploads/products/tomatoes.jpg',
    'public/uploads/products/corn.jpg',
    'public/uploads/products/potatoes.jpg',
    'public/uploads/products/apples.jpg',
    'public/uploads/products/eggs.jpg',
    'public/uploads/products/milk.jpg',
    
    // Individual product category images
    'public/images/vegetables.jpg',
    'public/images/fruits.jpg',
    'public/images/dairy.jpg',
    'public/images/grains.jpg'
];

// Create all empty image files
foreach ($images as $image) {
    create_empty_file($image);
}

// Create a note explaining the CSS-based approach
create_text_note('public/images/README.txt', 
    "AgroSmart Market is using CSS-based image placeholders instead of actual images.\n" .
    "This helps the site load faster and work even when images are not available.\n" .
    "The actual styling is handled by the image-placeholders.css file.\n"
);

echo "<h2>Placeholder Files Created Successfully!</h2>";
echo "<p>CSS-based placeholders will be used instead of actual images. This approach ensures your site will always look good, even if image files are missing.</p>";
echo "<p><a href='index.php' class='btn btn-primary'>Go to Homepage</a></p>";
?>
