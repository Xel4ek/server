<?php


abstract class Alloy
{
    private $density = array(
        'BPW_BCC_A2' => 7.64755,
        'BPW_BCC_A2_1' => 7.64755,
        'BPW_BCC_A2_2' => 7.64755,
        'BPW_CEMENTITE' => 5.704446,
        'BPW_FCC_A1' => 7.64922,
        'BPW_FCC_A1_2' => 7.64922,
        'BPW_FCC_A1_3' => 7.64922,
        'BPW_FCC_A1_1'=> 7.64922,
        'BPW_LIQUID' => 7.64922,
        'BPW_M23C6' => 7.61925,
        'BPW_M7C3' => 7.04718,
        'BPW_MC_SHP' => 5.64761
    );
    private $registry;
    private $composition;
    private $phases;
    private $matrix;
    private $carbide = [];
    private $radius;
    private $T;
    protected $compound;
    protected $dislocation_density;
    protected $sigma020;
    protected $d = 30; // microns
    protected $hall_petch;
    protected $dis_param = ['alpha' => 0, 'M' => 0, 'G' => 0, 'b' => 0, 'scale' => 0];
    protected $solid_hardening;
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->T = $registry['T'];
        $this->get_composition($this->registry['model']->composition($this->T));
        $this->get_matrix();
        $this->get_carbide();
        $this->compound = $this->registry['compound'];
//        $this->composition = $this->registry['model']->composition();
    }
    public function test() {
//        var_dump($this->get_matrix());
//        var_dump(array_keys($this->composition));
//        $this->get_matrix();
//        $this->get_carbide();
//        foreach ($this->carbide as $carbide){
//            $carbide->get_critical_time($this->matrix, $this->T);
//            echo '<br/>';
//        }
//        $test = $this->carbide[0]->radius($this->matrix, $this->T);
//        var_dump($test(81278));
//        $this->carbide();
//        var_dump($test(2225.2293957823));
//        var_dump($this->matrix);
//        var_dump($this->carbide);
//        var_dump($this->phases);
//        $this->active_phase();
//        $carbide = $this->carbide['CEMENTITE'];
//        var_dump($carbide->atom);
//        foreach ($carbide->atom as $el => $value){
//            $dif = new Diffusion($el, NUll);
//            echo "$el {$dif->value(500)} ";
//        }

//        foreach ($this->carbide as $carbide){
//            $carbide->control_flow($this->T);
//        }
    }
    public function gb_herdness(){
        return $this->hall_petch * pow($this->d , -1/2);
    }
    private function toScientific($num, $prec = 2) {
        $data = explode('E', sprintf("%0." . $prec . "E",$num));
        $exp = str_replace('+', '', $data[1]);
        return $data[0] == 0 ? '---' : $data[0] . '⋅10<sup>' . $exp . '</sup>';
    } 
    public function info(){
        $info = array();
        $info['composition']['fields'] = [['name'=>'phase', 'title' => 'Фаза'], ['name' => 'value', 'title' => 'об. %']];
        $carbides = $this->carbide;
        foreach ($carbides as $carbide){
            $field = &$info['carbides'][$carbide->name];
            $field = $carbide->carbides_info($this->T);
            array_unshift($field['props']['data'], ['prop' => 'Колличевство частиц' , 'value'=> $this->toScientific($this->phases["BPW_{$carbide->name}"]['particle_count']) . " м<sup>-3</sup>"]);
            $field['props']['data'][] = ['prop' => 'Плотность' , 'value'=> round($this->density["BPW_{$carbide->name}" ], 2) . " г⋅см<sup>-3</sup>"];
            $info['composition']['data'][] = ['phase' => $carbide->name, 'value' => round($this->phases["BPW_{$carbide->name}"]['volume'] * 100 , 3) ];
        }

        $matrix = $this->matrix;
        $matrix_info =  $matrix->info($this->T);
         $matrix_info['composition']['fields'][] = ['name'=>'diffusion', 'title' => 'D, м<sup>2</sup>с<sup>-1</sup>'];
//         $data = array();
        foreach($matrix_info['composition']['data'] as &$entry){
            $el = $entry['element'];
            $diffusion = new Diffusion($el, $this->matrix);
            $D = $diffusion->asString($this->T);
            $entry['diffusion'] = $D;            
        }
        unset($entry);
        $prop = array();
        $prop[] = ['prop' => 'Плотность' , 'value'=> round($this->density["BPW_{$matrix->name}"] ,2). " г⋅см<sup>-3</sup>"];
        $prop[] = ['prop' => 'Плотность дислокаций', 'value'=> $this->toScientific($this->dislocation_density) . " м<sup>-2</sup>"];
        $info['matrix'][$matrix->name] = $matrix_info;
        $info['composition']['data'][] = ['phase' => $matrix->name, 'value' => round($this->phases["BPW_{$matrix->name}"]['volume'] * 100, 3)];
        $info['matrix'][$matrix->name]['props']['data'] = $prop;
        $info['props']['data'][] =  ['prop' => 'Условный предел текучести, σ<sub>0,2</sub>' , 'value'=> round($this->sigma020, 2) . " МПа"];
        $info['props']['data'][] =  ['prop' => 'Зернограничное упрочнение' , 'value'=> round($this->gb_herdness(),2) . " МПа"];
        $info['props']['data'][] =  ['prop' => 'Дислокационное упрочнение' , 'value'=> round($this->dis_herdness(),2) . " МПа"];
        $info['chart']['data'] = $this->charts();
        return $info;
    }
    private function charts($max_time = 2e6) {
//        $compound = $this->matrix_compound();
        $time = range(0, $max_time, $max_time / 21);
        $out = array();
        foreach ($time as $t) {
            $hardening = $this->solid_hardening($t);
            $out['solid'][] = ['y' => $hardening, 'x' => $t / 3600];
        }
        return $out;
//        return $this->compound;
    }
    protected function solid_hardening($t) {
        $compound = $this->matrix_compound();
        $hardening = 0;
        foreach ($compound($t) as $el => $weight) {
            if (array_key_exists($el, $this->solid_hardening)) {
                $hardening += $weight * 100 * $this->solid_hardening[$el];
            }
        }
//        $hardening = $compound($t)['Fe'];
        return $hardening;
    }
    protected function matrix_compound($type = 'weight'){
        $compound = array();
        foreach ($this->carbide as $name => $carbide) {
            $carbide_info = $carbide->radius($this->matrix, $this->T);
            $this->radius[$name] = $carbide_info['get'];
            $compound[$name] = $carbide->$type;
        }
        return function ($t) use ($compound) {
            $source = $this->compound;
            foreach ($source as $el => &$c){
                foreach ($this->radius as $name => $carbide_radius){
                    $cc = $compound[$name];
                    if (isset($cc[$el])) {
                        $c -= $cc[$el] * 4 / 3 * $carbide_radius($t) ** 3 * $this->get_particle_count($name) * pi();
                    }
                }
            }
            return $source;
        };
    }
    public function carbide($carbide_name, $max_time, $min_time = 0, $steps = 42){
        $step = ($max_time - $min_time) / $steps;
        $data = array();
        $time = range($min_time, $max_time, $step);
//        echo 'BPW_' . $carbide_name . ' alloy';
        $precision = $step < 3600 ? ceil(-log10($step / 3600)) + 1 : 1;
        foreach ($time as $t){
            $data['time'][] = round($t / 3600, $precision);
        }
//        foreach ($this->carbide as $carbide){
            $carbide = $this->carbide['BPW_' . $carbide_name];
            $carbide_info = $carbide->radius($this->matrix, $this->T);
            $func = $carbide_info['get'];
            foreach ($time as $t){
                $data[$carbide->name][] = $func($t) * 1e9;
            }
//        }
        return $data;
    }
    public function critical_time(){
        $time = array();
        foreach ($this->carbide as $carbide){
            $carbide_info = $carbide->radius($this->matrix, $this->T);
            $time[$carbide->name] = $carbide_info['control'];
        }
        return $time;
    }
    private function get_matrix(){
        $matrix = '';
        $value = 0;
        foreach ($this->composition as $key => $data){
            if(preg_match('/^BPW_/', $key)) {
                if ($value <  $data) {
                    $matrix = $key;
                    $value = $data;
                }
            }
        }
        $this->matrix = $this->phase( $matrix );
//        $this->matrix[$matrix] = $this->composition[$matrix];
    }
    private function phase($key){
        $phase = join('_', array_slice(explode('_', $key), 1));
//        echo "$phase ";
//        var_dump(explode('_', $key));
//        var_dump($this->composition);
        $fields = array_filter(array_keys($this->composition), function ($key) use ($phase) {
            return preg_match("/^W_$phase/", $key) & 1;
        });
        $composition = array();
        foreach ($fields as $field) {
            $code = explode('_', $field);
            $index = count($code) - 1;
            $el = ucfirst(strtolower($code[$index]));
            $composition[$el] = $this->composition[$field];
        }
//        echo "$phase ";
        return new Phase($phase, $this->phases[$key], $composition);
    }
    private function get_carbide(){
        foreach ($this->composition as $key => $value) {
            if(!preg_match("/_.CC|^T$|^W_|^id$|^V_/",$key)){
                $this->carbide[$key] = $this->phase( $key );
            }
        }
    }
    private function get_particle_count($phase){
        $density = $this->dislocation_density;
        if(!$density) {
            throw new Exception('$density undefined');
        }
        $particle_count = pow($density, 3 / 2);
        if(preg_match('/M23|M6/', $phase)){
            return $particle_count / 100;
        }
        return $particle_count;
    }
    private function avg($rhs, $lhs, $shoulder, $diff){
        return $rhs + ($lhs - $rhs) / $diff * $shoulder;
    }
