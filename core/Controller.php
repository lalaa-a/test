<?php
    class Controller {
        // To load model
        public function model($model) {
            require_once '../app/models/' . $model . '.php';
            // Instantiate model and pass it to controller
            return new $model();
        }

        // To load view
        public function view($view, $data = []) {
            // Check if view file exists
            if(file_exists('../app/views/' . $view . '.php')) {
                // Extract data array to make variables available in view
                extract($data);
                require_once '../app/views/' . $view . '.php';
            } else {
                die('View does not exist: ' . '../app/views/' . $view . '.php');
            }
        }
    }
?>
