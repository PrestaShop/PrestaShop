<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Pscsx3241 extends Module
{
    public function __construct()
    {
        $this->name = 'pscsx3241';
        $this->tab = 'front_office_features';
        $this->version = 1.0;
        $this->author = 'PSCSX-3241';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = $this->l('Module PSCSX-3241');
        $this->description = $this->l('A module to test bug PSCSX-3241');
    }

    public function install()
    {
        if (parent::install() == false) {
            return false;
        }
        return true;
    }
}
