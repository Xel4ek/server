<?php

Class Controller_table Extends Controller_base {
    function index() {
        echo 'Default index of the `members` controllers';
    }
    function show() {
        $model = $this->registry['model'];
        $out = $model->table();
        echo json_encode($out);
        $model->compound();
//        var_dump($model);
    }
    function  all() {
        $model = $this->registry['model'];;
        $out = $model->tables();
        echo json_encode($out);
    }
    function macro_structure() {
        $model = $this->registry['model'];;
        $out = $model->macro_structure();
        echo json_encode($out);
    }
}
