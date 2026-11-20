<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business;

use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentCollectionTransfer;
use Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer;

interface ProductAttachmentFacadeInterface
{
    /**
     * Specification:
     * - Retrieves a collection of product attachments based on the provided criteria.
     * - Executes the stack of {@link \Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentCollectionExpanderPluginInterface} plugins.
     * - Returns a ProductAttachmentCollectionTransfer containing the matched attachments.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAttachmentCollectionTransfer
     */
    public function getProductAttachmentCollection(
        ProductAttachmentCriteriaTransfer $productAttachmentCriteriaTransfer,
    ): ProductAttachmentCollectionTransfer;

    /**
     * Specification:
     * - Saves the attachment collection for a product abstract.
     * - Persists the attachment relationships to the database.
     * - Executes the stack of {@link \Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentCollectionPostSavePluginInterface} plugins once in the same transaction.
     * - Passes all saved attachments with `ProductAttachmentTransfer.idProductAttachment` set to the plugins; passes an empty collection when the attachment list is empty.
     * - Each plugin receives the collection the previous plugin returned.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return void
     */
    public function saveProductAbstractAttachmentCollection(ProductAbstractTransfer $productAbstractTransfer): void;
}
