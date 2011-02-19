<?php

    class User {
        
        private $_role;
        
        public function __construct($role = "guest") {
            $this->_role = $role;
        }
        
        public function getRole() {
            return $this->_role;
        }
        
        public function setRole($role = "guest") {
            $this->_role = $role;
        }
        
        
    }

?>
