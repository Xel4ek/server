<?php


class Steel
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
    private $compound;
    private $T;

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->registry['steel'] = $this;
        $alloys = $this->registry['structure_types'];
        $composition = new Composition($this->registry);
        $this->compound = $composition->approximate();
        $this->registry['approximate'] = $this->compound;
        foreach ($alloys as $alloy) {
            $registry = $this->registry;
            $struct_id = $alloy['id'];
            $class = $this->structures[$struct_id];
            $this->T = $this->registry['T'];
            $compound = new $class($registry);
            if (!property_exists($this->registry['dislocations'], $class)) {
                $temp = $this->registry['dislocations'];
                $temp[$class] = $alloy['value'];
                $this->registry['dislocations'] = $temp;
            }
            $this->alloys[] = array(
                'name' => $alloy['name_en'],
                'volume' => $alloy['value'],
                'compound' => $compound
            );
        }
    }

    /*
     * Расчет оптимального времяни
     * */
    public function get_optimal_time()
    {
        $data = array();
        foreach ($this->alloys as $alloy) {
            $time = $alloy['compound']->critical_time();
            foreach ($time as $carbide => $value) {
                if (!isset($data[$carbide]) || $data[$carbide] < $value['time']) {
                    $data[$carbide] = $value['time'];
                }
            }
        }
        $this->critical_times = $data;
    }

    /*
     * Формарования информации о стали
     * */
    public function info()
    {
        $info = array();
        $prop['fields'] = [['name' => 'prop', 'title' => 'Свойство'], ['name' => 'value', 'title' => 'Значение']];
        foreach ($this->alloys as $alloy) {
            $phase = $alloy['compound'];
            $name = $alloy['name'];
            $info[$name] = $phase->info();
            $info[$name]['props'] += $prop;
            $info[$name]['props']['data'][] = ['prop' => 'Относительное удлинение, δ', 'value' => 0.2];
            $info[$name]['props']['data'][] = ['prop' => 'Относительное сужение, ψ', 'value' => 0.5];
            $info[$name]['props']['data'][] = ['prop' => 'Критическая температура хрупкости, T<sub>K<sub>0</sub></sub>', 'value' => 0.5];
        }
        return $info;
    }

    /*
     * Расчет размеров карбидов для оптимального времяни
     * */
    public function radius()
    {
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

    /*
     * Состав стали
     * */
    public function compound()
    {
        return $this->compound;
    }
}
