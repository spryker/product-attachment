<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\Attachment;

use Generated\Shared\Transfer\ProductAttachmentTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentEntityManagerInterface;

class AttachmentUpdater implements AttachmentUpdaterInterface
{
    use TransactionTrait;

    public function __construct(
        protected ProductAttachmentEntityManagerInterface $entityManager,
    ) {
    }

    public function updateAttachment(ProductAttachmentTransfer $productAttachmentTransfer): void
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($productAttachmentTransfer) {
            $this->executeUpdateAttachmentTransaction($productAttachmentTransfer);
        });
    }

    protected function executeUpdateAttachmentTransaction(ProductAttachmentTransfer $productAttachmentTransfer): void
    {
        $this->entityManager->updateProductAttachment($productAttachmentTransfer);
    }
}
