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
use Exception;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Exception\NotImplementedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigCommand extends Command
{
    // return values
    private const STATUS_OK = 0;
    private const STATUS_INVALID_ACTION = 1;
    private const STATUS_VALUE_REQUIRED = 2;
    private const STATUS_FAILED_SET = 3;
    private const STATUS_FAILED_REMOVE = 4;
    private const STATUS_INVALID_OPTIONS = 5;
    private const STATUS_FAILED_SHOPCONSTRAINT = 6;
    private const STATUS_LANG_REQUIRED = 7;
    private const STATUS_NOT_IMPLEMENTED = 8;
    private const STATUS_ERROR = 9;

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

    /**
     * @var int|null
     */
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

    protected function configure(): void
    {
        $this
            ->setName('prestashop:config')
            ->setDescription('Manage your configuration via command line')
            ->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)))
            ->addArgument('key', InputArgument::REQUIRED, 'Configuration key. Key can also use wildcards. To get all Prestashop configuration keys and values use `PS_*`')

            ->addOption('value', 'val', InputArgument::OPTIONAL, 'value to set', null)

            ->addOption('lang', 'l', InputArgument::OPTIONAL, 'in this language. this can be either language id or ISO 3166-2 alpha-2 (en, fr, fi...)', null)
            ->addOption('shopGroupId', 'g', InputArgument::OPTIONAL, 'in this shop group (if no shop group or shop options are given defaults to allShops)', null)
            ->addOption('shopId', 's', InputArgument::OPTIONAL, 'in this shop (if no shop group or shop options are given defaults to allShops)', null)
            ;
    }

    protected function init(InputInterface $input, OutputInterface $output): void
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
            $msg = $this->translator->trans(
                'Unknown configuration action. It must be one of these values: %actions%',
                ['%actions%' => implode(' / ', $this->allowedActions)],
                'Admin.Command.Notification'
            );
            throw new Exception($msg, self::STATUS_INVALID_ACTION);
        }

        $this->action = $action;

        $this->initShopConstraint();

        $this->initLanguage();
    }

    /**
     * init possible shopconstraints
     */
    private function initShopConstraint(): void
    {
        if ($this->input->getOption('shopId') && $this->input->getOption('shopGroupId')) {
            $msg = $this->translator->trans(
                'Both shopId and shopGroupId cannot be defined',
                [],
                'Admin.Command.Notification'
            );
            throw new Exception($msg, self::STATUS_INVALID_OPTIONS);
        }
        // init shopConstraint
        // TODO: this should check that shopId and shopGroupId are valid
        try {
            if ($this->input->getOption('shopGroupId')) {
                $this->shopConstraint = ShopConstraint::shopGroup((int) $this->input->getOption('shopGroupId'));
            } elseif ($this->input->getOption('shopId')) {
                $this->shopConstraint = ShopConstraint::shop((int) $this->input->getOption('shopId'));
            } else {
                $this->shopConstraint = ShopConstraint::allShops();
            }
        } catch (Exception $e) {
            $msg = $this->translator->trans(
                'Failed initializing ShopConstraint: %msg%',
                ['%msg%' => $e->getMessage()],
                'Admin.Command.Notification'
            );
            throw new Exception($msg, self::STATUS_FAILED_SHOPCONSTRAINT);
        }
    }

    /**
     * initialize language if the option was given
     */
    private function initLanguage(): void
    {
        $inputlang = $this->input->getOption('lang');
        if (!$inputlang) {
            return;
        }

        // all languages
        $onlyActive = true;
        $onlyShopId = is_null($this->shopConstraint->getShopId()) ? $this->shopConstraint->getShopId() : $this->shopConstraint->getShopId()->getValue();
        $languages = $this->languageDataProvider->getLanguages($onlyActive, $onlyShopId);

        if (is_numeric($inputlang)) {
            // check that input language is valid
            $found = current(array_filter($languages, function ($item) use ($inputlang) {
                return isset($item['id_lang']) && $inputlang == $item['id_lang'];
            }));
        } else {
            $found = current(array_filter($languages, function (array $item) use ($inputlang) {
                return isset($item['iso_code']) && $inputlang == $item['iso_code'];
            }));
        }

        if (!$found) {
            $msg = $this->translator->trans(
                'Invalid language',
                [],
                'Admin.Command.Notification'
            );
            throw new Exception($msg, self::STATUS_INVALID_OPTIONS);
        }

        $this->idLang = (int) $found['id_lang'];
    }

    /**
     * Get and print out one configuration value
     */
    private function get(): void
    {
        $key = $this->input->getArgument('key');

        if (strpos($key, '*') !== false) {
            if (!$this->idLang) {
                $msg = $this->translator->trans(
                    'Using wildcards is language dependant, --lang option is required',
                    [],
                    'Admin.Command.Notification'
                );
                throw new Exception($msg, self::STATUS_LANG_REQUIRED);
            }
            try {
                $keys = array_filter($this->configuration->keys(), function ($v) use ($key) {
                    return fnmatch($key, $v);
                });
            } catch (NotImplementedException $e) {
                $msg = $this->translator->trans(
                    'Not implemented yet',
                    [],
                    'Admin.Command.Notification'
                );
                throw new Exception($msg, self::STATUS_NOT_IMPLEMENTED);
            } catch (Exception $e) {
                $msg = $this->translator->trans(
                    'Could not load all configuration keys: %msg%',
                    ['%msg%' => $e->getMessage()],
                    'Admin.Command.Notification'
                );
                throw new Exception($msg, self::STATUS_ERROR);
            }
        } else {
            $keys[] = $key;
        }

        foreach ($keys as $key) {
            $value = $this->configuration->get($key, null, $this->shopConstraint);

            // non language values will be just strings
            // but for language dependant values the response is an array
            if (is_array($value)) {
                if (!$this->idLang) {
                    $msg = $this->translator->trans(
                        '%key% is language dependant, --lang option is required',
                        ['%key%' => $key],
                        'Admin.Command.Notification'
                    );
                    throw new Exception($msg, self::STATUS_LANG_REQUIRED);
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
    }

    /**
     * Set and print out one configuration value
     */
    private function set(): void
    {
        $key = $this->input->getArgument('key');
        $newvalue = $this->input->getOption('value');

        // new value is required for set
        if (is_null($newvalue)) {
            $msg = $this->translator->trans(
                'Value required for action "set"',
                [],
                'Admin.Command.Notification'
            );
            throw new Exception($msg, self::STATUS_VALUE_REQUIRED);
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
            $msg = $this->translator->trans(
                'Failed setting value: %msg%',
                ['%msg%' => $e->getMessage()],
                'Admin.Command.Notification'
            );
            throw new Exception($msg, self::STATUS_FAILED_SET);
        }

        $this->get();
    }

    /**
     * Remove one configuration value
     */
    private function remove(): void
    {
        $key = $this->input->getArgument('key');

        try {
            // remove does not support removing only one language, use default
            // lang if not defined
            $this->idLang = $this->configuration->get('PS_LANG_DEFAULT');
            // this will give the user at least some backup
            $this->get();
            $this->configuration->remove($key);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $msg = $this->translator->trans(
                'Failed removing: %msg%. Original message: %msg%',
                ['%msg%' => $msg],
                'Admin.Command.Notification'
            );
            throw new Exception($msg, self::STATUS_FAILED_REMOVE);
        }

        $this->output->writeln(
            $this->translator->trans(
                '%key% removed',
                ['%key%' => $key],
                'Admin.Command.Notification'
            )
        );
    }

    /**
     * Main execute. Calls the method defined by action
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->init($input, $output);
            $this->{$this->action}();
        } catch (Exception $e) {
            $this->displayMessage($e->getMessage(), 'error');

            return $e->getCode();
        }

        return self::STATUS_OK;
    }

    /**
     * Helper for showing a nice error message
     */
    protected function displayMessage(string $message, string $type = 'info'): void
    {
        $this->output->writeln(
            $this->formatter->formatBlock($message, $type, true)
        );
    }
}
