<?php

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class CustomerGroupsQueryBuilder extends AbstractDoctrineQueryBuilder
{

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $builder = $this->getCustomerGroupsQueryBuilder();

        $builder
            ->select('g.*')
        ;

        return $builder;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $builder = $this->getCustomerGroupsQueryBuilder();

        $builder
            ->select('COUNT(g.id_group)')
        ;

        return $builder;
    }

    private function getCustomerGroupsQueryBuilder()
    {

        $builder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'group', 'g')
            //->innerJoin('g', $this->dbPrefix . 'gender_lang', 'gl', 'g.id_gender = gl.id_gender')
            //->andWhere('gl.`id_lang`= :language')
            //->setParameter('language', $this->languageId)
        ;

        return $builder;
    }

}
