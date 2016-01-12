<?php

namespace PrestaShopBundle\Service\LogHandler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\Container;

class LogHandler extends AbstractProcessingHandler
{
    protected $container;

    public function __construct(Container $container, $level = Logger::DEBUG, $bubble = true)
    {
        $this->container = $container;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        $logger = $this->container->get('prestashop.adapter.legacy.logger');
        $logger->add($record['message'], $record['level']);
    }
}
