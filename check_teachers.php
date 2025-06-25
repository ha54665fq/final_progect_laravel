<?php

// Manually load the Laravel bootstrap file
require __DIR__.'/bootstrap/app.php';

// Manually boot the application to access Laravel services
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\User;

// Query for users with the 'teacher' role
$teachers = User::where('role', 'teacher')->get();

if ($teachers->isEmpty()) {
    echo "No users with the 'teacher' role found.\n";
} else {
    echo "Users with the 'teacher' role:\n";
    foreach ($teachers as $teacher) {
        echo " - ID: " . $teacher->id . ", Name: " . $teacher->name . ", Email: " . $teacher->email . ", Role: " . $teacher->role . "\n";
    }
}
