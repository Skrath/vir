<?php
namespace vir;

require_once('classes/abilities.php');
require_once('classes/stats.php');

class character {

    public $stats;
    public $ability_groups;

    public function __construct() {
        $this->stats = new primary_stats();

        $this->stats->Agility = 7;

        $this->ability_groups = new ability_groups();

        $this->ability_groups->calculate($this->stats);
    }
}