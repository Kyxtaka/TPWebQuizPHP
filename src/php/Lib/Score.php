<?php
namespace Lib;
class Score {
    private int $score;
    private int $total;

    public function __construct(int $score, int $total) {
        $this->score = $score;
        $this->total = $total;
    }

    public function getScore() {
        return $this->score;
    }
    public function getTotal() {
        return $this->total;
    }

    public function incrementScore(int $value) {
        $this->score+= $value;
    }
    public function getPercentage() {
        return $this->score / $this->total * 100;
    }
}
?>