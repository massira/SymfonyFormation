<?php

namespace Config\Formation\Controllers;

class ConfigController{

    /**
     * @param string $name
     */
    public function sayHello($name)
    {
        print 'Hello '.$name;
    }
}