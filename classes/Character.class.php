<?php
namespace vir;

require_once('classes/abilities.php');
require_once('classes/stats.php');

class Character {

    public $primary_stats;
    public $ability_groups;

    public function __construct() {
        $this->primary_stats = new primary_stats();

        // for testing
        $this->primary_stats->Agility = 7;

        $this->ability_groups = new ability_groups();

        $this->ability_groups->calculate($this->primary_stats);
    }
}