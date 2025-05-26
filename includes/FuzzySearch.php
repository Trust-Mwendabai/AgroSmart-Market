<?php
/**
 * FuzzySearch Class
 * 
 * Provides fuzzy search capabilities for products, handling typos and similar terms
 */
class FuzzySearch {
    private $conn;
    
    /**
     * Constructor
     * 
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Perform a fuzzy search on products
     * 
     * @param string $query Search query
     * @param int $max_results Maximum number of results to return
     * @return array Matching products
     */
    public function search_products($query, $max_results = 10) {
        // Clean and prepare the query
        $query = $this->prepare_query($query);
        
        // If the query is too short, return empty results
        if (strlen($query) < 2) {
            return [];
        }
        
        // Generate query variations to account for common typos
        $variations = $this->generate_query_variations($query);
        
        // Create SQL for each variation with decreasing relevance
        $sql_parts = [];
        $params = [];
        $types = '';
        
        // Exact matches (highest relevance)
        $sql_parts[] = "(p.name LIKE ? OR p.description LIKE ?)";
        $exact_term = "%$query%";
        $params[] = $exact_term;
        $params[] = $exact_term;
        $types .= 'ss';
        
        // Term at beginning of name (high relevance)
        $sql_parts[] = "p.name LIKE ?";
        $params[] = "$query%";
        $types .= 's';
        
        // Variation matches (medium relevance)
        foreach ($variations as $variation) {
            $sql_parts[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $var_term = "%$variation%";
            $params[] = $var_term;
            $params[] = $var_term;
            $types .= 'ss';
        }
        
        // Word boundary matches for multi-word queries (lower relevance)
        $words = explode(' ', $query);
        if (count($words) > 1) {
            foreach ($words as $word) {
                if (strlen($word) > 2) {
                    $sql_parts[] = "(p.name LIKE ? OR p.description LIKE ?)";
                    $word_term = "%$word%";
                    $params[] = $word_term;
                    $params[] = $word_term;
                    $types .= 'ss';
                }
            }
        }
        
        // Soundex matching for phonetic similarity (lowest relevance)
        $soundex = soundex($query);
        $sql_parts[] = "(SOUNDEX(p.name) = ? OR SOUNDEX(p.category) = ?)";
        $params[] = $soundex;
        $params[] = $soundex;
        $types .= 'ss';
        
        // Build the complete SQL query with relevance scoring
        $sql = "SELECT 
                p.*, 
                u.name as farmer_name, 
                u.location as farmer_location,
                CASE 
                    WHEN p.name LIKE ? THEN 100  -- Exact match in name
                    WHEN p.name LIKE ? THEN 90   -- Starts with query
                    WHEN p.description LIKE ? THEN 50  -- Match in description
                    ELSE 10  -- Other matches
                END as relevance
                FROM products p
                JOIN users u ON p.farmer_id = u.id
                WHERE " . implode(" OR ", $sql_parts) . "
                ORDER BY relevance DESC, p.name ASC
                LIMIT ?";
        
        // Add relevance score params
        $params[] = "%$query%";  // Exact match in name
        $params[] = "$query%";   // Starts with query
        $params[] = "%$query%";  // Match in description
        $types .= 'ssi';
        
        // Add limit parameter
        $params[] = $max_results;
        
        // Execute the query
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Fetch results
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Add a highlight field for displaying in autocomplete
            $row['highlight'] = $this->highlight_match($row['name'], $query);
            $products[] = $row;
        }
        
        return $products;
    }
    
    /**
     * Prepare a search query by removing special characters and extra spaces
     * 
     * @param string $query Raw search query
     * @return string Cleaned query
     */
    private function prepare_query($query) {
        // Convert to lowercase
        $query = strtolower($query);
        
        // Remove special characters
        $query = preg_replace('/[^\p{L}\p{N}\s]/u', '', $query);
        
        // Replace multiple spaces with a single space
        $query = preg_replace('/\s+/', ' ', $query);
        
        // Trim whitespace
        return trim($query);
    }
    
    /**
     * Generate variations of the query to account for common typos
     * 
     * @param string $query Search query
     * @return array Query variations
     */
    private function generate_query_variations($query) {
        $variations = [];
        $length = mb_strlen($query);
        
        // Skip for very short queries
        if ($length < 3) {
            return $variations;
        }
        
        // Character transpositions (swapping adjacent characters)
        for ($i = 0; $i < $length - 1; $i++) {
            $variation = $query;
            $char1 = mb_substr($query, $i, 1);
            $char2 = mb_substr($query, $i + 1, 1);
            $variation = mb_substr($query, 0, $i) . $char2 . $char1 . mb_substr($query, $i + 2);
            $variations[] = $variation;
        }
        
        // Character omissions (missing a character)
        for ($i = 0; $i < $length; $i++) {
            $variation = mb_substr($query, 0, $i) . mb_substr($query, $i + 1);
            $variations[] = $variation;
        }
        
        // Common character substitutions
        $substitutions = [
            'a' => ['e', 'q', 'z'],
            'b' => ['v', 'g', 'h'],
            'c' => ['v', 'x', 'd'],
            'd' => ['s', 'f', 'e'],
            'e' => ['w', 'r', 'd'],
            'f' => ['d', 'g', 'r'],
            'g' => ['f', 'h', 't'],
            'h' => ['g', 'j', 'y'],
            'i' => ['u', 'o', 'k'],
            'j' => ['h', 'k', 'u'],
            'k' => ['j', 'l', 'i'],
            'l' => ['k', 'o', 'p'],
            'm' => ['n', 'j', 'k'],
            'n' => ['m', 'b', 'h'],
            'o' => ['i', 'p', 'l'],
            'p' => ['o', 'l'],
            'q' => ['w', 'a'],
            'r' => ['e', 't', 'f'],
            's' => ['a', 'd', 'w'],
            't' => ['r', 'y', 'g'],
            'u' => ['y', 'i', 'j'],
            'v' => ['c', 'b', 'f'],
            'w' => ['q', 'e', 's'],
            'x' => ['z', 'c', 'd'],
            'y' => ['t', 'u', 'h'],
            'z' => ['a', 'x', 's']
        ];
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($query, $i, 1);
            if (isset($substitutions[$char])) {
                foreach ($substitutions[$char] as $replacement) {
                    $variation = mb_substr($query, 0, $i) . $replacement . mb_substr($query, $i + 1);
                    $variations[] = $variation;
                }
            }
        }
        
        // Deduplicate and return
        return array_unique($variations);
    }
    
    /**
     * Highlight the matching part of the text
     * 
     * @param string $text Text to highlight in
     * @param string $query Query to highlight
     * @return string Highlighted text
     */
    private function highlight_match($text, $query) {
        // Case-insensitive highlight
        $pattern = '/(' . preg_quote($query, '/') . ')/i';
        return preg_replace($pattern, '<strong>$1</strong>', $text);
    }
    
    /**
     * Calculate Levenshtein distance between two strings
     * Used for determining string similarity
     * 
     * @param string $str1 First string
     * @param string $str2 Second string
     * @return int Levenshtein distance
     */
    private function levenshtein_distance($str1, $str2) {
        // Use PHP's built-in Levenshtein function
        return levenshtein($str1, $str2);
    }
}
