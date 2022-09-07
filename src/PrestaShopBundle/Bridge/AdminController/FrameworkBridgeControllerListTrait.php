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

namespace PrestaShopBundle\Bridge\AdminController;

use PrestaShopBundle\Bridge\Helper\Listing\HelperListConfiguration;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Validate;

/**
 * Contains the principal methods you need to horizontally migrate a controller which has a list.
 */
trait FrameworkBridgeControllerListTrait
{
    /**
     * Updates object position when request is coming from legacy list javascript dnd.js.
     * Check ajaxProcessUpdatePositions for legacy behavior in some AdminControllers like AdminFeaturesController
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    protected function updatePositionBridge(Request $request): JsonResponse
    {
        $identifierKey = $request->query->get('identifierKey');
        // fallback to "id" because that is the default behavior and not all identifierKeys are mapped in dnd.js
        $objectId = $request->request->getInt($identifierKey, $request->request->getInt('id'));
        $className = $request->query->get('className');

        if (!$objectId || !$className) {
            return $this->json([
                'errorMessage' => 'Object id or className is missing in request for position update.',
                Response::HTTP_BAD_REQUEST,
            ]);
        }

        $position = $this->extractLegacyAjaxPosition($request, $className, $objectId);

        if ($position === null) {
            return $this->json([
                'errorMessage' => 'position not found for object',
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }

        if (!method_exists($className, 'updatePosition')) {
            return $this->json([
                'errorMessage' => sprintf('method "updatePosition" not found in class %s', $className),
                Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }

        $objectModel = new $className($objectId);

        if (!Validate::isLoadedObject($objectModel)) {
            return $this->json([
                'errorMessage' => 'Failed to load object model',
                Response::HTTP_BAD_REQUEST,
            ]);
        }

        if (!$objectModel->updatePosition($request->request->getInt('way'), $position)) {
            return $this->json([
                'errorMessage' => 'Failed to update position',
                Response::HTTP_BAD_REQUEST,
            ]);
        }

        return $this->json('ok', Response::HTTP_OK);
    }

    /**
     * @param string $identifierKey @see HelperListConfiguration::$identifierKey
     * @param string $defaultOrderBy @see HelperListConfiguration::$defaultOrderBy
     * @param string $indexRoute route name used to generate url for filters & sorting submissions. @see HelperListConfiguration::$indexUrl
     * @param string|null $updatePositionRoute used to generate url for position update
     * @param string|null $positionIdentifierKey @see HelperListConfiguration::$positionIdentifierKey
     * @param bool $autoJoinLangTable @see HelperListConfiguration::$autoJoinLanguageTable
     * @param bool $deleted @see HelperListConfiguration::$deleted
     * @param bool $explicitSelect @see HelperListConfiguration::$explicitSelect
     * @param bool $useFoundRows @see HelperListConfiguration::$useFoundRows
     * @param string|null $listId @see HelperListConfiguration::$listId
     *
     * @return HelperListConfiguration
     */
    protected function buildListConfiguration(
        string $identifierKey,
        string $defaultOrderBy,
        string $indexRoute,
        ?string $updatePositionRoute = null,
        ?string $positionIdentifierKey = null,
        bool $autoJoinLangTable = true,
        bool $deleted = false,
        bool $explicitSelect = false,
        bool $useFoundRows = true,
        ?string $listId = null
    ): HelperListConfiguration {
        $controllerConfiguration = $this->getControllerConfiguration();

        return $this->get('prestashop.bridge.helper.listing.helper_list_configuration_factory')->create(
            $controllerConfiguration,
            $identifierKey,
            $indexRoute,
            $positionIdentifierKey,
            $defaultOrderBy,
            $autoJoinLangTable,
            $deleted,
            $explicitSelect,
            $useFoundRows,
            $listId,
            $updatePositionRoute
        );
    }

    /**
     * Handles filters submit and reset
     *
     * @param Request $request
     * @param HelperListConfiguration $helperListConfiguration
     */
    protected function processFilters(Request $request, HelperListConfiguration $helperListConfiguration): void
    {
        $this->get('prestashop.bridge.helper.listing.filters_processor')
            ->processFilters($request, $helperListConfiguration)
        ;
    }

    /**
     * Extracts new position provided from legacy dnd.js javascript side
     *
     * @param Request $request
     * @param string $className
     *
     * @return int|null
     */
    private function extractLegacyAjaxPosition(Request $request, string $className, int $objectId): ?int
    {
        $positions = $request->request->get(strtolower($className));

        foreach ($positions as $position => $data) {
            if (!empty($data)) {
                // explodes value formatted in legacy. The 2 item of array should be the updatable object id
                $tmpData = explode('_', $data);
                if (!is_array($tmpData) || !isset($tmpData[2]) || (int) $tmpData[2] !== $objectId) {
                    continue;
                }

                return $position;
            }
        }

        return null;
    }
}
