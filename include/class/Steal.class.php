<?php


class Steal
{
    private $registry;
    private $alloys;
    private $critical_times;
    private $structures = array(
        '1' => 'Austenite',
        '2' => 'Martensite',
        '3' => 'Ferrite',
        '4' => 'Perlite',
        '5' => 'Upper_bainite',
        '6' => 'Lower_bainite',
    );
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->registry['steal'] = $this;
        $alloys = $this->registry['structure_types'];
//        $this->registry['T'] = 680;
        foreach ($alloys as $alloy){
            $registry = $this->registry;
            $struct_id = $alloy['id'];
            $class = $this->structures[$struct_id];
            $compound = new $class($registry);
            $this->alloys[] = array(
                 'name' => $alloy['name_en'],
                 'volume' => $alloy['value'],
                'compound' => $compound
            );
        }
        $this->registry['steal'] = $this;
    }
    public function get_optimal_time(){
        $data = array();
        foreach ($this->alloys as $alloy){
            $time = $alloy['compound']->critical_time();
            foreach ($time as $carbide => $value){
               if(!isset($data[$carbide]) || $data[$carbide] < $value['time'] ){
                    $data[$carbide] = $value['time'];
                }
            }
        }
        $this->critical_times = $data;
    }
    public function info(){
        $info = array();
        $prop['fields'] = [['name'=>'prop', 'title' => 'Свойство'], ['name' => 'value', 'title' => 'Значение']];
        foreach ($this->alloys as $alloy){
            $phase = $alloy['compound'];
            $name = $alloy['name'];
            $info[$name] = $phase->info();
            $info[$name]['props'] += $prop;
//             $info[$name]['props']['data'][] =  ['prop' => 'Условный предел текучести, σ<sub>0,2</sub>' , 'value'=> $this->sigma020 . " МПа"];
            $info[$name]['props']['data'][] =  ['prop' => 'Относительное удлинение, δ' , 'value'=> 0.2];
            $info[$name]['props']['data'][] =  ['prop' => 'Относительное сужение, ψ' , 'value'=> 0.5];
            $info[$name]['props']['data'][] =  ['prop' => 'Критическая температура хрупкости, T<sub>K<sub>0</sub></sub>' , 'value'=> 0.5];
//             $info[$name]['composition']['fields'] = [['name'=>'phase', 'title' => 'Фаза'], ['name' => 'value', 'title' => 'об. %']];
        }
        return $info;
    }
    public function radius(){
        $this->get_optimal_time();
        $out = array();
        foreach ($this->alloys as $alloy) {
            $compound = $alloy['compound'];
            $alloy_name = $alloy['name'];
            foreach ($this->critical_times as $carbide_name => $time) {
                $carbide = $compound->carbide($carbide_name, $time * 1.3);
                $out[$carbide_name][$alloy_name] = $carbide[$carbide_name];
                if (!isset($out[$carbide_name]['time'])) {
                    $out[$carbide_name]['time'] = $carbide['time'];
                }
            }
        }
        return $out;
    }
    public function cr_ni_eq(){
        $c = $this->registry['compound'];
        $cr_eq = (isset($c['Cr']) ? $c['Cr'] : 0)
            + 0 * (isset($c['Si']) ? $c['Si'] : 0)
            + 0 * (isset($c['Mo']) ? $c['Mo'] : 0)
            + 0 * (isset($c['Al']) ? $c['Al'] : 0)
            + 0.7 * (isset($c['Nb']) ? $c['Nb'] : 0)
            + 0 * (isset($c['Ti']) ? $c['Ti'] : 0)
            + 0 * (isset($c['W']) ? $c['W'] : 0)
            + 0 * (isset($c['V']) ? $c['V'] : 0);

        $ni_eq = (isset($c['Ni']) ? $c['Ni'] : 0)
            + 0 * (isset($c['Mn']) ? $c['Mn'] : 0)
            + 35 * (isset($c['C']) ? $c['C'] : 0)
            + 20 * (isset($c['N']) ? $c['N'] : 0)
            + 0 * (isset($c['Cu']) ? $c['Cu'] : 0)
            + 0 * (isset($c['Co']) ? $c['Co'] : 0);
//        return $c;
        return array('Cr'=>$cr_eq * 100, 'Ni' => $ni_eq * 100);
    }
}
