<?php
    class User {
        public $login;
        public $role;
        private $name;
    
        public function __construct($login, $role, $name) {
            $this->login = $login;
            $this->role = $role;
            $this->name = $name;
        }
    
        public function getInfo() {
            return "Login: {$this->login}, Role: {$this->role}, Name: {$this->name}";
        }
    }
    
    $person = new User("Test_login", "Test_role", "Test_name");
    echo $person->getInfo();

    echo $person->name;

?>