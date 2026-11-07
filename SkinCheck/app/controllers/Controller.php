<?php

require_once '../config/database.php';

class Controller {
    // Load model
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        $database = new Database();
        $db = $database->getConnection();
        return new $model($db);
    }

    // Load view
    public function view($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Check if view file exists
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist.');
        }
    }
}
