<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;

echo "=== اختبار المستخدمين ===\n";

$teachers = User::where('role', 'teacher')->get();
echo "عدد المعلمين: " . $teachers->count() . "\n";

foreach ($teachers as $teacher) {
    echo "ID: " . $teacher->id . ", Name: " . $teacher->name . ", Email: " . $teacher->email . ", Role: " . $teacher->role . "\n";
}

echo "\n=== اختبار الـ Gates ===\n";

$teacher = $teachers->first();
if ($teacher) {
    echo "اختبار Gate 'manage-courses' للمعلم: " . (Gate::allows('manage-courses', $teacher) ? 'مسموح' : 'ممنوع') . "\n";
    echo "اختبار Gate 'manage-assignments' للمعلم: " . (Gate::allows('manage-assignments', $teacher) ? 'مسموح' : 'ممنوع') . "\n";
}

echo "\n=== اختبار الـ Routes ===\n";

$router = app('router');
$routes = $router->getRoutes();

foreach ($routes as $route) {
    if (str_contains($route->getName(), 'courses') || str_contains($route->getName(), 'assignments')) {
        echo "Route: " . $route->getName() . " - " . implode('|', $route->methods()) . " " . $route->uri() . "\n";
    }
}
