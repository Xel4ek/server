<?php
class Controller_radius extends Controller_base
{
    function index() {
        var_dump($this->registry);
        echo '<br/>';
//        $this->registry['sample'] = 'sample_1_05';
//        $model = $this->registry['model'];
//        $this->registry['model'] = $model;
//        $this->registry['T'] = 680;
//        var_dump($model->composition());
//        $test = new Alloy($this->registry);
//        $data = $test->carbide(36 * 3600);
//        echo json_encode($data);
    }
    public function carbide(){
//        $postData = file_get_contents('php://input');
//        $data = json_decode($postData, true);
//        $this->registry['T'] = $data['T'];
//        $this->registry['structure_types'] = $data['struct'];
//        $steal = new Steal($this->registry);
//        var_dump($this->registry);
//        var_dump($steal->radius());

        $steal = $this->registry['steal'];
//        var_dump($steal);
//        var_dump($steal->get_optimal_time());
        echo json_encode($steal->radius());
//         var_dump( $this->registry['sample']);
//         var_dump($data);
//         var_dump($this->registry);
    }
    public function test(){
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
//         $this->registry['sample'] = $data['id'];
        $this->registry['T'] = $data['T'];
        $this->registry['structure_types'] = $data['struct'];
        $steal = new Steal($this->registry);
//        var_dump($this->registry);
//        var_dump($steal->radius());
        echo json_encode($steal->radius());
//         var_dump( $this->registry['sample']);
//         var_dump($data);
//         var_dump($this->registry);
    }
}
