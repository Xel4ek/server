<?php

class Controller_sample extends Controller_base
{
    function index()
    {
        echo 'hello';
    }

    public function info()
    {
        if (isset($this->registry['steel'])) {
            $info = $this->registry['steel']->info();
            echo json_encode($info);
        } else {
            echo '{}';
        }
    }
}
