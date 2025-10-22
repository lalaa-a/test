<?php
    class Core {
        // URL format -->. /controller/method/params
        protected $currentController = 'Home';
        protected $currentMethod = 'index';
        protected $params = [];

        public function __construct(){
            //print_r($this->getUrl());

            $url = $this->getUrl();

            // Look in controllers for first value
            if($url && isset($url[0]) && $url[0] != NULL){
                if(file_exists('../app/controllers/' . ucwords($url[0]) . '.php')){
                    // If exists, set as controller
                    $this->currentController = ucwords($url[0]);
                    // Unset 0 index
                    unset($url[0]);
                }
            }

            // Require the controller
            require_once '../app/controllers/' . $this->currentController . '.php';
            // Instantiate controller class
            $this->currentController = new $this->currentController;

            // Check whether or not a method exists in controller
            if($url && isset($url[1])){
                if(method_exists($this->currentController, $url[1])){
                    $this->currentMethod = $url[1];
                    // Unset 1 index
                    unset($url[1]);
                }
            }

            // Get params
            $this->params = $url ? array_values($url) : [];

            // Call a callback with array of params
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);   
        }

        public function getUrl(){
            if(isset($_GET['url'])){
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);

                return $url;
            }
        }
    }
?>