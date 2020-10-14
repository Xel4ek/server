<?php


class Controller_composition extends Controller_base
{

    function index()
    {
        echo 'Default index of the `composition` controllers';
    }

    function test()
    {
        $this->registry['composition'] = 12;
//        $test = $this->registry['model']->interpolated_composition();
        $test = $this->registry['steal']->cr_ni_eq();
        var_dump($test);
//        echo 'model say ' . $test . '!';
    }

    function analyse()
    {
        $postData = file_get_contents('php://input');
        $data = json_decode($postData, true);
        $this->registry['compound'] = $data['compound'];
        $this->registry['steal_type'] = $data['steal_type']['name'];

        $res = $this->registry['steal']->cr_ni_eq();
        var_dump($res);
    }
}