<?php
declare(strict_types=1);

$availableLanguages = require __DIR__ . '/translations/available.php';

$translations = [];
foreach ($availableLanguages as $locale => $_meta) {
    $path = __DIR__ . '/translations/' . $locale . '.php';
    if (is_file($path)) {
        $translations[$locale] = require $path;
    } else {
        $translations[$locale] = [];
    }
}

return [
    'available_languages' => $availableLanguages,
    'translations' => $translations,
];
