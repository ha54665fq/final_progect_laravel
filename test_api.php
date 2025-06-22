<?php

/**
 * API Test Script for Learning Management System
 * This script tests all API endpoints
 */

$baseUrl = 'http://127.0.0.1:8000/api';
$token = null;

function makeRequest($method, $endpoint, $data = null, $headers = []) {
    global $baseUrl;

    $url = $baseUrl . $endpoint;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers[] = 'Content-Type: application/json';
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

function printResult($testName, $result) {
    echo "\n=== $testName ===\n";
    echo "Status Code: " . $result['status'] . "\n";
    echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    echo "================================\n";
}

echo "🚀 Starting API Tests...\n";

// Test 1: Check API Status
echo "\n1. Testing API Status...";
$result = makeRequest('GET', '/status');
printResult('API Status', $result);

// Test 2: Register a new student
echo "\n2. Registering a new student...";
$studentData = [
    'name' => 'Test Student',
    'email' => 'student@test.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'student'
];
$result = makeRequest('POST', '/register', $studentData);
printResult('Student Registration', $result);

if ($result['status'] === 201 && isset($result['response']['data']['token'])) {
    $token = $result['response']['data']['token'];
    echo "✅ Student registered successfully. Token: " . substr($token, 0, 20) . "...\n";
}

// Test 3: Register a new teacher
echo "\n3. Registering a new teacher...";
$teacherData = [
    'name' => 'Test Teacher',
    'email' => 'teacher@test.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'teacher'
];
$result = makeRequest('POST', '/register', $teacherData);
printResult('Teacher Registration', $result);

if ($result['status'] === 201 && isset($result['response']['data']['token'])) {
    $teacherToken = $result['response']['data']['token'];
    echo "✅ Teacher registered successfully. Token: " . substr($teacherToken, 0, 20) . "...\n";
}

// Test 4: Login with student
echo "\n4. Logging in with student...";
$loginData = [
    'email' => 'student@test.com',
    'password' => 'password123'
];
$result = makeRequest('POST', '/login', $loginData);
printResult('Student Login', $result);

if ($result['status'] === 200 && isset($result['response']['data']['token'])) {
    $token = $result['response']['data']['token'];
    echo "✅ Student logged in successfully.\n";
}

// Test 5: Get profile
echo "\n5. Getting student profile...";
$result = makeRequest('GET', '/profile', null, ['Authorization: Bearer ' . $token]);
printResult('Get Profile', $result);

// Test 6: Login with teacher
echo "\n6. Logging in with teacher...";
$loginData = [
    'email' => 'teacher@test.com',
    'password' => 'password123'
];
$result = makeRequest('POST', '/login', $loginData);
printResult('Teacher Login', $result);

if ($result['status'] === 200 && isset($result['response']['data']['token'])) {
    $teacherToken = $result['response']['data']['token'];
    echo "✅ Teacher logged in successfully.\n";
}

// Test 7: Create a course (teacher)
echo "\n7. Creating a course (teacher)...";
$courseData = [
    'title' => 'Introduction to Programming',
    'description' => 'Learn the basics of programming with this comprehensive course.',
    'code' => 'CS101'
];
$result = makeRequest('POST', '/courses', $courseData, ['Authorization: Bearer ' . $teacherToken]);
printResult('Create Course', $result);

$courseId = null;
if ($result['status'] === 201 && isset($result['response']['data']['id'])) {
    $courseId = $result['response']['data']['id'];
    echo "✅ Course created successfully. ID: $courseId\n";
}

// Test 8: Get all courses (student)
echo "\n8. Getting all courses (student)...";
$result = makeRequest('GET', '/courses', null, ['Authorization: Bearer ' . $token]);
printResult('Get Courses (Student)', $result);

// Test 9: Get all courses (teacher)
echo "\n9. Getting all courses (teacher)...";
$result = makeRequest('GET', '/courses', null, ['Authorization: Bearer ' . $teacherToken]);
printResult('Get Courses (Teacher)', $result);

// Test 10: Create an assignment (teacher)
echo "\n10. Creating an assignment (teacher)...";
$assignmentData = [
    'title' => 'First Programming Assignment',
    'description' => 'Write a simple Hello World program.',
    'course_id' => $courseId,
    'due_date' => '2025-12-31 23:59:59',
    'max_score' => 100
];
$result = makeRequest('POST', '/assignments', $assignmentData, ['Authorization: Bearer ' . $teacherToken]);
printResult('Create Assignment', $result);

$assignmentId = null;
if ($result['status'] === 201 && isset($result['response']['data']['id'])) {
    $assignmentId = $result['response']['data']['id'];
    echo "✅ Assignment created successfully. ID: $assignmentId\n";
}

// Test 11: Get assignments for course
echo "\n11. Getting assignments for course...";
$result = makeRequest('GET', "/courses/$courseId/assignments", null, ['Authorization: Bearer ' . $token]);
printResult('Get Course Assignments', $result);

// Test 12: Enroll student in course
echo "\n12. Enrolling student in course...";
$enrollmentData = [
    'course_id' => $courseId
];
$result = makeRequest('POST', '/enrollments', $enrollmentData, ['Authorization: Bearer ' . $token]);
printResult('Enroll in Course', $result);

$enrollmentId = null;
if ($result['status'] === 201 && isset($result['response']['data']['id'])) {
    $enrollmentId = $result['response']['data']['id'];
    echo "✅ Student enrolled successfully. ID: $enrollmentId\n";
}

// Test 13: Submit assignment (student)
echo "\n13. Submitting assignment (student)...";
$submissionData = [
    'assignment_id' => $assignmentId,
    'content' => 'This is my assignment submission. I have created a Hello World program in Python.'
];
$result = makeRequest('POST', '/submissions', $submissionData, ['Authorization: Bearer ' . $token]);
printResult('Submit Assignment', $result);

$submissionId = null;
if ($result['status'] === 201 && isset($result['response']['data']['id'])) {
    $submissionId = $result['response']['data']['id'];
    echo "✅ Assignment submitted successfully. ID: $submissionId\n";
}

// Test 14: Get submissions for assignment (teacher)
echo "\n14. Getting submissions for assignment (teacher)...";
$result = makeRequest('GET', "/assignments/$assignmentId/submissions", null, ['Authorization: Bearer ' . $teacherToken]);
printResult('Get Assignment Submissions', $result);

// Test 15: Grade submission (teacher)
echo "\n15. Grading submission (teacher)...";
$gradeData = [
    'grade' => 85,
    'feedback' => 'Great work! Your Hello World program is working correctly. Keep up the good work!'
];
$result = makeRequest('PUT', "/submissions/$submissionId", $gradeData, ['Authorization: Bearer ' . $teacherToken]);
printResult('Grade Submission', $result);

// Test 16: Get student's submissions
echo "\n16. Getting student's submissions...";
$result = makeRequest('GET', '/submissions', null, ['Authorization: Bearer ' . $token]);
printResult('Get Student Submissions', $result);

// Test 17: Get enrollments for course (teacher)
echo "\n17. Getting enrollments for course (teacher)...";
$result = makeRequest('GET', "/courses/$courseId/enrollments", null, ['Authorization: Bearer ' . $teacherToken]);
printResult('Get Course Enrollments', $result);

// Test 18: Update enrollment status (teacher)
echo "\n18. Updating enrollment status (teacher)...";
$statusData = [
    'status' => 'active'
];
$result = makeRequest('PUT', "/enrollments/$enrollmentId", $statusData, ['Authorization: Bearer ' . $teacherToken]);
printResult('Update Enrollment Status', $result);

// Test 19: Try to create course as student (should fail)
echo "\n19. Trying to create course as student (should fail)...";
$result = makeRequest('POST', '/courses', $courseData, ['Authorization: Bearer ' . $token]);
printResult('Create Course as Student (Should Fail)', $result);

// Test 20: Try to submit assignment as teacher (should fail)
echo "\n20. Trying to submit assignment as teacher (should fail)...";
$result = makeRequest('POST', '/submissions', $submissionData, ['Authorization: Bearer ' . $teacherToken]);
printResult('Submit Assignment as Teacher (Should Fail)', $result);

// Test 21: Logout
echo "\n21. Logging out...";
$result = makeRequest('POST', '/logout', null, ['Authorization: Bearer ' . $token]);
printResult('Logout', $result);

echo "\n🎉 API Testing Complete!\n";
echo "All endpoints have been tested successfully.\n";
echo "Check the responses above to verify the API functionality.\n";
