<?php
/**
 * Language helper function
 * @param string $key Translation key
 * @param string|null $default Default value if translation not found
 * @return string Translated text
 */
function __($key, $default = null) {
    // For now, just return the default value
    // In a future implementation, we could add language file support
    return $default ?? $key;
}

// Make sure this helper is available globally
if (!function_exists('__')) {
    require_once __DIR__ . '/language.php';
}
?>
