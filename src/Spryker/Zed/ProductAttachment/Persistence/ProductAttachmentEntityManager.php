<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Persistence;

use Generated\Shared\Transfer\ProductAttachmentConditionTransfer;
use Generated\Shared\Transfer\ProductAttachmentProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachment;
use RuntimeException;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentPersistenceFactory getFactory()
 */
class ProductAttachmentEntityManager extends AbstractEntityManager implements ProductAttachmentEntityManagerInterface
{
    protected const int BATCH_SIZE = 500;

    public function createProductAttachment(ProductAttachmentTransfer $productAttachmentTransfer): ProductAttachmentTransfer
    {
        $productAttachmentMapper = $this->getFactory()->createProductAttachmentMapper();
        $productAttachmentEntity = $productAttachmentMapper->mapProductAttachmentTransferToProductAttachmentEntity(
            $productAttachmentTransfer,
            new SpyProductAttachment(),
        );

        $productAttachmentEntity->save();

        return $productAttachmentMapper->mapProductAttachmentEntityToProductAttachmentTransfer(
            $productAttachmentEntity,
            $productAttachmentTransfer,
        );
    }

    public function updateProductAttachment(ProductAttachmentTransfer $productAttachmentTransfer): void
    {
        $productAttachmentEntity = $this->getFactory()
            ->createProductAttachmentQuery()
            ->filterByIdProductAttachment($productAttachmentTransfer->getIdProductAttachment())
            ->findOne();

        if ($productAttachmentEntity === null) {
            throw new RuntimeException(sprintf('Product attachment entity with id "%s" not found.', $productAttachmentTransfer->getIdProductAttachment()));
        }

        $productAttachmentMapper = $this->getFactory()->createProductAttachmentMapper();
        $productAttachmentEntity = $productAttachmentMapper->mapProductAttachmentTransferToProductAttachmentEntity(
            $productAttachmentTransfer,
            $productAttachmentEntity,
        );

        $productAttachmentEntity->save();
    }

    public function saveProductAbstractRelation(
        ProductAttachmentProductAbstractTransfer $productAttachmentProductAbstractTransfer,
    ): void {
        $productAbstractRelationEntity = $this->getFactory()
            ->createProductAttachmentProductAbstractQuery()
            ->filterByFkProductAttachment($productAttachmentProductAbstractTransfer->getFkProductAttachment())
            ->filterByFkProductAbstract($productAttachmentProductAbstractTransfer->getFkProductAbstract())
            ->findOneOrCreate();

        $productAttachmentMapper = $this->getFactory()->createProductAttachmentMapper();
        $productAbstractRelationEntity = $productAttachmentMapper->mapProductAttachmentProductAbstractTransferToEntity(
            $productAttachmentProductAbstractTransfer,
            $productAbstractRelationEntity,
        );

        $productAbstractRelationEntity->save();
    }

    public function deleteProductAbstractRelation(ProductAttachmentConditionTransfer $productAttachmentConditionTransfer): void
    {
        $productAttachmentProductAbstractQuery = $this->getFactory()->createProductAttachmentProductAbstractQuery();

        if (count($productAttachmentConditionTransfer->getProductAbstractIds()) > 0) {
            $productAttachmentProductAbstractQuery
                ->filterByFkProductAbstract_In($productAttachmentConditionTransfer->getProductAbstractIds());
        }

        if (count($productAttachmentConditionTransfer->getProductAttachmentIds()) > 0) {
            $productAttachmentProductAbstractQuery
                ->filterByFkProductAttachment_In($productAttachmentConditionTransfer->getProductAttachmentIds());
        }

        if (!$productAttachmentProductAbstractQuery->hasWhereClause()) {
            return;
        }

        do {
            $productAbstractRelationEntities = (clone $productAttachmentProductAbstractQuery)
                ->limit(static::BATCH_SIZE)
                ->find();

            if ($productAbstractRelationEntities->count() === 0) {
                break;
            }

            foreach ($productAbstractRelationEntities as $productAbstractRelationEntity) {
                $productAbstractRelationEntity->delete();
            }
        } while (true);
    }

    /**
     * @param array<int> $attachmentIds
     */
    public function deleteProductAttachments(array $attachmentIds): void
    {
        if (!$attachmentIds) {
            return;
        }

        $productAttachmentQuery = $this->getFactory()
            ->createProductAttachmentQuery()
            ->filterByIdProductAttachment_In($attachmentIds);

        foreach ($productAttachmentQuery->find() as $productAttachmentEntity) {
            $productAttachmentEntity->delete();
        }
    }
}
