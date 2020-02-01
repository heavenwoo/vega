<?php

namespace Vega\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait CreatePaginator
{

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param int                        $currentPage
     * @param int                        $pageSize
     *
     * @return array
     */
    private function createPaginator(
        QueryBuilder $queryBuilder,
        int $currentPage,
        int $pageSize = 20
    ) : array {
        $currentPage = $currentPage < 1 ? 1 : $currentPage;
        $firstResult = ($currentPage - 1) * $pageSize;

        $query = $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($pageSize)
            ->getQuery();

        $paginator = new Paginator($query);
        $numResults = $paginator->count();
        $hasPreviousPage = $currentPage > 1;
        $hasNextPage = ($currentPage * $pageSize) < $numResults;

        return [
            'results'         => $paginator->getIterator(),
            'currentPage'     => $currentPage,
            'hasPreviousPage' => $hasPreviousPage,
            'hasNextPage'     => $hasNextPage,
            'previousPage'    => $hasPreviousPage ? $currentPage - 1 : null,
            'nextPage'        => $hasNextPage ? $currentPage + 1 : null,
            'numPages'        => (int)ceil($numResults / $pageSize),
            'haveToPaginate'  => $numResults > $pageSize,
        ];
    }
}