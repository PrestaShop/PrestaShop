<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Translations;

use Link;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepositoryInterface;
use PrestaShopBundle\Service\TranslationService;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class TranslationRouteFinder finds the correct route for translations
 */
class TranslationRouteFinder
{
    /**
     * Mails translations type
     */
    const MAILS = 'mails';

    /**
     * Modules translations type
     */
    const MODULES = 'modules';

    /**
     * Email body translations type
     */
    const BODY = 'body';

    /**
     * Themes translations type
     */
    const THEMES = 'themes';

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
     * Finds the correct translation route out of given query
     *
     * @param ParameterBag $query
     *
     * @return string
     */
    public function findRoute(ParameterBag $query)
    {
        $routeProperties = $query->get('modify_translations');
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
                    $route = $this->link->getAdminLink(
                        'AdminTranslations',
                        true,
                        [],
                        [
                            'type' => self::MODULES,
                            'module' => $moduleName,
                        ]
                    );
                }

                break;
        }

        return $route;
    }

    /**
     * Finds parameters for translation route out of given query
     *
     * @param ParameterBag $query
     *
     * @return array of route parameters
     */
    public function findRouteParameters(ParameterBag $query)
    {
        $routeProperties = $query->get('modify_translations');
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
                $parameters['selected'] = $emailContentType;

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
     * Checks if module is using the new translation system
     *
     * @param string $moduleName
     *
     * @return bool
     */
    private function isModuleUsingNewTranslationSystem($moduleName)
    {
        $module = $this->moduleRepository->getInstanceByName($moduleName);

        return $module->isUsingNewTranslationSystem();
    }
}
