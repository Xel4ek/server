<?php


class Calculator
{
    private $matrix;
    private $carbide;
    private $T;
    private $particles = 1e20;
    public function __construct($matrix, $carbide, $T)
    {
        $this->matrix = $matrix;
        $this->carbide = $carbide;
        $this->T = $T;
    }
    public function set_particles($value){
       $this->particles = $value;
    }
    private function get_critical_size(){

    }
}