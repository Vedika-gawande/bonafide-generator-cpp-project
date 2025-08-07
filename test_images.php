<?php
$logoPath = __DIR__ . "/gpy.jpg";
$signPath = __DIR__ . "/mechanical.jpg";
$stampPath = __DIR__ . "/stamp1.jpg";

echo "Logo Path: " . $logoPath . "<br>";
echo "Sign Path: " . $signPath . "<br>";
echo "Stamp Path: " . $stampPath . "<br>";

if (file_exists($logoPath)) {
    echo "✅ gpy.jpg found!<br>";
} else {
    echo "❌ gpy.jpg NOT found!<br>";
}

if (file_exists($signPath)) {
    echo "✅ mechanical.jpg found!<br>";
} else {
    echo "❌ mechanical.jpg NOT found!<br>";
}

if (file_exists($stampPath)) {
    echo "✅ stamp1.jpg found!<br>";
} else {
    echo "❌ stamp1.jpg NOT found!<br>";
}
?>
