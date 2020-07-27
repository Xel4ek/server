<?php


class Ferrite extends Alloy
{
    protected $dislocation_density = 3 * 1e11;
    protected $sigma020 = 20; //MPA
    private $hardnes;
    protected $hall_petch = 600;
    protected $dis_param = ['alpha' => 0.2, 'M' => 2.75, 'G' => 8.4e4, 'b' => 2.5e-10, 'scale' => .1];

}
