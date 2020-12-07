<?php


class Ferrite extends Alloy
{
//    protected $dislocation_density = 3 * 1e11;
    protected $dislocation_density = 1 * 1e15;
    protected $sigma020 = 20; //MPA
    private $hardnes;
    protected $hall_petch = 600;
    protected $props  = ['M' => 2.75, 'G' => 8.4e4, 'b' => 2.5e-10];
    protected $dis_param = ['alpha' => 0.2];
    protected $particle_per_dis = 100;
    protected $solid_hardening = ['Si' => 86,
        'Mn' => 33,
        'Cr' => 31,
        'Ni' => 30,
        'Cu' => 39,
        'Mo' => 11,
        'V' => 3,
        'Ti' => 82,
        'Al' => 60,
        'P' => 690,
        'N' => 4670,
        'С' => 4670,
    ];

}
