<?php
/**
 * AgroSmart Market Language Configuration
 * Supports multiple languages for the platform
 */

// Available languages
$available_languages = [
    'en' => [
        'name' => 'English',
        'flag' => 'gb.png',
        'code' => 'en',
        'default' => true
    ],
    'bem' => [
        'name' => 'Bemba',
        'flag' => 'zm.png',
        'code' => 'bem',
        'default' => false
    ]
];

// Get current language from session or set default
function get_current_language() {
    global $available_languages;
    
    if (isset($_SESSION['language']) && array_key_exists($_SESSION['language'], $available_languages)) {
        return $_SESSION['language'];
    }
    
    // Get default language
    foreach ($available_languages as $code => $lang) {
        if ($lang['default']) {
            return $code;
        }
    }
    
    // Fallback to English if no default found
    return 'en';
}

// Load language translations
function load_language($language_code = null) {
    if ($language_code === null) {
        $language_code = get_current_language();
    }
    
    $language_file = __DIR__ . '/../languages/' . $language_code . '.php';
    
    if (file_exists($language_file)) {
        require_once $language_file;
        return $translations;
    }
    
    // Fallback to English
    require_once __DIR__ . '/../languages/en.php';
    return $translations;
}

// Translate text
function __($key, $default = null) {
    static $translations = null;
    
    if ($translations === null) {
        $translations = load_language();
    }
    
    if (isset($translations[$key])) {
        return $translations[$key];
    }
    
    return $default !== null ? $default : $key;
}
?>
