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
        $steel = $this->registry['steel'];
        echo json_encode($steel->radius());
    }
    public function test(){
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
//         $this->registry['sample'] = $data['id'];
        $this->registry['T'] = $data['T'];
        $this->registry['structure_types'] = $data['struct'];
        $steel = new Steel($this->registry);
//        var_dump($this->registry);
//        var_dump($steel->radius());
        echo json_encode($steel->radius());
//         var_dump( $this->registry['sample']);
//         var_dump($data);
//         var_dump($this->registry);
    }
}
