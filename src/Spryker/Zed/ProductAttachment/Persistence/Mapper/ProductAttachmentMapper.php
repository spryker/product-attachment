<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Persistence\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer;
use Generated\Shared\Transfer\ProductAttachmentProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachment;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentProductAbstract;

class ProductAttachmentMapper
{
    /**
     * @param array<\Orm\Zed\ProductAttachment\Persistence\SpyProductAttachment> $productAttachmentEntities
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ProductAttachmentTransfer> $productAttachmentTransfers
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\ProductAttachmentTransfer>
     */
    public function mapProductAttachmentEntitiesToProductAttachmentTransfers(
        array $productAttachmentEntities,
        ArrayObject $productAttachmentTransfers,
        ?ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer = null,
    ): ArrayObject {
        foreach ($productAttachmentEntities as $productAttachmentEntity) {
            $productAttachmentTransfer = $this->mapProductAttachmentEntityToProductAttachmentTransfer(
                $productAttachmentEntity,
                new ProductAttachmentTransfer(),
                $productAttachmentCriteriaTransfer,
            );

            $productAttachmentTransfers->append($productAttachmentTransfer);
        }

        return $productAttachmentTransfers;
    }

    public function mapProductAttachmentEntityToProductAttachmentTransfer(
        SpyProductAttachment $productAttachmentEntity,
        ProductAttachmentTransfer $productAttachmentTransfer,
        ?ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer = null,
    ): ProductAttachmentTransfer {
        $productAttachmentTransfer->fromArray(
            $productAttachmentEntity->toArray(),
            true,
        );

        if ($productAttachmentCriteriaTransfer?->getWithProductAbstractRelation()) {
            $productAttachmentTransfer = $this->mapProductAbstractRelationsToProductAttachmentTransfer(
                $productAttachmentEntity,
                $productAttachmentTransfer,
            );
        }

        return $productAttachmentTransfer;
    }

    public function mapProductAttachmentTransferToProductAttachmentEntity(
        ProductAttachmentTransfer $productAttachmentTransfer,
        SpyProductAttachment $productAttachmentEntity,
    ): SpyProductAttachment {
        $productAttachmentEntity->fromArray(
            $productAttachmentTransfer->modifiedToArray(),
        );

        return $productAttachmentEntity;
    }

    protected function mapProductAbstractRelationsToProductAttachmentTransfer(
        SpyProductAttachment $productAttachmentEntity,
        ProductAttachmentTransfer $productAttachmentTransfer,
    ): ProductAttachmentTransfer {
        $productAttachmentTransfer->setSortOrder(
            $productAttachmentEntity->getProductAttachmentProductAbstracts()->getFirst()?->getOrder(),
        );

        return $productAttachmentTransfer;
    }

    public function mapProductAttachmentProductAbstractTransferToEntity(
        ProductAttachmentProductAbstractTransfer $productAttachmentProductAbstractTransfer,
        SpyProductAttachmentProductAbstract $productAbstractRelationEntity,
    ): SpyProductAttachmentProductAbstract {
        $productAbstractRelationEntity->fromArray(
            $productAttachmentProductAbstractTransfer->modifiedToArray(),
        );

        return $productAbstractRelationEntity;
    }
}
