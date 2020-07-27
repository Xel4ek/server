<?php


class Austenite extends Alloy
{
    protected $dislocation_density = 7 * 1e11;
    protected $sigma020 = 67.8;
    protected $hall_petch = 224;
    protected $dis_param = ['alpha' => 0.5, 'M' => 3.1, 'G' => 8.4e4, 'b' => 2.5e-10,'scale' => .1];


}