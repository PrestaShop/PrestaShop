<?php

class BuildException extends Exception
{
    /** @var string */
    protected $message = 'Can not build the release';
}