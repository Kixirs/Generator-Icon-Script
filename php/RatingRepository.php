<?php
namespace App;

class RatingRepository {
    public function save($userId, $iconId, $rating) {
        return true;
    }

    public function hasRated($userId, $iconId) {
        return true;
    }

    public function getAverage($iconId) {
        return 4.7;
    }
}
