<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\ProductAttachmentCollectionTransfer;
use Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentPersistenceFactory getFactory()
 */
class ProductAttachmentRepository extends AbstractRepository implements ProductAttachmentRepositoryInterface
{
    public function getProductAttachmentCollection(
        ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer,
    ): ProductAttachmentCollectionTransfer {
        $productAttachmentQuery = $this->getFactory()->createProductAttachmentQuery();
        $productAttachmentQuery = $this->applyProductAttachmentFilters(
            $productAttachmentQuery,
            $productAttachmentCriteriaTransfer,
        );

        $productAttachmentQuery = $this
            ->applySorting($productAttachmentQuery, $productAttachmentCriteriaTransfer->getSortCollection());

        $productAttachmentEntities = $productAttachmentQuery->find();

        $productAttachmentCollectionTransfer = new ProductAttachmentCollectionTransfer();
        $productAttachmentTransfers = $this->getFactory()
            ->createProductAttachmentMapper()
            ->mapProductAttachmentEntitiesToProductAttachmentTransfers(
                $productAttachmentEntities->getData(),
                new ArrayObject(),
                $productAttachmentCriteriaTransfer,
            );

        $productAttachmentCollectionTransfer->setProductAttachments($productAttachmentTransfers);

        return $productAttachmentCollectionTransfer;
    }

    protected function applyProductAttachmentFilters(
        SpyProductAttachmentQuery $productAttachmentQuery,
        ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer,
    ): SpyProductAttachmentQuery {
        $limit = $productAttachmentCriteriaTransfer->getFilter()?->getLimit();
        $offset = $productAttachmentCriteriaTransfer->getFilter()?->getOffset();

        if ($limit !== null) {
            $productAttachmentQuery->limit($limit);
        }

        if ($offset !== null) {
            $productAttachmentQuery->offset($offset);
        }

        $productAttachmentConditions = $productAttachmentCriteriaTransfer->getProductAttachmentCondition();

        if ($productAttachmentConditions === null) {
            return $productAttachmentQuery;
        }

        if ($productAttachmentConditions->getProductAttachmentIds() !== []) {
            $productAttachmentQuery->filterByIdProductAttachment_In(
                $productAttachmentConditions->getProductAttachmentIds(),
            );
        }

        if ($productAttachmentConditions->getLocaleIds() !== []) {
            $productAttachmentQuery->filterByFkLocale_In($productAttachmentConditions->getLocaleIds());
        }

        if ($productAttachmentConditions->getProductAbstractIds() !== []) {
            $productAttachmentQuery
                ->useProductAttachmentProductAbstractQuery()
                    ->filterByFkProductAbstract_In($productAttachmentConditions->getProductAbstractIds())
                ->endUse();
        }

        if ($productAttachmentCriteriaTransfer->getWithProductAbstractRelation()) {
            $productAttachmentQuery->joinWithProductAttachmentProductAbstract();
        }

        return $productAttachmentQuery;
    }

    /**
     * @return array<int>
     */
    public function getAttachmentIdsByProductAbstractId(int $idProductAbstract): array
    {
        /** @var \Propel\Runtime\Collection\ArrayCollection $collection */
        $collection = $this->getFactory()
            ->createProductAttachmentProductAbstractQuery()
            ->filterByFkProductAbstract($idProductAbstract)
            ->select(['FkProductAttachment'])
            ->find();

        return $collection->toArray();
    }

    /**
     * @param array<int> $attachmentIds
     *
     * @return array<int>
     */
    public function getOrphanedAttachmentIds(array $attachmentIds): array
    {
        if (!$attachmentIds) {
            return [];
        }

        /** @var \Propel\Runtime\Collection\ObjectCollection $attachmentIdsWithRelations */
        $attachmentIdsWithRelations = $this->getFactory()
            ->createProductAttachmentProductAbstractQuery()
            ->filterByFkProductAttachment_In($attachmentIds)
            ->select(['FkProductAttachment'])
            ->distinct()
            ->find();

        return array_diff($attachmentIds, $attachmentIdsWithRelations->toArray());
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\SortTransfer> $sortTransfers
     */
    protected function applySorting(
        SpyProductAttachmentQuery $modelCriteria,
        ArrayObject $sortTransfers,
    ): SpyProductAttachmentQuery {
        foreach ($sortTransfers as $sortTransfer) {
            $modelCriteria->orderBy(
                $sortTransfer->getFieldOrFail(),
                $sortTransfer->getIsAscending() ? Criteria::ASC : Criteria::DESC,
            );
        }

        return $modelCriteria;
    }
}
