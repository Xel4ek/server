<?php


class Controller_composition extends Controller_base
{

    function index()
    {
        echo 'Default index of the `composition` controllers';
    }

    function test()
    {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        $this->registry['compound'] = $data['compound'];
        $this->registry['steel_type'] = $data['steel_type']['name'];
        $this->registry['T'] = 655;
//        var_dump($data);
        $compound = new Composition($this->registry);
        $test = $compound->approximate();
        var_dump($test);
//        var_dump($compound->test());
    }

    function analyse()
    {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        $this->registry['compound'] = $data['compound'];
        $this->registry['steel_type'] = $data['steel_type']['name'];
        $this->registry['T'] = $data['T'];
//        $this->registry['T'] = 680;
//        $res = $this->registry['steel']->cr_ni_eq();
//        $this->registry['structure_types'] = ['id'=> '6', 'name_ru' => '123', 'name_en' => 456, 'value'=>100];
        if (!isset($this->registry['steel'])) {
            new Steel($this->registry);
        }
//        var_dump($this->registry['steel']);

//        $res = $this->registry['steel']->custom_composition();
//        var_dump($this->registry['steel']);
//        var_dump($this->registry['steel']->get_approximate('BPW_BCC_A2_1'));
    }
}