<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Business\Writer;

use ArrayObject;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentConditionTransfer;
use Generated\Shared\Transfer\ProductAttachmentProductAbstractTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\ProductAttachment\Business\Attachment\AttachmentCreatorInterface;
use Spryker\Zed\ProductAttachment\Business\Attachment\AttachmentUpdaterInterface;
use Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentEntityManagerInterface;
use Spryker\Zed\ProductAttachment\Persistence\ProductAttachmentRepositoryInterface;

class ProductAbstractAttachmentCollectionWriter implements ProductAbstractAttachmentCollectionWriterInterface
{
    use TransactionTrait;

    public function __construct(
        protected ProductAttachmentRepositoryInterface $repository,
        protected ProductAttachmentEntityManagerInterface $entityManager,
        protected AttachmentCreatorInterface $attachmentCreator,
        protected AttachmentUpdaterInterface $attachmentUpdater,
    ) {
    }

    public function saveProductAbstractAttachments(ProductAbstractTransfer $productAbstractTransfer): void
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($productAbstractTransfer) {
            $this->executeSaveProductAbstractAttachmentsTransaction($productAbstractTransfer);
        });
    }

    protected function executeSaveProductAbstractAttachmentsTransaction(ProductAbstractTransfer $productAbstractTransfer): void
    {
        $idProductAbstract = $productAbstractTransfer->getIdProductAbstractOrFail();
        $incomingProductAttachmentTransfers = $productAbstractTransfer->getProductAttachments();

        if ($incomingProductAttachmentTransfers->count() === 0) {
            $this->removeAllProductAbstractRelations($idProductAbstract);

            return;
        }

        $existingAttachmentIds = $this->getExistingAttachmentIds($idProductAbstract);
        $incomingAttachmentIds = $this->extractAttachmentIds($incomingProductAttachmentTransfers);

        $this->removeUnusedRelations($idProductAbstract, $existingAttachmentIds, $incomingAttachmentIds);

        foreach ($incomingProductAttachmentTransfers as $productAttachmentTransfer) {
            $sortOrder = $productAttachmentTransfer->getSortOrder() ?? 0;

            if ($productAttachmentTransfer->getIdProductAttachment()) {
                $this->attachmentUpdater->updateAttachment($productAttachmentTransfer);

                $this->saveRelation($productAttachmentTransfer->getIdProductAttachmentOrFail(), $idProductAbstract, $sortOrder);

                continue;
            }

            $createdAttachment = $this->attachmentCreator->createAttachment($productAttachmentTransfer);

            $this->saveRelation($createdAttachment->getIdProductAttachmentOrFail(), $idProductAbstract, $sortOrder);
        }
    }

    /**
     * @return array<int>
     */
    protected function getExistingAttachmentIds(int $idProductAbstract): array
    {
        return $this->repository->getAttachmentIdsByProductAbstractId($idProductAbstract);
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ProductAttachmentTransfer> $attachments
     *
     * @return array<int>
     */
    protected function extractAttachmentIds(ArrayObject $attachments): array
    {
        $attachmentIds = [];

        foreach ($attachments as $attachment) {
            if ($attachment->getIdProductAttachment()) {
                $attachmentIds[] = $attachment->getIdProductAttachment();
            }
        }

        return $attachmentIds;
    }

    /**
     * @param array<int> $existingAttachmentIds
     * @param array<int> $incomingAttachmentIds
     */
    protected function removeUnusedRelations(
        int $idProductAbstract,
        array $existingAttachmentIds,
        array $incomingAttachmentIds
    ): void {
        $attachmentIdsToRemove = array_diff($existingAttachmentIds, $incomingAttachmentIds);

        if (!$attachmentIdsToRemove) {
            return;
        }

        $this->entityManager->deleteProductAbstractRelation(
            (new ProductAttachmentConditionTransfer())
                ->addIdProductAbstract($idProductAbstract)
                ->setProductAttachmentIds($attachmentIdsToRemove),
        );

        $orphanedAttachmentIds = $this->repository->getOrphanedAttachmentIds($attachmentIdsToRemove);

        if (count($orphanedAttachmentIds) > 0) {
            $this->entityManager->deleteProductAttachments($orphanedAttachmentIds);
        }
    }

    protected function removeAllProductAbstractRelations(int $idProductAbstract): void
    {
        $existingAttachmentIds = $this->getExistingAttachmentIds($idProductAbstract);

        $this->entityManager->deleteProductAbstractRelation(
            (new ProductAttachmentConditionTransfer())
                ->addIdProductAbstract($idProductAbstract),
        );

        $orphanedAttachmentIds = $this->repository->getOrphanedAttachmentIds($existingAttachmentIds);

        if (count($orphanedAttachmentIds) > 0) {
            $this->entityManager->deleteProductAttachments($orphanedAttachmentIds);
        }
    }

    protected function saveRelation(int $idProductAttachment, int $idProductAbstract, int $order): void
    {
        $productAttachmentProductAbstractTransfer = $this->createProductAttachmentProductAbstractTransfer(
            $idProductAttachment,
            $idProductAbstract,
            $order,
        );

        $this->entityManager->saveProductAbstractRelation($productAttachmentProductAbstractTransfer);
    }

    protected function createProductAttachmentProductAbstractTransfer(
        int $idProductAttachment,
        int $idProductAbstract,
        int $order,
    ): ProductAttachmentProductAbstractTransfer {
        return (new ProductAttachmentProductAbstractTransfer())
            ->setFkProductAttachment($idProductAttachment)
            ->setFkProductAbstract($idProductAbstract)
            ->setOrder($order);
    }
}
