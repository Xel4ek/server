<?php


class Martensite extends Alloy
{
    protected $dislocation_density = 5 * 1e14;
    protected $sigma020 = 50;
    protected $hall_petch;
    protected $dis_param = ['alpha' => 0.25, 'M' => 3, 'G' => 8.0e4, 'b' => 2.5e-10, 'scale' => .01];
    function __construct($registry)
    {
        parent::__construct($registry);
        $this->hall_petch = 300 * pow(0.067 , -1/2);
    }
}