<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Entity\Employee\Employee;
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

        $currentUserIdProfile = $user->getProfile()->getId();
        foreach ($profiles as $profile) {
            if ($profile['id_profile'] === $currentUserIdProfile) {
                $profile['disableBulkCheckbox'] = true;
            }

            $modifiedProfiles[] = $profile;
        }

        return new RecordCollection($modifiedProfiles);
    }
}
