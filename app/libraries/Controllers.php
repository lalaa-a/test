<?php
    class controller {
        // To load model
        public function model($model) {

            require_once '../app/models/' . $model . '.php';

            // Instantiate model and pass it to controller
            return new $model();
        }

        // To load view
        public function view($view, $data = []) {
            if(file_exists('../app/views/' . $view . '.php')) {               
                if (!empty($data)) {
                    extract($data);
                }
                require_once '../app/views/' . $view . '.php';
            }
            else {
                die('View does not exist');
            }
        }
    }
?>
