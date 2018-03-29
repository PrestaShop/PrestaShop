<?php

namespace PrestaShopBundle\Service\Tab;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;

class TabStatus
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Changes tab status
     *
     * @param string $className tab's class name
     * @param bool $status wanted status for the tab
     *
     * @throws \InvalidArgumentException
     */
    public function changeStatusByClassName($className, $status)
    {
        if (!is_bool($status)) {
            throw new \InvalidArgumentException(sprintf('Invalid type: bool expected, got %s', gettype($status)));
        }

        /** @var TabRepository $tabRepository */
        $tabRepository = $this->entityManager->getRepository(Tab::class);

        /** @var Tab $tab */
        $tab = $tabRepository->findOneByClassName($className);

        if (null !== $tab) {
            $tab->setActive($status);
            $this->entityManager->persist($tab);
            $this->entityManager->flush();
        }
    }
}
