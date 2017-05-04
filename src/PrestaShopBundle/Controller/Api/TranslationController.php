<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Api;

use Exception;
use PrestaShopBundle\Service\TranslationService;
use PrestaShopBundle\Translation\View\TreeBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TranslationController extends ApiController
{
    /**
     * @var TranslationService
     */
    public $translationService;

    /**
     * Show translations for 1 domain & 1 locale given
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listDomainTranslationAction(Request $request)
    {
        try {
            $translationProvider = $this->container->get('prestashop.translation.search_provider');

            $translationProvider->setLocale($request->attributes->get('locale'));
            $translationProvider->setDomain($request->attributes->get('domain'));

            $info = array(
                'locale' => $translationProvider->getLocale(),
                'domain' => $translationProvider->getDomain(),
                'missing' => 0,
                'total' => 0,
            );

            $catalog = current($translationProvider->getMessageCatalogue()->all());
            foreach ($catalog as $original => $translated) {
                if ($original === $translated) {
                    $info['missing']++;
                }

                $info['total']++;
            }

            return new JsonResponse($catalog, 200, $info);

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

            if (in_array($type, array('modules', 'themes')) && empty($selected)) {
                throw new Exception('This \'selected\' param is not valid.');
            }

            if ('modules' === $type) {
                $tree = $this->getModulesTree($lang, $type, $selected);
            } else {
                $tree = $this->getNormalTree($lang, $type, $selected);
            }

            return new JsonResponse($tree, 200);

        } catch (Exception $exception) {
            return $this->handleException(new BadRequestHttpException($exception->getMessage(), $exception));
        }
    }

    /**
     * @param $lang
     * @param $type
     * @param $selected
     *
     * @return array
     */
    private function getNormalTree($lang, $type, $selected)
    {
        $treeBuilder = new TreeBuilder($this->translationService->langToLocale($lang), $selected);

        $catalogue = $this->translationService->getTranslationsCatalogue($lang, $type, $selected);
        $translationsTree = $treeBuilder->makeTranslationsTree($catalogue);
        $translationsTree = $treeBuilder->cleanTreeToApi($translationsTree, $this->container->get('router'));

        return $translationsTree;
    }

    /**
     * @param $lang
     * @param $type
     * @param $selected
     *
     * @return array
     */
    private function getModulesTree($lang, $type, $selected)
    {
        $moduleProvider = $this->container->get('prestashop.translation.module_provider');
        $moduleProvider->setModuleName($selected);

        $treeBuilder = new TreeBuilder($this->translationService->langToLocale($lang), $selected);

        $catalogue = $treeBuilder->makeTranslationArray($moduleProvider);
        $translationsTree = $treeBuilder->makeTranslationsTree($catalogue);
        $translationsTree = $treeBuilder->cleanTreeToApi($translationsTree, $this->container->get('router'));

        return $translationsTree;
    }
}
