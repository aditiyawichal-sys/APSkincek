<?php

/**
 * SkinCheck - Main Entry Point
 */

// Start session
session_start();

// Autoload core files manually for this basic setup
require_once __DIR__ . '/../app/controllers/Controller.php';

// Include routes
require_once __DIR__ . '/../routes/web.php';
