<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== بيانات المعلمين ===\n";

$teachers = User::where('role', 'teacher')->get(['id', 'name', 'email', 'role']);

foreach ($teachers as $teacher) {
    echo "ID: " . $teacher->id . "\n";
    echo "الاسم: " . $teacher->name . "\n";
    echo "البريد الإلكتروني: " . $teacher->email . "\n";
    echo "الدور: " . $teacher->role . "\n";
    echo "------------------------\n";
}

echo "إجمالي عدد المعلمين: " . $teachers->count() . "\n";
