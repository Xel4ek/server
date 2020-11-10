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
        $list = $this->registry['model']->element_range();
        echo json_encode($list);
    }

}
