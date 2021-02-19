<?php

namespace Tests\Integration\Core\Form\IdentifiableObject\Handler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerFactoryInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FormHandlerFactory implements FormHandlerFactoryInterface
{
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $isDemoModeEnabled;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param TranslatorInterface $translator
     * @param bool $isDemoModeEnabled
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        TranslatorInterface $translator,
        $isDemoModeEnabled
    ) {
        $this->hookDispatcher = $hookDispatcher;
        $this->translator = $translator;
        $this->isDemoModeEnabled = $isDemoModeEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function create(FormDataHandlerInterface $dataHandler)
    {
        $formHandler = new FormHandler(
            $dataHandler,
            $this->hookDispatcher,
            $this->translator,
            $this->isDemoModeEnabled
        );

        return new FormHandlerChecker($formHandler);
    }
}
