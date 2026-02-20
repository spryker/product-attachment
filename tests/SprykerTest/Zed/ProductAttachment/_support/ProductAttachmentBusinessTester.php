<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\ProductAttachment;

use Codeception\Actor;
use Generated\Shared\DataBuilder\ProductAttachmentBuilder;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAttachmentConditionTransfer;
use Generated\Shared\Transfer\ProductAttachmentCriteriaTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;
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
class ProductAttachmentBusinessTester extends Actor
{
    use _generated\ProductAttachmentBusinessTesterActions;

    /**
     * @param array<string, mixed> $override
     */
    public function haveProductAttachment(array $override = []): ProductAttachmentTransfer
    {
        $productAttachmentTransfer = $this->buildProductAttachment($override);

        $productAttachmentEntity = new SpyProductAttachment();
        $productAttachmentEntity->setFkLocale($productAttachmentTransfer->getFkLocale());
        $productAttachmentEntity->setLabel($productAttachmentTransfer->getLabel() ?? 'Test Label');
        $productAttachmentEntity->setUrl($productAttachmentTransfer->getUrl() ?? 'https://example.com/test-' . random_int(1, 999999));
        $productAttachmentEntity->save();

        return $productAttachmentTransfer
            ->setIdProductAttachment($productAttachmentEntity->getIdProductAttachment())
            ->setFkLocale($productAttachmentEntity->getFkLocale())
            ->setLabel($productAttachmentEntity->getLabel())
            ->setUrl($productAttachmentEntity->getUrl());
    }

    public function haveProductAbstractAttachmentRelation(
        int $idProductAbstract,
        int $idProductAttachment,
        int $sortOrder = 0
    ): void {
        $entity = new SpyProductAttachmentProductAbstract();
        $entity->setFkProductAbstract($idProductAbstract);
        $entity->setFkProductAttachment($idProductAttachment);
        $entity->setOrder($sortOrder);
        $entity->save();
    }

    /**
     * @param array<string, mixed> $override
     */
    public function haveProductAbstractForAttachment(array $override = []): ProductAbstractTransfer
    {
        return $this->haveProductAbstract($override);
    }

    /**
     * @param array<int>|null $productAbstractIds
     * @param array<int>|null $productAttachmentIds
     */
    public function buildProductAttachmentCriteria(
        ?array $productAbstractIds = null,
        ?array $productAttachmentIds = null
    ): ProductAttachmentCriteriaTransfer {
        $criteriaTransfer = new ProductAttachmentCriteriaTransfer();
        $conditionsTransfer = new ProductAttachmentConditionTransfer();

        if ($productAbstractIds !== null) {
            $conditionsTransfer->setProductAbstractIds($productAbstractIds);
        }

        if ($productAttachmentIds !== null) {
            $conditionsTransfer->setProductAttachmentIds($productAttachmentIds);
        }

        return $criteriaTransfer->setProductAttachmentCondition($conditionsTransfer);
    }

    public function assertProductAbstractAttachmentCount(int $idProductAbstract, int $expectedCount): void
    {
        $actualCount = SpyProductAttachmentProductAbstractQuery::create()
            ->filterByFkProductAbstract($idProductAbstract)
            ->count();

        $this->assertSame(
            $expectedCount,
            $actualCount,
            sprintf(
                'Expected %d attachments for product abstract %d, found %d',
                $expectedCount,
                $idProductAbstract,
                $actualCount,
            ),
        );
    }

    public function assertProductAbstractAttachmentRelationNotExists(
        int $idProductAbstract,
        int $idProductAttachment
    ): void {
        $exists = SpyProductAttachmentProductAbstractQuery::create()
            ->filterByFkProductAbstract($idProductAbstract)
            ->filterByFkProductAttachment($idProductAttachment)
            ->exists();

        $this->assertFalse(
            $exists,
            sprintf(
                'Expected relation between product abstract %d and attachment %d to not exist',
                $idProductAbstract,
                $idProductAttachment,
            ),
        );
    }

    public function findProductAttachmentLocale(int $idProductAttachment): ?int
    {
        $entity = SpyProductAttachmentQuery::create()
            ->findOneByIdProductAttachment($idProductAttachment);

        return $entity?->getFkLocale();
    }

    public function findProductAttachmentIdByLabel(string $label): ?int
    {
        $entity = SpyProductAttachmentQuery::create()
            ->filterByLabel($label)
            ->findOne();

        return $entity ? $entity->getIdProductAttachment() : null;
    }

    public function findProductAttachmentLabel(int $idProductAttachment): ?string
    {
        $entity = SpyProductAttachmentQuery::create()
            ->findOneByIdProductAttachment($idProductAttachment);

        return $entity?->getLabel();
    }

    /**
     * @param array<string, mixed> $override
     */
    public function buildProductAttachment(array $override = []): ProductAttachmentTransfer
    {
        return (new ProductAttachmentBuilder($override))->build();
    }
}
