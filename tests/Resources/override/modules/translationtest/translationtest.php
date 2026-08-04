<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class translationtestOverride
{
    public function getContent()
    {
        return $this->l('Wording from module override');
    }
}
