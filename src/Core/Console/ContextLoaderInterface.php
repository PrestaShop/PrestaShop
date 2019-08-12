<?php

namespace PrestaShop\PrestaShop\Core\Console;

use Symfony\Component\Console\Input\InputInterface;

interface ContextLoaderInterface
{
    public function loadConsoleContext(InputInterface $input);
}
