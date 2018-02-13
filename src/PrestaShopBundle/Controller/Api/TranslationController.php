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

namespace PrestaShopBundle\Controller\Api;

use Exception;
use PrestaShopBundle\Api\QueryTranslationParamsCollection;
use PrestaShopBundle\Service\TranslationService;
use PrestaShopBundle\Translation\View\TreeBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Show translations for 1 domain & 1 locale given & 1 theme given (optional)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listDomainTranslationAction(Request $request)
    {
        try {
            $queryParamsCollection = $this->queryParams->fromRequest($request);
            $queryParams = $queryParamsCollection->getQueryParams();

            $translationService = $this->container->get('prestashop.service.translation');

            $locale = $request->attributes->get('locale');
            $domain = $request->attributes->get('domain');
            $theme = $request->attributes->get('theme');

            $search = $request->query->get('search');

            $catalog = $translationService->listDomainTranslation($locale, $domain, $theme, $search);
            $info = array(
                'Total-Pages' => ceil(count($catalog['data']) / $queryParams['page_size'])
            );

            $catalog['info'] = array_merge($catalog['info'],
                array(
                    'locale' => $locale,
                    'domain' => $domain,
                    'theme' => $theme,
                    'total_translations' => count($catalog['data']),
                    'total_missing_translations' => 0,
                )
            );

            foreach ($catalog['data'] as $message) {
                if (empty($message['xliff']) && empty($message['database'])) {
                    $catalog['info']['total_missing_translations']++;
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
     * Show tree for translation page with some params
     *
     * @param Request $request
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

            if (in_array($type, array('modules', 'themes')) && empty($selected)) {
                throw new Exception('This \'selected\' param is not valid.');
            }

            if ('modules' === $type) {
                $tree = $this->getModulesTree($lang, $type, $selected, $search);
            } else {
                $tree = $this->getNormalTree($lang, $type, $selected, $search);
            }

            return $this->jsonResponse($tree, $request);

        } catch (Exception $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }
    }

    /**
     * Route to edit translation
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function translationEditAction(Request $request)
    {
        try {
            $decodedContent = $this->guardAgainstInvalidTranslationBulkRequest($request);

            $translations = $decodedContent['translations'];
            $this->guardAgainstInvalidTranslationEditRequest($translations);

            $translationService = $this->container->get('prestashop.service.translation');
            $response = array();

            foreach ($translations as $translation) {
                if (!array_key_exists('theme', $translation)) {
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
     * Route to reset translation
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function translationResetAction(Request $request)
    {
        try {
            $decodedContent = $this->guardAgainstInvalidTranslationBulkRequest($request);

            $translations = $decodedContent['translations'];
            $this->guardAgainstInvalidTranslationResetRequest($translations);

            $translationService = $this->container->get('prestashop.service.translation');
            $response = array();

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
     * @return mixed
     */
    private function guardAgainstInvalidTranslationBulkRequest(Request $request)
    {
        $content = $request->getContent();

        $decodedContent = $this->guardAgainstInvalidJsonBody($content);

        if (empty($decodedContent) || !array_key_exists('translations', $decodedContent) || !is_array($decodedContent['translations'])) {
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
            'a "locale", a "domain", a "default" and a "edited" values. '.
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
    function guardAgainstInvalidTranslationResetRequest($content)
    {
        $message = 'Each item of JSON-encoded array in the request body should contain ' .
            'a "locale", a "domain" and a "default" values. '.
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
     * @param $lang
     * @param $type
     * @param $selected
     * @param null $search
     *
     * @return array
     */
    private function getNormalTree($lang, $type, $selected, $search = null)
    {
        $treeBuilder = new TreeBuilder($this->translationService->langToLocale($lang), $selected);
        $catalogue = $this->translationService->getTranslationsCatalogue($lang, $type, $selected, $search);

        return $this->getCleanTree($treeBuilder, $catalogue, $type, $selected, $search);
    }

    /**
     * @param $lang
     * @param $type
     * @param $selected
     * @param null $search
     *
     * @return array
     */
    private function getModulesTree($lang, $type, $selected, $search = null)
    {
        $moduleProvider = $this->container->get('prestashop.translation.module_provider');
        $moduleProvider->setModuleName($selected);

        $treeBuilder = new TreeBuilder($this->translationService->langToLocale($lang), $selected);
        $catalogue = $treeBuilder->makeTranslationArray($moduleProvider, $search);

        return $this->getCleanTree($treeBuilder, $catalogue, $type, null, $search);
    }

    /**
     * Make final tree
     *
     * @param TreeBuilder $treeBuilder
     * @param $catalogue
     * @param $type
     * @param $selected
     * @param null $search
     *
     * @return array
     */
    private function getCleanTree(TreeBuilder $treeBuilder, $catalogue, $type, $selected, $search = null)
    {
        $selected = ('mails' === $type && 'subject' === $selected ? false : $selected);

        $translationsTree = $treeBuilder->makeTranslationsTree($catalogue);
        $translationsTree = $treeBuilder->cleanTreeToApi($translationsTree, $this->container->get('router'), $selected, $search);

        return $translationsTree;
    }
}
