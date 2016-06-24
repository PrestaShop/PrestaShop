<?php

interface HttpConfigureInterface
{
    /**
     * Process form to go to next step
     */
    public function processNextStep();

    /**
     * Validate current step
     */
    public function validate();

    /**
     * Display current step view
     */
    public function display();
}
