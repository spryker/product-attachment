<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Communication\Plugin\ProductManagement;

use ArrayObject;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductAttachment\Communication\Form\Product\AttachmentForm;
use Spryker\Zed\ProductManagementExtension\Dependency\Plugin\ProductAbstractTransferMapperPluginInterface;

/**
 * @method \Spryker\Zed\ProductAttachment\Business\ProductAttachmentFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductAttachment\ProductAttachmentConfig getConfig()
 * @method \Spryker\Zed\ProductAttachment\Communication\ProductAttachmentCommunicationFactory getFactory()
 */
class ProductAttachmentProductAbstractTransferMapperPlugin extends AbstractPlugin implements ProductAbstractTransferMapperPluginInterface
{
    /**
     * {@inheritDoc}
     * - Maps form data to ProductAbstractTransfer.
     * - Builds product attachment collection from form data.
     * - Sets the attachment collection on ProductAbstractTransfer.
     *
     * @api
     *
     * @param array<string, mixed> $data
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractTransfer
     */
    public function map(array $data, ProductAbstractTransfer $productAbstractTransfer): ProductAbstractTransfer
    {
        $attachmentCollection = $this->buildProductAttachmentCollection($data);

        $productAbstractTransfer->setProductAttachments(new ArrayObject($attachmentCollection));

        return $productAbstractTransfer;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<\Generated\Shared\Transfer\ProductAttachmentTransfer>
     */
    protected function buildProductAttachmentCollection(array $data): array
    {
        $attachments = [];
        $localeCollection = $this->getFactory()->getLocaleFacade()->getLocaleCollection();
        $localeCollection = array_merge([
            (new LocaleTransfer())->setLocaleName('default'),
        ], $localeCollection);

        foreach ($localeCollection as $localeTransfer) {
            $formName = $this->getConfig()->getAttachmentFormName() . '_' . $localeTransfer->getLocaleName();

            if (!isset($data[$formName])) {
                continue;
            }

            $attachmentCollection = $data[$formName];

            foreach ($attachmentCollection as $attachmentData) {
                if (
                    empty($attachmentData[AttachmentForm::FIELD_LABEL]) ||
                    empty($attachmentData[AttachmentForm::FIELD_URL])
                ) {
                    continue;
                }

                $attachmentTransfer = (new ProductAttachmentTransfer())
                    ->setIdProductAttachment($attachmentData[AttachmentForm::FIELD_ID_PRODUCT_ATTACHMENT] ?? null)
                    ->setFkLocale($localeTransfer->getIdLocale())
                    ->setLabel($attachmentData[AttachmentForm::FIELD_LABEL])
                    ->setUrl($attachmentData[AttachmentForm::FIELD_URL])
                    ->setSortOrder($attachmentData[AttachmentForm::FIELD_SORT_ORDER] ?? 0);

                $attachments[] = $attachmentTransfer;
            }
        }

        return $attachments;
    }
}
