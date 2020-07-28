<?php


class Martensite extends Alloy
{
    protected $dislocation_density = 4e15;
    protected $sigma020 = 50;
    protected $hall_petch;
    protected $props = ['M' => 3, 'G' => 8.0e4, 'b' => 2.5e-10];
    protected $dis_param = ['alpha' => 0.25];
    protected $precipitation_param = ['F' => 1.25];

    protected $solid_hardening = [
        'Mn' => 540,
        'Cr' => 622,
        'Ni' => 708,
        'Cu' => 320,
        'Mo' => 2362,
        'V' => 2362,
        'Ti' => 2628,
        'Al' => 196,
    ];
    protected $particle_per_dis = 7;
    function __construct($registry)
    {
        parent::__construct($registry);
        $this->hall_petch = 300 * pow(0.067 , -1/2);
    }
    protected function solid_hardening($t) {
        $compound = $this->matrix_compound('atom');
        $hardening = 0;
        foreach ($compound($t) as $el => $mole) {
            if (array_key_exists($el, $this->solid_hardening)) {
                $hardening += $mole * $this->solid_hardening[$el] ** 2;
            }
        }
//        $hardening = $compound($t)['Fe'];
        return $hardening ** (1/2);
    }
}