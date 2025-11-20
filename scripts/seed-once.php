#!/usr/bin/env php
<?php

// Check if seeder has already run
$seedMarkerFile = storage_path('framework/seeder.lock');

if (file_exists($seedMarkerFile)) {
    echo "Seeder already run. Skipping...\n";
    exit(0);
}

// Run seeder
echo "Running database seeder...\n";
exec('php artisan db:seed --force', $output, $returnCode);

if ($returnCode === 0) {
    // Create marker file to prevent re-running
    file_put_contents($seedMarkerFile, date('Y-m-d H:i:s'));
    echo "Seeder completed successfully.\n";
} else {
    echo "Seeder failed or already populated.\n";
}

exit($returnCode);
