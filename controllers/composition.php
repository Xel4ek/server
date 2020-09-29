<?php


class Controller_composition extends Controller_base
{

    function index() {
        echo 'Default index of the `composition` controllers';
    }
    function test() {
        $this->registry['composition'] = 12;
//        $test = $this->registry['model']->interpolated_composition();
        $test = $this->registry['steal']->cr_ni_eq();
        var_dump($test);
//        echo 'model say ' . $test . '!';
    }
}