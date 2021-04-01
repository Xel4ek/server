<?php


class Composition
{
    private $db;
    private $phase_list;
    private $registry;
    private $compound;
    private $pivot;
    private $points;
    private $custom_composition;

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->compound = $this->registry['compound'];
        $this->prepare_db();
        $this->set_phases();
    }

    private function composition_cr_ni_eq($c)
    {
        $cr_eq = (isset($c['Cr']) ? $c['Cr'] : 0)
            + 0.01 * (isset($c['Si']) ? $c['Si'] : 0)
            + 0.01 * (isset($c['Mo']) ? $c['Mo'] : 0)
            + 0.01 * (isset($c['Al']) ? $c['Al'] : 0)
            + 0.7 * (isset($c['Nb']) ? $c['Nb'] : 0)
            + 0.01 * (isset($c['Ti']) ? $c['Ti'] : 0)
            + 0.01 * (isset($c['W']) ? $c['W'] : 0)
            + 0 * (isset($c['V']) ? $c['V'] : 0);

        $ni_eq = (isset($c['Ni']) ? $c['Ni'] : 0)
            + 0.01 * (isset($c['Mn']) ? $c['Mn'] : 0)
            + 35 * (isset($c['C']) ? $c['C'] : 0)
            + 20 * (isset($c['N']) ? $c['N'] : 0)
            + 0.01 * (isset($c['Cu']) ? $c['Cu'] : 0)
            + 0.01 * (isset($c['Co']) ? $c['Co'] : 0);
        return array('Cr' => $cr_eq * 100, 'Ni' => $ni_eq * 100);
    }

    private function prepare_db()
    {
        $compounds = $this->registry['model']->compounds_list();
        $points = array();
        foreach ($compounds as $compound => $data) {
            $cr_ni = $this->composition_cr_ni_eq($data);
            if (!in_array($cr_ni, $points)) {
                $points[$compound] = $this->composition_cr_ni_eq($data);
            }
        }
        $this->pivot = $this->composition_cr_ni_eq($this->compound);
        $point = $this->pivot;
        $this->points = $points;
//        var_dump($this->points);
        uasort($points, function ($a, $b) use ($point) {
            $dif = abs($a['Cr'] - $point['Cr']) > abs($b['Cr'] - $point['Cr']);

            return $dif === 0 ? 0 : ($dif > 0 ? 1 : -1);
        });
        $points_4 = array_slice($points, 0, 4, true);
        uasort($points_4, function ($a, $b) use ($point) {
            $dif = abs($a['Ni'] - $point['Ni']) > abs($b['Ni'] - $point['Ni']);
            return $dif === 0 ? 0 : ($dif > 0 ? -1 : 1);
        });
        array_pop($points_4);
        foreach (array_keys($points_4) as $point) {
            $this->db[$point] = $this->registry['model']->get_composition_by_name($point);
        }
    }

    private function set_phases()
    {
        $phases = array();
        foreach ($this->db as $phase => $data) {
            $phases = array_merge($phases, array_filter(array_keys($data), function ($field) {
                return strpos($field, 'BPW_') === 0;
            }));
        }
        $this->phase_list = array_unique($phases);
    }

    private function get_approximate($prop_name)
    {
        $x = +$this->pivot['Cr'];
        $y = +$this->pivot['Ni'];
        $keys = array_keys($this->db);
        $xa = +$this->points[$keys[0]]['Cr'];
        $ya = +$this->points[$keys[0]]['Ni'];
        $za = +$this->db[$keys[0]][$prop_name];
        $xb = +$this->points[$keys[1]]['Cr'];
        $yb = +$this->points[$keys[1]]['Ni'];
        $zb = +$this->db[$keys[1]][$prop_name];
        $xc = +$this->points[$keys[2]]['Cr'];
        $yc = +$this->points[$keys[2]]['Ni'];
        $zc = +$this->db[$keys[2]][$prop_name];
        $z = $za + (
                ($y - $ya) * (
                    ($xb - $xa) * ($zc - $za) - ($xc - $xa) * ($zb - $za)
                )
                - ($x - $xa) * (
                    ($yb - $ya) * ($zc - $za) - ($yc - $ya) * ($zb - $za)
                )
            ) / (
                ($xb - $xa) * ($yc - $ya) - ($xc - $xa) * ($yb - $ya)
            );
        return $z < 0 ? 0 : $z;
    }

    private function fix_phase($arr)
    {
        $total = 0;
        $dominate_phase = '';
        $dominate_value = 0;
        foreach ($arr as $name => $data) {
            $total += $data;
            if ($dominate_value < $data) {
                $dominate_phase = $name;
                $dominate_value = $data;
            }
        }
        $dif = $total - 1.;
        $arr[$dominate_phase] = $dominate_value - $dif;
        return $arr;
    }

    private function set_element_list($phase_name)
    {
        $phases = array();
        $short_name = substr($phase_name, 2);
        foreach ($this->db as $phase => $data) {
            $phases = array_merge($phases, array_filter(array_keys($data), function ($field) use ($short_name) {
                return strpos($field, $short_name) === 0;
            }));
        }
//        var_dump($this->db);
//        var_dump($phases);
        return array_unique($phases);
    }

    public function approximate()
    {
//        var_dump($this->custom_composition);
        $phases = array();
        foreach ($this->phase_list as $phase) {
            $phases[$phase] = $this->get_approximate($phase);
        }
//        return $this->get_approximate('BPW_BCC_A2');

        $phases = $this->fix_phase($phases);
//        var_dump(array_reduce($phases, function ($a, $b) {
//            return $a + $b;
//        }));
        $composition = [];
//        var_dump($this->set_element_list('BPW_BCC_A2'));
        foreach ($phases as $phase => $data) {
            $composition = [];
            $elements = $this->set_element_list($phase);
            foreach ($elements as $element) {
                $composition[$element] = $this->get_approximate($element);
            }
            $composition = $this->fix_phase($composition);
            $phases = array_merge($phases, $composition);
//            var_dump($elements);
        }
        return $phases;
    }
}