//    private function  start_compound(){
//        $compound = $this->matrix->atom;
//        $name = $this->matrix->name;
//        array_walk($compound, function (&$value) use ($name) {
//            $value = $this->phases['BPW_' . $name]['volume'];
//        });
//        foreach ($this->carbide as $carbide){
//            $cc = $carbide->atom;
//            $name = $carbide->name;
//            array_walk($cc, function (&$value) use ($name) {
//                $value = $this->phases['BPW_' . $name]['volume'];
//            });
//            foreach ($compound as $el => &$c) {
//                if (isset($cc[$el])) {
//                    $c += $cc[$el];
//                }
//            }
//        }
//        $this->compound = $compound;
//        $this->compound = $this->registry['compound'];
//    }


    private function get_composition($data){
        $index = count($data) - 1;
        $rhs = +$data[$index - 1]['T'];
        $lhs = +$data[$index]['T'];
        $diff = $lhs - $rhs;
        $shoulder = $this->T - $rhs;
        $total_volume = 0;

        $this->composition = array();
        foreach ($data[$index - 1] as $field => $value){
            $avg = $this->avg($value, $data[$index][$field], $shoulder, $diff);
            if ($avg != 0) {
                $this->composition[$field] = $avg;
                if (preg_match("/^BPW_/", $field)){
                    $volume = $avg / $this->density[$field];
                    $total_volume += $volume;
                    $this->phases[$field] = ['weight' => $avg, 'volume' => $volume];
                    $this->phases[$field]['particle_count'] = $this->get_particle_count($field);
                }
            }
        }
        foreach ($this->phases as &$value){
           $value['volume'] /= $total_volume;
        }
//         public function hardness();
//        var_dump($this->phases);
//        echo "total $total_volume";
//        var_dump($this->composition);
    }

    public function dis_herdness()
    {
        return array_product($this->dis_param) * ($this->dislocation_density ** (1/2));
    }
}
