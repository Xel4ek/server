<?php

class Controller_table extends Controller_base
{
    function index()
    {
        echo 'Default index of the `members` controllers';
    }

    function show()
    {
        $model = $this->registry['model'];
        $out = $model->table();
        $model->compound();
        echo json_encode($out);
//        var_dump($model);
    }

    function all()
    {
        $model = $this->registry['model'];;
        $out = $model->tables();
        echo json_encode($out);
    }

    function macro_structure()
    {
        $model = $this->registry['model'];;
        $out = $model->macro_structure();
        echo json_encode($out);
    }

    function element_list()
    {
//        $moack = array(
//            'Fe' => [
//                'name' => 'Fe',
//                'maxValue' => 100,
//                'value' => 100,
//                'active' => false
//            ],
//            'Co' => [
//                'name' => 'Co',
//                'maxValue' => 1.2,
//                'value' => 0,
//            ],
//            'Ni' => [
//                'name' => 'Ni',
//                'maxValue' => 5.2,
//                'value' => 0,
//            ]
//        );
        $list = $this->registry['model']->element_range();
        echo json_encode($list);
    }

}
