<?php

namespace PrestaShopBundle\Bridge;

interface AddActionInterface
{
    public const ACTION_TYPE_BULK = 'bulk';
    public const ACTION_TYPE_ROW = 'row';
    public const ACTION_TYPE_HEADER_TOOLBAR = 'header_toolbar';
    public const ACTION_TYPE_LIST_HEADER_TOOLBAR = 'list_header_toolbar';

    public function addAction(string $type, string $action, array $config = []): void;
}
