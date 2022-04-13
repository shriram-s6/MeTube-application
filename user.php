<?php

class User {

    private $connect, $sqlData;

    public function __construct($connect, $username) {
        $this->$connect = $connect;
    }

}


?>