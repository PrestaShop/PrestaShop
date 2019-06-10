<?php

namespace PrestaShop\PrestaShop\Core\Domain\Theme\Exception;

class FailedToEnableThemeModuleException extends ThemeException
{
    /**
     * @var string
     */
    private $moduleName;

    public function __construct(
        $moduleName,
        $message = '',
        $code = 0,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
}
