<?php
/**
 * Student Dashboard Controller
 * Handles student dashboard view and data
 */

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/db.php';
requireStudent();

/**
 * Student Dashboard Index
 */
function index() {
    $page = 'student_dashboard';
    $page_title = 'Student Dashboard';
    
    load_layout('student', VIEWS_PATH . '/pages/student/dashboard.php', [
        'page' => $page,
        'page_title' => $page_title
    ]);
}
