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

namespace PrestaShop\PrestaShop\Adapter\Translations;

use Link;
use Module;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepositoryInterface;
use PrestaShopBundle\Exception\InvalidModuleException;
use PrestaShopBundle\Service\TranslationService;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class TranslationRouteFinder finds the correct route for translations.
 */
class TranslationRouteFinder
{
    /**
     * Mails translations type.
     */
    public const MAILS = 'mails';

    /**
     * Modules translations type.
     */
    public const MODULES = 'modules';

    /**
     * Email body translations type.
     */
    public const BODY = 'body';

    /**
     * Themes translations type.
     */
    public const THEMES = 'themes';

    /**
     * @var TranslationService
     */
    private $translationService;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var ModuleRepositoryInterface
     */
    private $moduleRepository;

    /**
     * @param TranslationService $translationService
     * @param Link $link
     * @param ModuleRepositoryInterface $moduleRepository
     */
    public function __construct(
        TranslationService $translationService,
        Link $link,
        ModuleRepositoryInterface $moduleRepository
    ) {
        $this->translationService = $translationService;
        $this->link = $link;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * Finds the correct translation route out of given query.
     *
     * @param ParameterBag $query
     *
     * @return string
     */
    public function findRoute(ParameterBag $query)
    {
        $routeProperties = $query->get('form');
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $route = 'admin_international_translation_overview';

        switch ($propertyAccessor->getValue($routeProperties, '[translation_type]')) {
            case self::MAILS:
                if (self::BODY === $propertyAccessor->getValue($routeProperties, '[email_content_type]')) {
                    $language = $propertyAccessor->getValue($routeProperties, '[language]');
                    $route = $this->link->getAdminLink(
                        'AdminTranslations',
                        true,
                        [],
                        [
                            'lang' => $language,
                            'type' => self::MAILS,
                            'selected-emails' => self::BODY,
                            'selected-theme' => $propertyAccessor->getValue($routeProperties, '[theme]'),
                            'locale' => $this->translationService->langToLocale($language),
                        ]
                    );
                }

                break;

            case self::MODULES:
                $moduleName = $propertyAccessor->getValue($routeProperties, '[module]');

                // If module is not using the new translation system -
                // generate a legacy link for it
                if (!$this->isModuleUsingNewTranslationSystem($moduleName)) {
                    $language = $propertyAccessor->getValue($routeProperties, '[language]');
                    $route = $this->link->getAdminLink(
                        'AdminTranslations',
                        true,
                        [],
                        [
                            'type' => self::MODULES,
                            'module' => $moduleName,
                            'lang' => $language,
                        ]
                    );
                }

                break;
        }

        return $route;
    }

    /**
     * Finds parameters for translation route out of given query.
     *
     * @param ParameterBag $query
     *
     * @return array of route parameters
     */
    public function findRouteParameters(ParameterBag $query)
    {
        $routeProperties = $query->get('form');
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $language = $propertyAccessor->getValue($routeProperties, '[language]');

        $parameters = [
            'lang' => $language,
            'type' => $propertyAccessor->getValue($routeProperties, '[translation_type]'),
            'locale' => $this->translationService->langToLocale($language),
        ];

        switch ($propertyAccessor->getValue($routeProperties, '[translation_type]')) {
            case self::THEMES:
                $parameters['selected'] = $propertyAccessor->getValue($routeProperties, '[theme]');

                break;

            case self::MAILS:
                $emailContentType = $propertyAccessor->getValue($routeProperties, '[email_content_type]');

                if (self::BODY === $emailContentType) {
                    $parameters = [];
                }

                break;

            case self::MODULES:
                $moduleName = $propertyAccessor->getValue($routeProperties, '[module]');
                $parameters['selected'] = $moduleName;

                if (!$this->isModuleUsingNewTranslationSystem($moduleName)) {
                    $parameters = [];
                }

                break;
        }

        return $parameters;
    }

    /**
     * Checks if module is using the new translation system.
     *
     * @param string $moduleName
     *
     * @return bool
     */
    private function isModuleUsingNewTranslationSystem($moduleName)
    {
        $module = $this->moduleRepository->getInstanceByName($moduleName);

        if (!($module instanceof Module)) {
            throw new InvalidModuleException($moduleName);
        }

        return $module->isUsingNewTranslationSystem();
    }
}
