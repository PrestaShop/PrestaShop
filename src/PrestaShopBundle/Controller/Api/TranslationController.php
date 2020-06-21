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
use PrestaShopBundle\Api\QueryTranslationParamsCollection;
use PrestaShopBundle\Exception\InvalidLanguageException;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\TranslationService;
use PrestaShopBundle\Translation\Exception\UnsupportedLocaleException;
use PrestaShopBundle\Translation\View\TranslationApiTreeBuilder;
use PrestaShopBundle\Translation\View\TreeBuilder;
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
    public function listDomainTranslationAction(Request $request)
    {
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
            $queryParams = $queryParamsCollection->getQueryParams();

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

            $catalog = $this->translationService->listDomainTranslation($locale, $domain, $theme, $search, $module);
            $info = [
                'Total-Pages' => ceil(count($catalog['data']) / $queryParams['page_size']),
            ];

            $catalog['info'] = array_merge(
                $catalog['info'],
                [
                    'locale' => $locale,
                    'domain' => $domain,
                    'theme' => $theme,
                    'total_translations' => count($catalog['data']),
                    'total_missing_translations' => 0,
                ]
            );

            foreach ($catalog['data'] as $message) {
                if (empty($message['xlf']) && empty($message['db'])) {
                    ++$catalog['info']['total_missing_translations'];
                }
            }

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

            if (!in_array($type, ['modules', 'themes', 'mails', 'mails_body', 'back', 'others', 'front'])) {
                throw new Exception(sprintf("The 'type' parameter '%s' is not valid", $type));
            }

            if (in_array($type, ['modules', 'themes']) && empty($selected)) {
                throw new Exception("The 'selected' parameter is empty.");
            }

            $treeType = $type;
            $selectedTheme = null;

            if ('themes' === $type) {
                $selectedTheme = $selected;
            }

            if ('modules' === $type) {
                $moduleProvider = $this->container->get('prestashop.translation.external_module_provider');

                // this will magically update the module provider inside the factory (yes, this is horrible)
                $moduleProvider->setModuleName($selected);
                $treeType = $moduleProvider->getIdentifier();
            }

            return $this->jsonResponse(
                $this->getTree($lang, $treeType, $selectedTheme, $search),
                $request
            );
        } catch (Exception $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }
    }

    /**
     * Route to edit translation.
     *
     * @AdminSecurity("is_granted(['create', 'update'], request.get('_legacy_controller'))")
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
            foreach ($translations as $translation) {
                if (empty($translation['theme'])) {
                    $translation['theme'] = null;
                }

                try {
                    $lang = $translationService->findLanguageByLocale($translation['locale']);
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
            }

            $this->clearCache();

            return new JsonResponse($response, 200);
        } catch (BadRequestHttpException $exception) {
            return $this->handleException($exception);
        }
    }

    /**
     * Route to reset translation.
     *
     * @AdminSecurity("is_granted(['create', 'update'], request.get('_legacy_controller'))")
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
     * @param $content
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
     * @param $content
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
     * Returns a translation domain tree
     *
     * @param string $lang
     * @param string $type "themes", "modules", "mails", "mails_body", "back" or "others"
     * @param string|null $theme Selected theme name. Set only if type = "themes"
     * @param string|array|null $search Search string
     *
     * @return array
     */
    private function getTree(string $lang, string $type, ?string $theme = null, $search = null)
    {
        $catalogue = $this->translationService->getTranslationsCatalogue($lang, $type, $theme, $search);

        $locale = $this->translationService->langToLocale($lang);

        return $this->getCleanTree($locale, $catalogue, $theme, $search);
    }

    /**
     * Make final tree.
     *
     * @param string $locale
     * @param array $catalogue
     * @param string|null $theme
     * @param string|null $search
     * @param string|null $module
     *
     * @return array
     */
    private function getCleanTree($locale, array $catalogue, $theme, $search = null, $module = null)
    {
        $apiBuilder = new TranslationApiTreeBuilder($this->container->get('router'), new TreeBuilder());

        return $apiBuilder->buildDomainTreeForApi($catalogue, $locale, $theme, $search, $module);
    }
}
