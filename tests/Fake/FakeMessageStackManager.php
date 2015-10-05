<?php

namespace PrestaShop\PrestaShop\tests\Fake;

use PrestaShop\PrestaShop\Core\Foundation\Log\MessageStackManager;

class FakeMessageStackManager extends MessageStackManager
{
    public function getErrorQueue()
    {
        return $this->errorQueue;
    }

    public function getWarningQueue()
    {
        return $this->warningQueue;
    }

    public function getInfoQueue()
    {
        return $this->infoQueue;
    }

    public function getSuccessQueue()
    {
        return $this->successQueue;
    }
}
