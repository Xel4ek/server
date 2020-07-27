<?php


class Perlite_type extends Alloy
{
    protected $sigma020 = 30;
    protected $hall_petch = 400;
    protected $dis_param = ['alpha' => 0.2, 'M' => 2.75, 'G' => 8.4e4, 'b' => 2.5e-10, 'scale' => 1/25];
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