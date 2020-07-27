<?php


class Diffusion
{
    private $el;
    private $matrix;
    private $D;
    private $E;
    private $diffusionDb = array(
        'C' => array('D' => 0.01,  'E' =>200000),
        'Cr' => array('D' => .000852, 'E' => 250800),
        'Mo' => array('D' => .0000785, 'E' => 226000),
        'V' => array('D' => .00034,'E' => 22100),
        'Fe' => array('D' => 0.118, 'E' => 281500),
        'Ni' => array('D' => .00014, 'E' => 245800),
        'Mn' => array('D'=> .00016, 'E' => 224500));
    const R = 8.314;
    function __construct($el, Phase $matrix)
    {
        $this->el = $el;
        $this->matrix = $matrix;
        $this->D = isset($this->diffusionDb[$el]['D']) ? $this->diffusionDb[$el]['D'] : 0;
        $this->E = isset($this->diffusionDb[$el]['E']) ? $this->diffusionDb[$el]['E'] : 0;
    }
    public function value($T){
        return $this->D * exp(-$this->E / (Diffusion::R * ($T + 273.18)));
    }
    public function asString($T){
        $D = $this->value($T);
        $str = explode('E', sprintf("%0.2E",$D));
        return $str[0] == 0 ? '---' : $str[0] . '⋅10<sup>' . $str[1] . '</sup>';
    }
}
