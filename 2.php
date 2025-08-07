<?php
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Directory: " . __DIR__ . "<br>";

$testImage = "C:/vedika1/htdocs/cppfinal/gpy.jpg";
if (file_exists($testImage)) {
    echo "✅ Image Exists: gpy.jpg";
} else {
    echo "❌ Image NOT Found: gpy.jpg";
}
?>
