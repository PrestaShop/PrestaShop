<?php

namespace PrestaShopBundle\Bridge;

trait AddActionTrait
{
    public function addAction(string $type, string $action, array $config = []): void
    {
        switch ($type) {
            case self::ACTION_TYPE_BULK:
                $this->controllerConfiguration->bulkActions[$action] = $config;
                break;

            case self::ACTION_TYPE_ROW:
                $this->controllerConfiguration->actions[] = $action;
                break;

            case self::ACTION_TYPE_HEADER_TOOLBAR:
                $this->controllerConfiguration->pageHeaderToolbarButton[$action] = $config;
                break;

            case self::ACTION_TYPE_LIST_HEADER_TOOLBAR:
                $this->controllerConfiguration->toolbarButton[$action] = $config;
                break;

            default:
                throw new \Exception('This type doesn\'t exist');
        }
    }
}
