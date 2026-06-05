<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0');

// Get all tables
$tables = DB::select('SHOW TABLES');
$tableArray = [];
foreach ($tables as $table) {
    foreach ($table as $value) {
        $tableArray[] = $value;
    }
}

// Drop all tables
foreach ($tableArray as $table) {
    DB::statement("DROP TABLE IF EXISTS `$table`");
    echo "Dropped table: $table\n";
}

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "Database cleaned successfully!\n";
