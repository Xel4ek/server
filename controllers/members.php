<?php

Class Controller_members Extends Controller_base {
    function index() {
        echo 'Default index of the `members` controllers';
    }
    function view() {
        echo 'You are viewing the members/view request';
    }
    function say() {
        $test = new Model($this->registry);
//        $test->macro_structure();
//        var_dump($this->registry['options']);
//        $res = $test->table($this->registry['options']);
//        var_dump($res);
    }

}