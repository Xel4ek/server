<?php


class Model
{
    private $registry;
    private $db;
    private  $user = 'id14072386_user';
    private $pwd = ')Uu34su6yb&krkF(';
    private $db_name = 'id14072386_data';
    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->registry['model'] = $this;
    }

    private function connect()
    {
//        $user = 'strength';
//        $pwd = 'Fdx%Y5_KNz2rbS$9qnr';
//        $db_name = 'strength';
//        $user = 'id14072386_user';
//        $pwd = ')Uu34su6yb&krkF(';
//        $db_name = 'id14072386_data_1';
        try {
            $this->db = new PDO('mysql:host=localhost;dbname=' . $this->db_name, $this->user, $this->pwd);
        } catch (PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
    }

    public function say()
    {
        $this->connect();
    }

    public function macro_structure()
    {
        $this->connect();
        $sth = $this->db->prepare("SELECT `id` , `name_ru`, `name_en` FROM `macro_structure`");
        $sth->execute();
        $this->db = null;
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function table()
    {
        $options = $this->registry['options'];
        $query = $options['name'];
        $table = $this->registry['sample'] = $options['sample'];
        $min = isset($options['min']) ? $options['min'] : '0';
        $max = isset($options['max']) ? $options['max'] : '10000';
        $this->connect();
        $sth = $this->db->prepare("SELECT `COLUMN_NAME`
          FROM `INFORMATION_SCHEMA`.`COLUMNS`
          WHERE `TABLE_SCHEMA`='$this->db_name' AND 
          `TABLE_NAME`='$table' 
          AND `COLUMN_NAME` LIKE '$query%'");
        $sth->execute();
        $needle = array();
        foreach ($sth->fetchAll() as $field) {
            if (stripos($field[0], $query) === 0) {
                $needle[] = $field[0];
            }
        }
//        var_dump($needle);
        $sth = $this->db->prepare("SELECT `T`, " . join(', ', $needle) . " 
            FROM `$table`
            WHERE `T` > " . $min . " AND" . " `T` < " . $max);
        $sth->execute();
//        var_dump($sth->errorInfo());
        $out = array();
        $clearing = array();
        while (($row = $sth->fetch(PDO::FETCH_ASSOC)) != false) {
            $temperature = $row['T'];
            unset($row['T']);
            foreach ($row as $key => $value) {
                if ($key !== 'T') {
                    $out[$key][] = ['y' => $value * 100, 'x' => $temperature];
                    if (isset($clearing[$key])) {
                        $clearing[$key] += $value;
                    } else {
                        $clearing[$key] = $value;
                    }
                }
            }
        }
        foreach ($clearing as $key => $reason) {
            if ($reason < 1e-6) {
                unset($out[$key]);
            }
        }
        $this->db = null;

        return $out;
    }

    public function compound()
    {
        $table = $this->registry['sample'];
        $this->registry['compound'] =$this->get_coumpound_by_name($table);
    }

    private function get_coumpound_by_name($table){
        $this->connect();
        $sth = $this->db->prepare("SELECT *
          FROM `$table`
          WHERE `T` = IFNULL((
              SELECT MAX(`T`) 
              FROM $table 
              ), 0)");
        $sth->execute();
        $this->db = null;
        $out = array();
        foreach ($sth->fetch(PDO::FETCH_ASSOC) as $key => $value) {
            if ($value != 0) {
                $code = explode('_', $key);
                $index = count($code) - 1;
                $el = ucfirst(strtolower($code[$index]));
                $out[$el] = $value;
            }
        }
        return array_slice($out, 3);
    }
    public function tables()
    {
        $this->connect();
        $sth = $this->db->prepare("SELECT `table_id` , `name` FROM `tables`");
        $sth->execute();
        $out = array();
        while (($entry = $sth->fetch(PDO::FETCH_ASSOC)) != null) {
            $out[$entry['name']][] = $entry['table_id'];
        }
        $this->db = null;
        return $out;
    }

    public function composition()
    {
        $table = $this->registry['sample'];
        return $this->get_composition_by_name($table);
    }
    private function avg($rhs, $lhs, $shoulder, $diff){
        return $rhs + ($lhs - $rhs) / $diff * $shoulder;
    }

    public function get_composition_by_name($table) {
        $T = $this->registry['T'];
        $this->connect();
        $sth = $this->db->prepare("
            SELECT * FROM `$table`
            WHERE (
                `id` =  IFNULL((SELECT MAX(`id`) from $table
                    WHERE `T` >= $T), 0) OR
                `id` = IFNULL((SELECT MIN(`id`) from $table
                    WHERE `T` <= $T), 0)
            )
            ORDER BY `T`;
            ");
        $sth->execute();
        $this->db = null;
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
            $index = count($data) - 1;
            $rhs = +$data[$index - 1]['T'];
            $lhs = +$data[$index]['T'];
            $diff = $lhs - $rhs;
            $shoulder = $T - $rhs;
            $composition = array();
            foreach ($data[$index - 1] as $field => $value){
                $avg = $this->avg($value, $data[$index][$field], $shoulder, $diff);
                if ($avg != 0) {
                    $composition[$field] = $avg;
                }
            }
            return $composition;
    }

    public function compounds_list()
    {
//        $composition = ['composition' => $this->registry['compound'], 'type' => $this->registry['steal_type']];
        $samples = $this->get_samples_names();
        $compounds = array();
        foreach ($samples as $sample) {
            $compounds[$sample] = $this->get_coumpound_by_name($sample);
        }
        return $compounds;
    }
    private function get_samples_names() {
        $steal_type = $this->registry['steal_type'];
        $this->connect();
        $sth = $this->db->prepare("
            SELECT `table_id` 
            FROM `tables` 
            WHERE `name` = '" . $steal_type . "'");
        $sth->execute();
        $this->db = null;
        $res = array();
        foreach ($sth->fetchAll() as $entry) {
            $res[] = $entry['table_id'];
        }
        return $res;
    }

    public function element_range()
    {
        $steal_type = $this->registry['options']['steal_type'];
        $this->connect();
        $sth = $this->db->prepare("
            SELECT `element_name`, `min_value`, `max_value` 
            FROM `steal_info` 
            WHERE `steal_name` = '" . $steal_type . "'
            ORDER BY `element_name`");
        $sth->execute();

        $this->db = null;
        $res = array();
        foreach ($sth->fetchAll(PDO::FETCH_ASSOC) as $entry) {
            $res[$entry['element_name']] = $entry;
        }
        return $res;
    }
}
