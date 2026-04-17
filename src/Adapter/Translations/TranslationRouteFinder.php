<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Translations;

use Link;
use Module;
use PrestaShop\PrestaShop\Core\Module\ModuleRepositoryInterface;
use PrestaShopBundle\Exception\InvalidModuleException;
use PrestaShopBundle\Service\TranslationService;
use Symfony\Component\HttpFoundation\Request;
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
     * @return string
     */
    public function findRoute(Request $request)
    {
        $routeProperties = $request->request->all('form');
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
     * @return array of route parameters
     */
    public function findRouteParameters(Request $request): array
    {
        $routeProperties = $request->request->all('form');
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
        $module = $this->moduleRepository->getModule($moduleName);

        if (!($module->getInstance() instanceof Module)) {
            throw new InvalidModuleException($moduleName);
        }

        return $module->getInstance()->isUsingNewTranslationSystem();
    }
}
