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

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Security\Admin\Employee;
use Symfony\Component\Security\Core\Security;

/**
 * Class ProfileGridDataFactory decorates data from profile doctrine data factory.
 */
final class ProfileGridDataFactoryDecorator implements GridDataFactoryInterface
{
    /**
     * @var GridDataFactoryInterface
     */
    private $profileGridDataFactory;

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        GridDataFactoryInterface $profileGridDataFactory,
        Security $security
    ) {
        $this->profileGridDataFactory = $profileGridDataFactory;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $profileData = $this->profileGridDataFactory->getData($searchCriteria);

        $profileRecords = $this->applyModifications($profileData->getRecords());

        return new GridData(
            $profileRecords,
            $profileData->getRecordsTotal(),
            $profileData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $profiles
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $profiles)
    {
        $modifiedProfiles = [];

        /** @var Employee|null $user */
        $user = $this->security->getUser();
        if (null === $user) {
            return new RecordCollection($modifiedProfiles);
        }

        $currentUserIdProfile = $user->getData()->id_profile;
        foreach ($profiles as $profile) {
            if ($profile['id_profile'] === $currentUserIdProfile) {
                $profile['disableBulkCheckbox'] = true;
            }

            $modifiedProfiles[] = $profile;
        }

        return new RecordCollection($modifiedProfiles);
    }
}
