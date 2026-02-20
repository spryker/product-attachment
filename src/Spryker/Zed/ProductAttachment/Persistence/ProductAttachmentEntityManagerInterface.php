<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Persistence;

use Generated\Shared\Transfer\ProductAttachmentConditionTransfer;
use Generated\Shared\Transfer\ProductAttachmentProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;

interface ProductAttachmentEntityManagerInterface
{
    public function createProductAttachment(ProductAttachmentTransfer $productAttachmentTransfer): ProductAttachmentTransfer;

    public function updateProductAttachment(ProductAttachmentTransfer $productAttachmentTransfer): void;

    public function saveProductAbstractRelation(
        ProductAttachmentProductAbstractTransfer $productAttachmentProductAbstractTransfer,
    ): void;

    public function deleteProductAbstractRelation(ProductAttachmentConditionTransfer $productAttachmentConditionTransfer): void;

    /**
     * @param array<int> $attachmentIds
     */
    public function deleteProductAttachments(array $attachmentIds): void;
}
