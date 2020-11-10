<?php

class Controller_sample extends Controller_base
{
    function index()
    {
        echo 'hello';
    }

    public function info()
    {
        if (isset($this->registry['steal'])) {
            $info = $this->registry['steal']->info();
            echo json_encode($info);
        } else {
            echo '{}';
        }
    }
}
