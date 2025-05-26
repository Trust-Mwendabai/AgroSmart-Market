<?php
/**
 * Search Autocomplete API
 * 
 * Provides product search suggestions with fuzzy matching capabilities
 */

// Start session and include required files
session_start();
require_once '../config/database.php';
require_once '../config/utils.php';
require_once '../models/Product.php';
require_once '../includes/FuzzySearch.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if the request contains a search term
if (!isset($_GET['query']) || empty($_GET['query'])) {
    echo json_encode(['error' => 'No search term provided']);
    exit;
}

// Get search term and sanitize it
$query = sanitize_input($_GET['query']);

// Initialize the Product model
$product_model = new Product($conn);

// Get the fuzzy search suggestions
$suggestions = $product_model->get_search_suggestions($query);

// Add fuzzy search results if enabled and we have the FuzzySearch class
if (class_exists('FuzzySearch') && count($suggestions) < 10) {
    $fuzzy_search = new FuzzySearch($conn);
    $fuzzy_results = $fuzzy_search->search_products($query);
    
    // Merge fuzzy results with direct matches, avoiding duplicates
    foreach ($fuzzy_results as $result) {
        $found = false;
        foreach ($suggestions as $suggestion) {
            if ($suggestion['id'] == $result['id']) {
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $suggestions[] = $result;
        }
        
        // Limit to 10 suggestions total
        if (count($suggestions) >= 10) {
            break;
        }
    }
}

// Return the suggestions as JSON
echo json_encode([
    'query' => $query,
    'suggestions' => $suggestions
]);
?>
