<?php
namespace App;

class UserRepository {
    public function findByEmail($email) { return null; }
    public function register($user, $pass) { return true; }
    public function login($user, $pass) { return false; }
}
