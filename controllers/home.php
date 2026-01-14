<?php
/**
 * Home Controller
 * Handles public pages: home, browse, item details
 */

function index() {
    $page = 'home';
    $page_title = 'Home';
    
    load_layout('public', VIEWS_PATH . '/pages/public/home.php', [
        'page' => $page,
        'page_title' => $page_title
    ]);
}

function browse() {
    $page = 'browse';
    $page_title = 'Browse Items';
    
    // TODO: Fetch items from database
    $items = [];
    
    load_layout('public', VIEWS_PATH . '/pages/public/browse.php', [
        'page' => $page,
        'page_title' => $page_title,
        'items' => $items
    ]);
}

function details() {
    $page = 'item_details';
    $page_title = 'Item Details';
    
    // TODO: Fetch item by ID
    $item_id = $_GET['id'] ?? null;
    
    if (!$item_id) {
        redirect('browse');
    }
    
    load_layout('public', VIEWS_PATH . '/pages/public/item_details.php', [
        'page' => $page,
        'page_title' => $page_title,
        'item_id' => $item_id
    ]);
}
