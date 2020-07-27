<?php


class Austenite extends Alloy
{
    protected $dislocation_density = 7 * 1e11;
    protected $sigma020 = 67.8;
    protected $hall_petch = 224;
    protected $dis_param = ['alpha' => 0.5, 'M' => 3.1, 'G' => 8.4e4, 'b' => 2.5e-10,'scale' => .1];
    protected $solid_hardening = ['Si' => 31.3,
        'Mn' => 1.,
        'Cr' => 1.,
        'Ni' => 1.3,
        'Cu' => 16.8,
        'Mo' => 31.3,
        'Ti' => 15.2,
        'Al' => 3.8,
        'С' => 47,
    ];
    protected function matrix_compound($type = 'atom') {
        return parent::matrix_compound($type);
    }
}