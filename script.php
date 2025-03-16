<?php
// URL of the page to cache
$url = "http://10.0.1.1:3003/"; // Change to your actual site URL
$cacheFile = "/path-to-cached-file/index.html"; // Path where cached file will be stored

// Fetch the content from the live website
$content = file_get_contents($url);

// Delete old cache before generating a new one
if (file_exists($cacheFile)) {
    unlink($cacheFile);
}

// Fetch content from your live website
$content = file_get_contents($url);

if ($content !== false) {
    // Minify the HTML while keeping inline scripts safe
    $minifiedContent = minify_html($content);

    // Add timestamp for debugging
    $timestamp = "<!-- Cached at: " . date('Y-m-d H:i:s') . " -->\n";

    // Save the minified content as cache
    file_put_contents($cacheFile, $timestamp . $minifiedContent);

    echo "Cache updated successfully.";
} else {
    echo "Failed to update cache.";
}

// Function to minify HTML while keeping JavaScript safe
function minify_html($html) {
    // Protect <script> blocks
    preg_match_all('/<script\b[^>]*>[\s\S]*?<\/script>/', $html, $scriptMatches);
    
    // Replace scripts with placeholders
    $placeholders = [];
    foreach ($scriptMatches[0] as $index => $script) {
        $placeholder = "__SCRIPT_PLACEHOLDER_{$index}__";
        $placeholders[$placeholder] = $script;
        $html = str_replace($script, $placeholder, $html);
    }

    // Minify HTML (excluding scripts)
    $html = preg_replace(
        [
            '/<!--(.*?)-->/s',  // Remove HTML comments
            '/\s{2,}/',         // Remove extra spaces
            '/>\s+</'           // Remove spaces between HTML tags
        ],
        [
            '', 
            ' ',
            '><'
        ],
        $html
    );

    // Restore scripts
    foreach ($placeholders as $placeholder => $original) {
        $html = str_replace($placeholder, $original, $html);
    }

    return $html;
}
?>
