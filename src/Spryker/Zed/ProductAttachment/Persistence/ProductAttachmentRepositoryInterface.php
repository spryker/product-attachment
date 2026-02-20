<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Persistence;

use Generated\Shared\Transfer\ProductAttachmentCollectionTransfer;
use Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer;

interface ProductAttachmentRepositoryInterface
{
    public function getProductAttachmentCollection(
        ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer,
    ): ProductAttachmentCollectionTransfer;

    /**
     * @return array<int>
     */
    public function getAttachmentIdsByProductAbstractId(int $idProductAbstract): array;

    /**
     * @param array<int> $attachmentIds
     *
     * @return array<int>
     */
    public function getOrphanedAttachmentIds(array $attachmentIds): array;
}
