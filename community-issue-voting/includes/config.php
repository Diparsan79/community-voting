<?php

declare(strict_types=1);

// Database configuration
const DB_HOST = 'localhost';
const DB_NAME = 'community_voting';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

// App configuration
const APP_NAME = 'Community Issue Voting Platform';
const BASE_URL = '/community-issue-voting'; // Adjust if deployed in subdirectory

// Upload configuration
const UPLOAD_DIR = __DIR__ . '/../uploads/';
const UPLOAD_BASE_URL = BASE_URL . '/uploads/';
const MAX_UPLOAD_BYTES = 2 * 1024 * 1024; // 2MB
const ALLOWED_IMAGE_MIME = [
    'image/jpeg',
    'image/png',
    'image/gif',
];

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');