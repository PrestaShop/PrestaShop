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

namespace PrestaShopBundle\Controller\Api;

use Exception;
use PrestaShop\PrestaShop\Adapter\EntityTranslation\EntityTranslatorFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\CoreDomainProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\OthersProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use PrestaShopBundle\Api\QueryTranslationParamsCollection;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Exception\InvalidLanguageException;
use PrestaShopBundle\Form\Admin\Improve\International\Translations\ModifyTranslationsType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\TranslationService;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TranslationController extends ApiController
{
    /**
     * @var QueryTranslationParamsCollection
     */
    public $queryParams;

    /**
     * @var TranslationService
     */
    public $translationService;

    /**
     * Show translations for 1 domain & 1 locale given & 1 theme given (optional).
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listDomainTranslationAction(Request $request): JsonResponse
    {
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
            $queryParams = $queryParamsCollection->getQueryParams();

            /** @var TranslationService $translationService */
            $translationService = $this->container->get('prestashop.service.translation');

            $locale = $request->attributes->get('locale');
            $domain = $request->attributes->get('domain');
            $theme = $request->attributes->get('theme');
            $module = $request->query->get('module');
            $search = $request->query->get('search');

            try {
                $this->translationService->findLanguageByLocale($locale);
            } catch (InvalidLanguageException $e) {
                // If the locale is invalid, no need to call the translation provider.
                throw UnsupportedLocaleException::invalidLocale($locale);
            }

            if (ucfirst(OthersProviderDefinition::OTHERS_DOMAIN_NAME) === $domain) {
                $domain = OthersProviderDefinition::OTHERS_DOMAIN_NAME;
            }

            if (!empty($module)) {
                $providerDefinition = new ModuleProviderDefinition($module);
            } elseif (
                !empty($theme)
                // Default theme is not considered like other themes because its translations belong to the Core
                && ThemeProviderDefinition::DEFAULT_THEME_NAME !== $theme
            ) {
                $providerDefinition = new ThemeProviderDefinition($theme);
            } else {
                $providerDefinition = new CoreDomainProviderDefinition($domain);
            }

            $catalog = $translationService->listDomainTranslation($providerDefinition, $locale, $domain, $this->searchExpressionToArray($search));
            $info = [
                'Total-Pages' => ceil(count($catalog['data']) / $queryParams['page_size']),
            ];

            $catalog['data'] = array_slice(
                $catalog['data'],
                ($queryParams['page_index'] - 1) * $queryParams['page_size'],
                $queryParams['page_size']
            );

            return $this->jsonResponse($catalog, $request, $queryParamsCollection, 200, $info);
        } catch (Exception $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }
    }

    /**
     * Show tree for translation page with some params.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function listTreeAction(Request $request)
    {
        try {
            // params possibles:
            // lang : en, fr, etc.
            // type : themes, modules, mails, back, others
            // selected : classic, starterTheme, module name, subject (for email).

            $lang = $request->attributes->get('lang');
            $type = $request->attributes->get('type');
            $selected = $request->attributes->get('selected');

            $search = $request->query->get('search');

            if (!in_array($type, ProviderDefinitionInterface::ALLOWED_TYPES)) {
                throw new Exception(sprintf("The 'type' parameter '%s' is not valid", $type));
            }

            if (
                ProviderDefinitionInterface::TYPE_THEMES === $type
                && ModifyTranslationsType::CORE_TRANSLATIONS_CHOICE_INDEX === $selected
            ) {
                $type = ProviderDefinitionInterface::TYPE_FRONT;
            }

            if (
                in_array($type, [ProviderDefinitionInterface::TYPE_MODULES, ProviderDefinitionInterface::TYPE_THEMES])
                && empty($selected)
            ) {
                throw new Exception("The 'selected' parameter is empty.");
            }

            $tree = $this->getTree($lang, $type, $this->searchExpressionToArray($search), $selected);

            return $this->jsonResponse($tree, $request);
        } catch (Exception $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }
    }

    /**
     * Route to edit translation.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) or is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function translationEditAction(Request $request)
    {
        try {
            $decodedContent = $this->guardAgainstInvalidTranslationBulkRequest($request);

            $translations = $decodedContent['translations'];
            $this->guardAgainstInvalidTranslationEditRequest($translations);

            $translationService = $this->container->get('prestashop.service.translation');
            $response = [];
            $modifiedDomains = [];
            if (!empty($translations)) {
                $lang = null;
                foreach ($translations as $translation) {
                    if (empty($translation['theme'])) {
                        $translation['theme'] = null;
                    }

                    try {
                        if ($lang === null) {
                            $lang = $translationService->findLanguageByLocale($translation['locale']);
                        }
                    } catch (Exception $exception) {
                        throw new BadRequestHttpException($exception->getMessage());
                    }

                    $response[$translation['default']] = $translationService->saveTranslationMessage(
                        $lang,
                        $translation['domain'],
                        $translation['default'],
                        $translation['edited'],
                        $translation['theme']
                    );

                    $modifiedDomains[$translation['domain']] = true;
                }

                // this has to be done *before* retranslating
                $this->clearCache();

                $this->translateMultilingualContent(array_keys($modifiedDomains), $lang);
            }

            return new JsonResponse($response, 200);
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Route to reset translation.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller')) or is_granted('update', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function translationResetAction(Request $request)
    {
        try {
            $decodedContent = $this->guardAgainstInvalidTranslationBulkRequest($request);

            $translations = $decodedContent['translations'];
            $this->guardAgainstInvalidTranslationResetRequest($translations);

            $translationService = $this->container->get('prestashop.service.translation');
            $response = [];

            foreach ($translations as $translation) {
                if (!array_key_exists('theme', $translation)) {
                    $translation['theme'] = null;
                }

                try {
                    $lang = $translationService->findLanguageByLocale($translation['locale']);
                } catch (Exception $exception) {
                    throw new BadRequestHttpException($exception->getMessage());
                }

                $response[$translation['default']] = $translationService->resetTranslationMessage(
                    $lang->getId(),
                    $translation['domain'],
                    $translation['default'],
                    $translation['theme']
                );
            }

            $this->clearCache();

            return new JsonResponse($response, 200);
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    private function guardAgainstInvalidTranslationBulkRequest(Request $request)
    {
        $content = $request->getContent();

        $decodedContent = $this->guardAgainstInvalidJsonBody($content);

        if (empty($decodedContent) ||
            !array_key_exists('translations', $decodedContent) ||
            !is_array($decodedContent['translations'])
        ) {
            $message = 'The request body should contain a JSON-encoded array of translations';

            throw new BadRequestHttpException(sprintf('Invalid JSON content (%s)', $message));
        }

        return $decodedContent;
    }

    /**
     * @param array $content
     */
    private function guardAgainstInvalidTranslationEditRequest($content)
    {
        $message = 'Each item of JSON-encoded array in the request body should contain ' .
            'a "locale", a "domain", a "default" and a "edited" values. ' .
            'The item of index #%d is invalid.';

        array_walk($content, function ($item, $index) use ($message) {
            if (!array_key_exists('locale', $item) ||
                !array_key_exists('domain', $item) ||
                !array_key_exists('default', $item) ||
                !array_key_exists('edited', $item)
            ) {
                throw new BadRequestHttpException(sprintf($message, $index));
            }
        });
    }

    /**
     * @param array $content
     */
    protected function guardAgainstInvalidTranslationResetRequest($content)
    {
        $message = 'Each item of JSON-encoded array in the request body should contain ' .
            'a "locale", a "domain" and a "default" values. ' .
            'The item of index #%d is invalid.';

        array_walk($content, function ($item, $index) use ($message) {
            if (!array_key_exists('locale', $item) ||
                !array_key_exists('domain', $item) ||
                !array_key_exists('default', $item)
            ) {
                throw new BadRequestHttpException(sprintf($message, $index));
            }
        });
    }

    /**
     * Trigger translation of multilingual content in database according to which domains have been modified
     *
     * @param string[] $modifiedDomains List of modified domains
     * @param Lang $lang
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function translateMultilingualContent(array $modifiedDomains, Lang $lang)
    {
        if (in_array('AdminNavigationMenu', $modifiedDomains)) {
            $translator = $this->container->get('translator');

            // reset translator
            $translator->clearLanguage($lang->getLocale());

            // update menu items (tabs)
            (new EntityTranslatorFactory($translator))
                ->buildFromTableName('tab', $lang->getLocale())
                ->translate($lang->getId(), \Context::getContext()->shop->id);
        }
    }

    /**
     * Returns a translation domain tree
     *
     * @param string $lang
     * @param string $type "themes", "modules", "mails", "mails_body", "back", "front" or "others"
     * @param array $search Search strings
     * @param string|null $selectedValue Depends on the type. It's a theme name if type = "themes" or a module name if type = "modules"
     *
     * @return array
     *
     * @throws Exception
     */
    private function getTree(string $lang, string $type, array $search, ?string $selectedValue = null): array
    {
        $locale = $this->translationService->langToLocale($lang);
        $providerDefinitionFactory = $this->container->get('prestashop.translation.factory.provider_definition');

        return $this->translationService->getTranslationsTree(
            $providerDefinitionFactory->build($type, $selectedValue),
            $locale,
            $search
        );
    }

    /**
     * @param string|array $search
     *
     * @return array
     */
    private function searchExpressionToArray($search): array
    {
        if (is_array($search)) {
            return $search;
        }

        return empty($search) ? [] : [$search];
    }
}
