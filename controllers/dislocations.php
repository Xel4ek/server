<?php

class Controller_dislocations extends Controller_base
{
    function index()
    {
        var_dump($this->registry['approximate']);
        echo '<br/>';
    }

}