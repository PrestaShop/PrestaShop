<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use Employee;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigCommand extends Command
{
    // return values
    private const RET_OK = 0;
    private const RET_INVALID_ACTION = 1;
    private const RET_VALUE_REQUIRED = 2;
    private const RET_FAILED_SET = 3;
    private const RET_FAILED_REMOVE = 4;
    private const RET_INVALID_OPTIONS = 5;
    private const RET_FAILED_SHOPCONSTRAINT = 6;
    private const RET_LANG_REQUIRED = 7;

    private $allowedActions = [
        'get',
        'set',
        'remove',
    ];

    /**
     * @var FormatterHelper
     */
    protected $formatter;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var ShopConfigurationInterface
     */
    private $configuration;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    /**
     * @var LanguageDataProvider
     */
    private $languageDataProvider;

    private $action;
    private $idLang;

    public function __construct(
        TranslatorInterface $translator,
        LegacyContext $context,
        ShopConfigurationInterface $configuration,
        LanguageDataProvider $languageDataProvider
    ) {
        parent::__construct();
        $this->translator = $translator;
        $this->context = $context;
        $this->configuration = $configuration;
        $this->languageDataProvider = $languageDataProvider;

        // default to null for language
        $this->idLang = null;
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:config')
            ->setDescription('Manage your configuration via command line')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)))
            ->addArgument('key', InputArgument::REQUIRED, 'Configuration key')

            ->addOption('value', 'val', InputArgument::OPTIONAL, 'value to set', null)

            ->addOption('lang', 'l', InputArgument::OPTIONAL, 'in this language. this can be either language id or ISO 3166-2 alpha-2 (en, fr, fi...)', null)
            ->addOption('shopGroupId', 'g', InputArgument::OPTIONAL, 'in this shop group (if no shop group or shop options are given defaults to allShops)', null)
            ->addOption('shopId', 's', InputArgument::OPTIONAL, 'in this shop (if no shop group or shop options are given defaults to allShops)', null)
            ;
    }

    protected function init(InputInterface $input, OutputInterface $output)
    {
        $this->formatter = $this->getHelper('formatter');
        $this->input = $input;
        $this->output = $output;
        //We need to have an employee or the module hooks don't work
        //see LegacyHookSubscriber
        if (!$this->context->getContext()->employee) {
            //Even a non existing employee is fine
            $this->context->getContext()->employee = new Employee(42);
        }

        // check our action
        $action = $input->getArgument('action');
        if (!in_array($action, $this->allowedActions)) {
            $this->displayMessage(
                $this->translator->trans(
                    'Unknown configuration action. It must be one of these values: %actions%',
                    ['%actions%' => implode(' / ', $this->allowedActions)],
                    'Admin.Command.Notification'
                ),
                'error'
            );
            exit(self::RET_INVALID_ACTION);
        }
        $this->action = $action;

        // check ShopConstraint
        $this->initShopConstraint();

        // if language is provided check that it is valid
        $this->initLanguage();
    }

    /**
     * init possible shopconstraints
     */
    private function initShopConstraint()
    {
        if ($this->input->getOption('shopId') && $this->input->getOption('shopGroupId')) {
            $this->displayMessage(
                $this->translator->trans(
                    'Both shopId and shopGroupId cannot be defined',
                    [],
                    'Admin.Command.Notification'
                ),
                'error'
            );
            exit(self::RET_INVALID_OPTIONS);
        }
        // init shopConstraint
        // TODO: this should check that shopId and shopGroupId are valid
        try {
            if ($this->input->getOption('shopGroupId')) {
                $this->shopConstraint = ShopConstraint::shopGroup($this->input->getOption('shopGroupId'));
            } elseif ($this->input->getOption('shopId')) {
                $this->shopConstraint = ShopConstraint::shop($this->input->getOption('shopId'));
            } else {
                $this->shopConstraint = ShopConstraint::allShops();
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $this->displayMessage(
                $this->translator->trans(
                    'Failed initializing ShopConstraint: %msg%',
                    ['%msg%' => $msg],
                    'Admin.Command.Notification'
                ),
                'error'
            );
            exit(self::RET_FAILED_SHOPCONSTRAINT);
        }
    }

    /**
     * initialize language if the option was given
     */
    private function initLanguage()
    {
        $inputlang = $this->input->getOption('lang');
        if (!$inputlang) {
            return;
        }

        // all languages
        $onlyActive = true;
        $onlyShopId = is_null($this->shopConstraint->getShopId()) ? $this->shopConstraint->getShopId() : $this->shopConstraint->getShopId()->getValue();
        $languages = $this->languageDataProvider->getLanguages($onlyActive, $onlyShopId);

        $found = null;
        if (is_numeric($inputlang)) {
            // check that input language is valid
            $found = current(array_filter($languages, function ($item) use ($inputlang) {
                return isset($item['id_lang']) && $inputlang == $item['id_lang'];
            }));
        } else {
            $found = current(array_filter($languages, function ($item) use ($inputlang) {
                return isset($item['iso_code']) && $inputlang == $item['iso_code'];
            }));
        }

        if (!$found) {
            $this->displayMessage(
                $this->translator->trans(
                    'Invalid language',
                    [],
                    'Admin.Command.Notification'
                ),
                'error'
            );
            exit(self::RET_INVALID_OPTIONS);
        }

        $this->idLang = (int) $found['id_lang'];
    }

    /**
     * Get and print out one configuration value
     */
    private function get()
    {
        $key = $this->input->getArgument('key');
        $value = $this->configuration->get($key, null, $this->shopConstraint);

        // non language values will be just strings
        // but for language dependant values the response is an array
        if (is_array($value)) {
            if (!$this->idLang) {
                $this->displayMessage(
                    $this->translator->trans(
                        '%key% is language dependant, --lang option is required',
                        ['%key%' => $key],
                        'Admin.Command.Notification'
                    ),
                    'error'
                );
                exit(self::RET_LANG_REQUIRED);
            }
            $value = $value[$this->idLang];
        }

        $this->output->writeln(
            $this->translator->trans(
                '%key%="%value%"',
                ['%key%' => $key, '%value%' => $value],
                'Admin.Command.Notification'
            )
        );
    }

    /**
     * Set and print out one configuration value
     */
    private function set()
    {
        $key = $this->input->getArgument('key');
        $newvalue = $this->input->getOption('value');

        // new value is required for set
        if (is_null($newvalue)) {
            $this->displayMessage(
                $this->translator->trans(
                    'Value required for action "set"',
                    [],
                    'Admin.Command.Notification'
                ),
                'error'
            );
            exit(self::RET_VALUE_REQUIRED);
        }

        // make the value language array if lang is set
        if (!is_null($this->idLang)) {
            $newvalue = [
                $this->idLang => $newvalue,
            ];
        }

        // set the value
        try {
            $this->configuration->set($key, $newvalue, $this->shopConstraint);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $this->displayMessage(
                $this->translator->trans(
                    'Failed setting value: %msg%',
                    ['%msg%' => $msg],
                    'Admin.Command.Notification'
                ),
                'error'
            );
            exit(self::RET_FAILED_SET);
        }

        // and call get to print the value out
        $this->get();
    }

    /**
     * Remove one configuration value
     */
    private function remove()
    {
        $key = $this->input->getArgument('key');
        // this will give the user at least some backup
        $oldvalue = $this->configuration->get($key, null, $this->shopConstraint);

        try {
            $this->configuration->remove($key);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $this->displayMessage(
                $this->translator->trans(
                    'Failed removing: %msg%',
                    ['%msg%' => $msg],
                    'Admin.Command.Notification'
                ),
                'error'
            );
            exit(self::RET_FAILED_REMOVE);
        }

        $this->output->writeln(
            $this->translator->trans(
                '%key% removed (value was "%oldvalue%")',
                ['%key%' => $key, '%oldvalue%' => $oldvalue],
                'Admin.Command.Notification'
            )
        );
    }

    /**
     * Main execute. Calls the method defined by action
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input, $output);

        $action = $this->action;
        $this->$action();

        return self::RET_OK;
    }

    /**
     * Helper for showing a nice error message
     */
    protected function displayMessage(string $message, string $type = 'info')
    {
        $this->output->writeln(
            $this->formatter->formatBlock($message, $type, true)
        );
    }
}
