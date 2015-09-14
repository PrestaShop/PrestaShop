<?php

class Adapter_Translator
{
    public function l($string, $context)
    {
        return Translate::getFrontTranslation($string, $context);
    }
}
