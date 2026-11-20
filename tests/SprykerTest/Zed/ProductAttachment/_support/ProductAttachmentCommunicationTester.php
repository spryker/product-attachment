<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\ProductAttachment;

use Codeception\Actor;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;
use Orm\Zed\Locale\Persistence\SpyLocaleQuery;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachment;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentProductAbstract;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentProductAbstractQuery;
use Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentQuery;

/**
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 * @method \Spryker\Zed\ProductAttachment\Business\ProductAttachmentFacadeInterface getFacade()
 *
 * @SuppressWarnings(PHPMD)
 */
class ProductAttachmentCommunicationTester extends Actor
{
    use _generated\ProductAttachmentCommunicationTesterActions;

    public function ensureProductAttachmentTablesAreEmpty(): void
    {
        $this->cleanupProductAttachmentProductAbstractRelations();
        $this->cleanupProductAttachments();
    }

    /**
     * @return array<\Orm\Zed\ProductAttachment\Persistence\SpyProductAttachment>
     */
    public function getAllProductAttachments(): array
    {
        return SpyProductAttachmentQuery::create()->find()->getData();
    }

    public function findProductAttachmentByLabel(string $label): ?object
    {
        return SpyProductAttachmentQuery::create()
            ->filterByLabel($label)
            ->findOne();
    }

    /**
     * @return array<\Orm\Zed\ProductAttachment\Persistence\SpyProductAttachmentProductAbstract>
     */
    public function getAllProductAttachmentProductAbstractRelations(): array
    {
        return SpyProductAttachmentProductAbstractQuery::create()->find()->getData();
    }

    public function getLocaleIdByName(string $localeName): ?int
    {
        $localeEntity = SpyLocaleQuery::create()
            ->filterByLocaleName($localeName)
            ->findOne();

        return $localeEntity?->getIdLocale();
    }

    public function haveProductAttachmentLinkedToProductAbstract(
        int $idProductAbstract,
        string $label,
        ?int $idLocale,
        int $sortOrder
    ): ProductAttachmentTransfer {
        $productAttachmentEntity = (new SpyProductAttachment())
            ->setFkLocale($idLocale)
            ->setLabel($label)
            ->setUrl(sprintf('https://example.com/%s.pdf', uniqid()));
        $productAttachmentEntity->save();

        (new SpyProductAttachmentProductAbstract())
            ->setFkProductAbstract($idProductAbstract)
            ->setFkProductAttachment($productAttachmentEntity->getIdProductAttachment())
            ->setOrder($sortOrder)
            ->save();

        return (new ProductAttachmentTransfer())
            ->setIdProductAttachment($productAttachmentEntity->getIdProductAttachment())
            ->setFkLocale($productAttachmentEntity->getFkLocale())
            ->setLabel($productAttachmentEntity->getLabel())
            ->setUrl($productAttachmentEntity->getUrl())
            ->setSortOrder($sortOrder);
    }

    public function getFirstStoreLocale(): LocaleTransfer
    {
        return array_values($this->getLocator()->locale()->facade()->getLocaleCollection())[0];
    }

    protected function cleanupProductAttachments(): void
    {
        SpyProductAttachmentQuery::create()->deleteAll();
    }

    protected function cleanupProductAttachmentProductAbstractRelations(): void
    {
        SpyProductAttachmentProductAbstractQuery::create()->deleteAll();
    }
}
