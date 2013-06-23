<?php
namespace vir;

class Level {
    use BasicConstruct, Calculable;

    public $experience = 0;

    private $experience_rate = 1;
    private $experience_to_next_level = 0;

    private function LevelPreConstruct() {
        $this->addConstructVar(['experience', 'experience_rate']);
    }

    public function increase_experience($amount) {
        for ($i = 1; $i < $this->level+1; $i++) {
            $this->experience_to_next_level += pow(100, 1 + ($i-1)/40);
        }

        $this->experience += $amount * $this->experience_rate;

        if ($this->experience >= $this->experience_to_next_level) {
            $this->level++;
            $this->increase_experience(0);
        }
    }
}