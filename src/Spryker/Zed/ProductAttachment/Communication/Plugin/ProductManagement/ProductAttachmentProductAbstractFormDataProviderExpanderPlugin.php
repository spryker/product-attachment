<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Communication\Plugin\ProductManagement;

use ArrayObject;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentConditionTransfer;
use Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer;
use Generated\Shared\Transfer\SortTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductAttachment\Communication\Form\Product\AttachmentForm;
use Spryker\Zed\ProductManagementExtension\Dependency\Plugin\ProductAbstractFormDataProviderExpanderPluginInterface;

/**
 * @method \Spryker\Zed\ProductAttachment\Business\ProductAttachmentFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductAttachment\ProductAttachmentConfig getConfig()
 * @method \Spryker\Zed\ProductAttachment\Communication\ProductAttachmentCommunicationFactory getFactory()
 */
class ProductAttachmentProductAbstractFormDataProviderExpanderPlugin extends AbstractPlugin implements ProductAbstractFormDataProviderExpanderPluginInterface
{
    /**
     * @uses \Orm\Zed\ProductAttachment\Persistence\Map\SpyProductAttachmentProductAbstractTableMap::COL_ORDER
     */
    protected const string COL_PRODUCT_ATTACHMENT_PRODUCT_ABSTRACT_ORDER = 'spy_product_attachment_product_abstract.order';

    /**
     * {@inheritDoc}
     * - Expands product abstract form data with attachment data.
     * - Gets attachments for the product abstract from database.
     * - Groups attachments by locale (default and localized versions).
     *
     * @api
     *
     * @param array<string, mixed> $formData
     * @param \Generated\Shared\Transfer\ProductAbstractTransfer $productAbstractTransfer
     *
     * @return array<string, mixed>
     */
    public function expand(array $formData, ProductAbstractTransfer $productAbstractTransfer): array
    {
        $attachmentData = $this->getAttachmentsForProductAbstract($productAbstractTransfer->getIdProductAbstractOrFail());

        foreach ($attachmentData as $key => $value) {
            $formData[$key] = $value;
        }

        return $formData;
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function getAttachmentsForProductAbstract(int $idProductAbstract): array
    {
        $productAttachmentCriteriaTransfer = (new ProductAttachmentCriteriaTransfer())
            ->setProductAttachmentCondition(
                (new ProductAttachmentConditionTransfer())
                    ->addIdProductAbstract($idProductAbstract),
            )
            ->addSort(
                (new SortTransfer())
                    ->setField(static::COL_PRODUCT_ATTACHMENT_PRODUCT_ABSTRACT_ORDER)
                    ->setIsAscending(true),
            )
            ->setWithProductAbstractRelation(true);

        $attachmentCollection = $this->getFacade()->getProductAttachmentCollection($productAttachmentCriteriaTransfer);

        return $this->groupAttachmentsByLocale(
            $attachmentCollection->getProductAttachments(),
            $idProductAbstract,
        );
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ProductAttachmentTransfer> $productAttachmentTransfers
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    protected function groupAttachmentsByLocale(ArrayObject $productAttachmentTransfers, int $idProductAbstract): array
    {
        $groupedAttachments = [];
        $groupedNamesByLocales = [];

        $locales = [
            (new LocaleTransfer())->setLocaleName('default'),
            ...$this->getFactory()->getLocaleFacade()->getLocaleCollection(),
        ];

        foreach ($locales as $localeTransfer) {
            if ($localeTransfer->getIdLocale()) {
                $groupedNamesByLocales[$localeTransfer->getIdLocale()] = $localeTransfer->getLocaleName();
            }

            $groupedAttachments[$this->getAttachmentFormName($localeTransfer->getLocaleNameOrFail())] = [];
        }

        $defaultKey = $this->getLocalizedPrefixName(
            $this->getConfig()->getAttachmentFormName(),
            $this->getConfig()->getProductAttachmentDefaultLocale(),
        );

        $groupedAttachments[$defaultKey] = [];

        foreach ($productAttachmentTransfers as $attachmentTransfer) {
            $fkLocale = $attachmentTransfer->getFkLocale();

            $attachmentData = [
                AttachmentForm::FIELD_ID_PRODUCT_ATTACHMENT => $attachmentTransfer->getIdProductAttachment(),
                AttachmentForm::FIELD_FK_LOCALE => $fkLocale,
                AttachmentForm::FIELD_LABEL => $attachmentTransfer->getLabel(),
                AttachmentForm::FIELD_URL => $attachmentTransfer->getUrl(),
                AttachmentForm::FIELD_SORT_ORDER => $attachmentTransfer->getSortOrder(),
            ];

            $localeKey = $fkLocale !== null && isset($groupedNamesByLocales[$fkLocale])
                ? $this->getAttachmentFormName($groupedNamesByLocales[$fkLocale])
                : $defaultKey;

            $groupedAttachments[$localeKey][] = $attachmentData;
        }

        return $groupedAttachments;
    }

    protected function getAttachmentFormName(string $localeName): string
    {
        return $this->getLocalizedPrefixName($this->getConfig()->getAttachmentFormName(), $localeName);
    }

    protected function getLocalizedPrefixName(string $prefix, string $localeName): string
    {
        if ($localeName === $this->getConfig()->getProductAttachmentDefaultLocale()) {
            return sprintf('%s_default', $prefix);
        }

        return sprintf('%s_%s', $prefix, $localeName);
    }
}
