<?php
namespace App;

class FavoriteRepository {
    public function add($userId, $iconId) { return true; }
    public function getFavs($userId) { return array(); }
}
