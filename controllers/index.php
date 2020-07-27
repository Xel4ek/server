<?php
class Controller_index extends Controller_base
{
    function index() {
//        var_dump($this->registry);
//        echo '<br/>';
//        $this->registry['sample'] = 'sample_1_05';
        $model = $this->registry['model'];
        $this->registry['T'] = 680;
//        var_dump($model->composition());
        $test = new Alloy($this->registry);
        $data = $test->carbide(36 * 3600);
        echo json_encode($data);
    }
}
