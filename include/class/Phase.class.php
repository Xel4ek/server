<?php


class Phase
{
    private $molar_mass = array(
        'C' => 12.0107,
        'Cr' => 51.9961,
        'Mn' => 54.938044,
        'Mo' => 95.96,
        'Ni' => 58.6934,
        'Si' => 28.0855,
        'V' => 50.9415,
        'P' => 30.973762,
        'Fe' => 55.845,
        'Cu' => 63.546,
        'Co' => 58.933195,
    );
    private $name;
    private $composition_w;
    private $composition_a;
    private $share;
    private $control;
    private $time;
    private $critical_radius;
    private $matrix;

    public function __construct($name, $share, $composition)
    {
        $this->name = $name;
        $this->share = $share;
        $this->set_composition($composition);
        $this->get_radius();
    }
//     public function info(Phase $matrix, $T){
//         $table= array();
//         return $this->composition_table($matrix, $T);
//     }
    public function info($T){
        $table = array(
            'fields' => array(
                array('name'=>'element', 'title' => 'Элемент'), 
                array('name'=> 'atom', 'title' => 'вес. %'),
//                 array('name'=>'diffusion', 'title' => 'D, м<sup>2</sup>с<sup>-1</sup>')
            ),
         );
        foreach ($this->composition_w as $el => $c){
//             $diffusion = new Diffusion($el, $this->matrix);
//             $D = $diffusion->asString($T);
            $table['data'][] = array('element'=> $el, 'atom'=>round($c * 100, 3));
        }
        $prop = array();
        $prop['fields'] = [['name'=>'prop', 'title' => 'Свойство'], ['name' => 'value', 'title' => 'Значение']];
//         $prop['data'] = 
        return ['composition' => $table, 'props' => $prop];
    }
    public function carbides_info($T){
        $info = $this->info($T);
        $prop = &$info['props']['data'];
        $prop[] = ['prop' => 'Лимитирующий элемент' , 'value'=> $this->control['element']];
        $time = new Custom_time($this->control['time']);
        $prop[] = ['prop' => 'Критическое время' , 'value'=> "$time"];
        return $info;
    }
    private function get_radius(){
        $count = $this->share['particle_count'];
        $volume = $this->share['volume'];
        $this->critical_radius = pow($volume / $count * 3 / 4 / pi(), 1 / 3);
    }
    private function set_composition($composition){
        $this->composition_w = $composition;
        $sum = 0;
        foreach ($this->composition_w as $el => $weight){
            $atom = $weight / $this->molar_mass[$el];
            $sum += $atom;
            $this->composition_a[$el] = $atom;
        }
        foreach ($this->composition_a as $key => $value){
           $this->composition_a[$key] = $value / $sum;
        }
//        var_dump($composition);
    }
    public function radius(Phase $matrix, $T){
        $this->get_critical_time($matrix, $T);
        $data = $this->control;
        $data['volume'] = $this->share['volume'];
        $data['matrix'] = $matrix;
        $this->matrix = $matrix;
        $data['c'] = $this->composition_a[$this->control['element']];
        return array(
            'control' => $this->control,
            'get' => function ($time) use ($data) {
//            var_dump($data);
                return sqrt(2 * $data['D'] *
                    ($data['c'] * $data['volume']) /
                    ($data['c']  - $data['matrix']->atom[$data['element']]) *
                    ($time <= $data['time'] ? $time : $data['time']));
            }
        );
    }
    public function  get_critical_time(Phase $matrix, $T)
    {
        $composition = $this->composition_a;
        $control =  array('element'=> '?', 'D' => 0, 'time' => 0);
        foreach ($composition as $el => $value) {
            $diffusion = new Diffusion($el, $matrix);
            $D = $diffusion->value($T);
            if ($D != 0) {
                $time = pow($this->critical_radius, 2) / 2 / $D *
                    ($composition[$el] - $matrix->atom[$el]) /
                    ($composition[$el] * $this->share['volume']);
                if ($time > $control['time']){
                    $control = array('element'=> $el, 'D' => $D, 'time' => $time);
                }
            }
        }
//        var_dump($m);
//        var_dump($control);
//        echo $control['time'] / 3600;
//        var_dump($this->critical_radius);
//        echo '<br/>';
//        var_dump($composition);
//        echo '<br/>';
        if($this->time < $control['time']){
            $this->time = $control['time'];
        }

        $this->control = $control;
    }

    public function __get($name) {
        $route = array(
            'atom' => $this->composition_a ,
            'weight' => $this->composition_w,
            'name' => $this->name,
            'time' => $this->time );
        if(isset($route[$name])) {
            return $route[$name];
        }
        return null;
    }
}
