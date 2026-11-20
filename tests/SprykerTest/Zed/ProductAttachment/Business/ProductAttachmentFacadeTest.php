<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\ProductAttachment\Business;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\ProductAttachmentCollectionTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;
use RuntimeException;
use Spryker\Zed\ProductAttachment\ProductAttachmentDependencyProvider;
use Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentCollectionExpanderPluginInterface;
use Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentCollectionPostSavePluginInterface;
use SprykerTest\Zed\ProductAttachment\ProductAttachmentBusinessTester;
use Throwable;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductAttachment
 * @group Business
 * @group Facade
 * @group ProductAttachmentFacadeTest
 * Add your own group annotations below this line
 */
class ProductAttachmentFacadeTest extends Unit
{
    protected ProductAttachmentBusinessTester $tester;

    public function testGivenProductIdFilterWhenGetCollectionThenReturnsOnlyLinkedAttachments(): void
    {
        // Arrange
        $productAbstract1 = $this->tester->haveProductAbstractForAttachment();
        $productAbstract2 = $this->tester->haveProductAbstractForAttachment();
        $attachment1 = $this->tester->haveProductAttachment([
            ProductAttachmentTransfer::LABEL => 'User Manual',
            ProductAttachmentTransfer::URL => 'https://example.com/manual.pdf',
        ]);
        $attachment2 = $this->tester->haveProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Quick Start Guide',
            ProductAttachmentTransfer::URL => 'https://example.com/quick-start.pdf',
        ]);
        $attachment3 = $this->tester->haveProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Technical Specifications',
            ProductAttachmentTransfer::URL => 'https://example.com/specs.pdf',
        ]);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract1->getIdProductAbstractOrFail(), $attachment1->getIdProductAttachmentOrFail(), 1);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract1->getIdProductAbstractOrFail(), $attachment2->getIdProductAttachmentOrFail(), 2);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract1->getIdProductAbstractOrFail(), $attachment3->getIdProductAttachmentOrFail(), 3);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract2->getIdProductAbstractOrFail(), $attachment2->getIdProductAttachmentOrFail(), 1);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract2->getIdProductAbstractOrFail(), $attachment3->getIdProductAttachmentOrFail(), 2);

        // Act
        $collectionTransfer = $this->tester->getFacade()->getProductAttachmentCollection(
            $this->tester
                ->buildProductAttachmentCriteria([$productAbstract1->getIdProductAbstractOrFail()])
                ->setWithProductAbstractRelation(true),
        );

        // Assert
        $this->assertCount(3, $collectionTransfer->getProductAttachments());

        $attachmentsById = $this->groupAttachmentsById($collectionTransfer);

        $this->assertArrayHasKey($attachment1->getIdProductAttachmentOrFail(), $attachmentsById);
        $this->assertSame('User Manual', $attachmentsById[$attachment1->getIdProductAttachmentOrFail()]->getLabel());
        $this->assertSame('https://example.com/manual.pdf', $attachmentsById[$attachment1->getIdProductAttachmentOrFail()]->getUrl());
        $this->assertSame(1, $attachmentsById[$attachment1->getIdProductAttachmentOrFail()]->getSortOrder());

        $this->assertArrayHasKey($attachment2->getIdProductAttachmentOrFail(), $attachmentsById);
        $this->assertSame('Quick Start Guide', $attachmentsById[$attachment2->getIdProductAttachmentOrFail()]->getLabel());
        $this->assertSame('https://example.com/quick-start.pdf', $attachmentsById[$attachment2->getIdProductAttachmentOrFail()]->getUrl());
        $this->assertSame(2, $attachmentsById[$attachment2->getIdProductAttachmentOrFail()]->getSortOrder());

        $this->assertArrayHasKey($attachment3->getIdProductAttachmentOrFail(), $attachmentsById);
        $this->assertSame('Technical Specifications', $attachmentsById[$attachment3->getIdProductAttachmentOrFail()]->getLabel());
        $this->assertSame('https://example.com/specs.pdf', $attachmentsById[$attachment3->getIdProductAttachmentOrFail()]->getUrl());
        $this->assertSame(3, $attachmentsById[$attachment3->getIdProductAttachmentOrFail()]->getSortOrder());
    }

    public function testGivenAttachmentsWithDifferentLocalesWhenGetCollectionThenReturnsAttachmentsGroupedByLocale(): void
    {
        // Arrange
        $localeEn = $this->tester->haveLocale([LocaleTransfer::LOCALE_NAME => 'en_US']);
        $localeDe = $this->tester->haveLocale([LocaleTransfer::LOCALE_NAME => 'de_DE']);
        $productAbstract = $this->tester->haveProductAbstractForAttachment();

        $attachmentDefault = $this->tester->haveProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Manual Default',
            ProductAttachmentTransfer::URL => 'https://example.com/manual.pdf',
            ProductAttachmentTransfer::FK_LOCALE => null,
        ]);
        $attachmentEn = $this->tester->haveProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Manual EN',
            ProductAttachmentTransfer::URL => 'https://example.com/manual-en.pdf',
            ProductAttachmentTransfer::FK_LOCALE => $localeEn->getIdLocaleOrFail(),
        ]);
        $attachmentDe = $this->tester->haveProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Manual DE',
            ProductAttachmentTransfer::URL => 'https://example.com/manual-de.pdf',
            ProductAttachmentTransfer::FK_LOCALE => $localeDe->getIdLocaleOrFail(),
        ]);

        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachmentDefault->getIdProductAttachmentOrFail(), 1);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachmentEn->getIdProductAttachmentOrFail(), 2);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachmentDe->getIdProductAttachmentOrFail(), 3);

        // Act
        $collectionTransfer = $this->tester->getFacade()->getProductAttachmentCollection(
            $this->tester->buildProductAttachmentCriteria([$productAbstract->getIdProductAbstractOrFail()]),
        );

        // Assert
        $this->assertCount(3, $collectionTransfer->getProductAttachments());

        $attachmentsById = $this->groupAttachmentsById($collectionTransfer);

        $this->assertArrayHasKey($attachmentDefault->getIdProductAttachmentOrFail(), $attachmentsById);
        $this->assertNull($attachmentsById[$attachmentDefault->getIdProductAttachmentOrFail()]->getFkLocale());

        $this->assertArrayHasKey($attachmentEn->getIdProductAttachmentOrFail(), $attachmentsById);
        $this->assertSame($localeEn->getIdLocaleOrFail(), $attachmentsById[$attachmentEn->getIdProductAttachmentOrFail()]->getFkLocale());

        $this->assertArrayHasKey($attachmentDe->getIdProductAttachmentOrFail(), $attachmentsById);
        $this->assertSame($localeDe->getIdLocaleOrFail(), $attachmentsById[$attachmentDe->getIdProductAttachmentOrFail()]->getFkLocale());
    }

    public function testGivenNewAttachmentsWhenSaveCollectionThenCreatesAttachments(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $productAbstract->addProductAttachment($this->tester->buildProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Manual 1',
            ProductAttachmentTransfer::URL => 'https://example.com/manual1.pdf',
        ]));
        $productAbstract->addProductAttachment($this->tester->buildProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Manual 2',
            ProductAttachmentTransfer::URL => 'https://example.com/manual2.pdf',
        ]));

        // Act
        $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract);

        // Assert
        $this->tester->assertProductAbstractAttachmentCount($productAbstract->getIdProductAbstractOrFail(), 2);
    }

    public function testGivenNewAttachmentsForDifferentLocalesWhenSaveCollectionThenCreatesAllAttachments(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $localeEn = $this->tester->haveLocale([LocaleTransfer::LOCALE_NAME => 'en_US']);
        $localeDe = $this->tester->haveLocale([LocaleTransfer::LOCALE_NAME => 'de_DE']);

        $productAbstract->addProductAttachment($this->tester->buildProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Manual Default',
            ProductAttachmentTransfer::URL => 'https://example.com/manual.pdf',
            ProductAttachmentTransfer::FK_LOCALE => null,
        ]));
        $productAbstract->addProductAttachment($this->tester->buildProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Manual EN',
            ProductAttachmentTransfer::URL => 'https://example.com/manual-en.pdf',
            ProductAttachmentTransfer::FK_LOCALE => $localeEn->getIdLocaleOrFail(),
        ]));
        $productAbstract->addProductAttachment($this->tester->buildProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Manual DE',
            ProductAttachmentTransfer::URL => 'https://example.com/manual-de.pdf',
            ProductAttachmentTransfer::FK_LOCALE => $localeDe->getIdLocaleOrFail(),
        ]));

        // Act
        $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract);

        // Assert
        $this->tester->assertProductAbstractAttachmentCount($productAbstract->getIdProductAbstractOrFail(), 3);

        $defaultAttachmentId = $this->tester->findProductAttachmentIdByLabel('Manual Default');
        $this->assertNotNull($defaultAttachmentId);
        $this->assertNull($this->tester->findProductAttachmentLocale($defaultAttachmentId));

        $enAttachmentId = $this->tester->findProductAttachmentIdByLabel('Manual EN');
        $this->assertNotNull($enAttachmentId);
        $this->assertSame($localeEn->getIdLocaleOrFail(), $this->tester->findProductAttachmentLocale($enAttachmentId));

        $deAttachmentId = $this->tester->findProductAttachmentIdByLabel('Manual DE');
        $this->assertNotNull($deAttachmentId);
        $this->assertSame($localeDe->getIdLocaleOrFail(), $this->tester->findProductAttachmentLocale($deAttachmentId));
    }

    public function testGivenUpdatedLabelWhenSaveCollectionThenUpdatesAttachment(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $attachment = $this->tester->haveProductAttachment([ProductAttachmentTransfer::LABEL => 'Old Manual']);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachment->getIdProductAttachmentOrFail());
        $productAbstract->addProductAttachment($attachment->setLabel('New Manual'));

        // Act
        $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract);

        // Assert
        $this->assertSame('New Manual', $this->tester->findProductAttachmentLabel($attachment->getIdProductAttachmentOrFail()));
    }

    public function testGivenRemovedAttachmentWhenSaveCollectionThenDeletesRelation(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $attachment1 = $this->tester->haveProductAttachment();
        $attachment2 = $this->tester->haveProductAttachment();
        $attachment3 = $this->tester->haveProductAttachment();
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachment1->getIdProductAttachmentOrFail());
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachment2->getIdProductAttachmentOrFail());
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachment3->getIdProductAttachmentOrFail());
        $productAbstract->addProductAttachment($attachment1);
        $productAbstract->addProductAttachment($attachment2);

        // Act
        $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract);

        // Assert
        $this->tester->assertProductAbstractAttachmentRelationNotExists($productAbstract->getIdProductAbstractOrFail(), $attachment3->getIdProductAttachmentOrFail());
    }

    public function testGivenMixedOperationsWhenSaveCollectionThenHandlesAllCorrectly(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $attachmentA = $this->tester->haveProductAttachment([ProductAttachmentTransfer::LABEL => 'Attachment A']);
        $attachmentB = $this->tester->haveProductAttachment([ProductAttachmentTransfer::LABEL => 'Attachment B']);
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachmentA->getIdProductAttachmentOrFail());
        $this->tester->haveProductAbstractAttachmentRelation($productAbstract->getIdProductAbstractOrFail(), $attachmentB->getIdProductAttachmentOrFail());
        $productAbstract->addProductAttachment($attachmentA->setLabel('Attachment A Updated'));
        $productAbstract->addProductAttachment($this->tester->buildProductAttachment([
            ProductAttachmentTransfer::LABEL => 'Attachment C',
            ProductAttachmentTransfer::URL => 'https://example.com/attachment-c.pdf',
        ]));

        // Act
        $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract);

        // Assert
        $this->assertSame('Attachment A Updated', $this->tester->findProductAttachmentLabel($attachmentA->getIdProductAttachmentOrFail()));
        $this->assertNotNull($this->tester->findProductAttachmentIdByLabel('Attachment C'));
        $this->tester->assertProductAbstractAttachmentRelationNotExists($productAbstract->getIdProductAbstractOrFail(), $attachmentB->getIdProductAttachmentOrFail());
    }

    public function testGivenNoExpanderPluginsWhenGetCollectionThenReturnsUnchangedCoreAttachments(): void
    {
        // Arrange
        $this->tester->setDependency(ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_EXPANDER, []);
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $attachments = $this->haveRelatedAttachments($productAbstract->getIdProductAbstractOrFail(), ['Manual First', 'Manual Second']);

        // Act
        $collectionTransfer = $this->tester->getFacade()->getProductAttachmentCollection(
            $this->tester->buildProductAttachmentCriteria([$productAbstract->getIdProductAbstractOrFail()]),
        );

        // Assert
        $this->assertEqualsCanonicalizing($this->extractAttachmentData($attachments), $this->extractAttachmentData($collectionTransfer->getProductAttachments()->getArrayCopy()));
    }

    public function testGivenExpanderPluginsWhenGetCollectionThenEachPluginExpandsFullCollectionOnceInRegisteredOrder(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $this->haveRelatedAttachments($productAbstract->getIdProductAbstractOrFail(), ['Manual First', 'Manual Second']);
        $pluginCalls = $this->haveLabelSuffixExpanderPlugins(['-first', '-second']);

        // Act
        $collectionTransfer = $this->tester->getFacade()->getProductAttachmentCollection(
            $this->tester->buildProductAttachmentCriteria([$productAbstract->getIdProductAbstractOrFail()]),
        );

        // Assert
        $this->assertSame(['-first:2', '-second:2'], $pluginCalls->getArrayCopy());
        $this->assertEqualsCanonicalizing(['Manual First-first-second', 'Manual Second-first-second'], $this->extractAttachmentLabels($collectionTransfer));
    }

    public function testGivenNoPostSavePluginsWhenSaveCollectionThenStoresAttachmentsAndRelations(): void
    {
        // Arrange
        $this->tester->setDependency(ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_POST_SAVE, []);
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $productAbstract->addProductAttachment($this->tester->buildProductAttachment([ProductAttachmentTransfer::LABEL => 'Manual No Plugin']));

        // Act
        $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract);

        // Assert
        $this->tester->assertProductAbstractAttachmentCount($productAbstract->getIdProductAbstractOrFail(), 1);
        $this->assertNotNull($this->tester->findProductAttachmentIdByLabel('Manual No Plugin'));
    }

    public function testGivenPostSavePluginWhenSaveCollectionThenPluginRunsOnceWithIdsOfExistingAndNewAttachments(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $existingAttachment = $this->haveRelatedAttachments($productAbstract->getIdProductAbstractOrFail(), ['Manual Existing'])[0];
        $productAbstract->addProductAttachment($existingAttachment)->addProductAttachment($this->tester->buildProductAttachment([ProductAttachmentTransfer::LABEL => 'Manual New']));
        $postSaveCalls = $this->havePostSavePluginSpy();

        // Act
        $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract);

        // Assert
        $this->assertCount(1, $postSaveCalls);
        $this->assertSame([$existingAttachment->getIdProductAttachmentOrFail(), $this->tester->findProductAttachmentIdByLabel('Manual New')], $this->extractAttachmentIds($postSaveCalls[0]));
    }

    public function testGivenEmptyAttachmentListWhenSaveCollectionThenPluginRunsOnceWithEmptyCollectionAndRelationsAreRemoved(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $attachment = $this->haveRelatedAttachments($productAbstract->getIdProductAbstractOrFail(), ['Manual Removed'])[0];
        $postSaveCalls = $this->havePostSavePluginSpy();

        // Act
        $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract);

        // Assert
        $this->assertCount(1, $postSaveCalls);
        $this->assertCount(0, $postSaveCalls[0]->getProductAttachments());
        $this->tester->assertProductAbstractAttachmentRelationNotExists($productAbstract->getIdProductAbstractOrFail(), $attachment->getIdProductAttachmentOrFail());
    }

    /**
     * @disableTransaction
     */
    public function testGivenFailingPostSavePluginWhenSaveCollectionThenAttachmentsAndRelationsAreRolledBack(): void
    {
        // Arrange
        $productAbstract = $this->tester->haveProductAbstractForAttachment();
        $productAbstract->addProductAttachment($this->tester->buildProductAttachment([ProductAttachmentTransfer::LABEL => uniqid('Manual Rolled Back ', true)]));
        $this->tester->setDependency(ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_POST_SAVE, [$this->createFailingPostSavePlugin()]);

        // Act
        $throwable = $this->catchThrowable(fn () => $this->tester->getFacade()->saveProductAbstractAttachmentCollection($productAbstract));

        // Assert
        $this->assertInstanceOf(RuntimeException::class, $throwable);
        $this->tester->assertProductAbstractAttachmentCount($productAbstract->getIdProductAbstractOrFail(), 0);
        $this->assertNull($this->tester->findProductAttachmentIdByLabel($productAbstract->getProductAttachments()->offsetGet(0)->getLabelOrFail()));
    }

    /**
     * @param array<string> $labels
     *
     * @return array<\Generated\Shared\Transfer\ProductAttachmentTransfer>
     */
    protected function haveRelatedAttachments(int $idProductAbstract, array $labels): array
    {
        $attachments = [];

        foreach ($labels as $index => $label) {
            $attachment = $this->tester->haveProductAttachment([ProductAttachmentTransfer::LABEL => $label]);
            $this->tester->haveProductAbstractAttachmentRelation($idProductAbstract, $attachment->getIdProductAttachmentOrFail(), $index + 1);
            $attachments[] = $attachment;
        }

        return $attachments;
    }

    /**
     * @param array<string> $suffixes
     *
     * @return \ArrayObject<int, string>
     */
    protected function haveLabelSuffixExpanderPlugins(array $suffixes): ArrayObject
    {
        $pluginCalls = new ArrayObject();
        $plugins = [];

        foreach ($suffixes as $suffix) {
            $plugins[] = $this->createLabelSuffixExpanderPlugin($suffix, $pluginCalls);
        }

        $this->tester->setDependency(ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_EXPANDER, $plugins);

        return $pluginCalls;
    }

    /**
     * @param \ArrayObject<int, string> $pluginCalls
     */
    protected function createLabelSuffixExpanderPlugin(string $suffix, ArrayObject $pluginCalls): ProductAttachmentCollectionExpanderPluginInterface
    {
        return new class ($suffix, $pluginCalls) implements ProductAttachmentCollectionExpanderPluginInterface {
            /**
             * @param \ArrayObject<int, string> $pluginCalls
             */
            public function __construct(protected string $suffix, protected ArrayObject $pluginCalls)
            {
            }

            public function expand(ProductAttachmentCollectionTransfer $productAttachmentCollectionTransfer): ProductAttachmentCollectionTransfer
            {
                $this->pluginCalls->append($this->suffix . ':' . $productAttachmentCollectionTransfer->getProductAttachments()->count());

                foreach ($productAttachmentCollectionTransfer->getProductAttachments() as $productAttachmentTransfer) {
                    $productAttachmentTransfer->setLabel($productAttachmentTransfer->getLabelOrFail() . $this->suffix);
                }

                return $productAttachmentCollectionTransfer;
            }
        };
    }

    /**
     * @return \ArrayObject<int, \Generated\Shared\Transfer\ProductAttachmentCollectionTransfer>
     */
    protected function havePostSavePluginSpy(): ArrayObject
    {
        $postSaveCalls = new ArrayObject();
        $this->tester->setDependency(ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_POST_SAVE, [
            new class ($postSaveCalls) implements ProductAttachmentCollectionPostSavePluginInterface {
                /**
                 * @param \ArrayObject<int, \Generated\Shared\Transfer\ProductAttachmentCollectionTransfer> $postSaveCalls
                 */
                public function __construct(protected ArrayObject $postSaveCalls)
                {
                }

                public function postSave(ProductAttachmentCollectionTransfer $productAttachmentCollectionTransfer): ProductAttachmentCollectionTransfer
                {
                    $this->postSaveCalls->append($productAttachmentCollectionTransfer);

                    return $productAttachmentCollectionTransfer;
                }
            },
        ]);

        return $postSaveCalls;
    }

    protected function createFailingPostSavePlugin(): ProductAttachmentCollectionPostSavePluginInterface
    {
        return new class () implements ProductAttachmentCollectionPostSavePluginInterface {
            public function postSave(ProductAttachmentCollectionTransfer $productAttachmentCollectionTransfer): ProductAttachmentCollectionTransfer
            {
                throw new RuntimeException('Post-save failure.');
            }
        };
    }

    protected function catchThrowable(callable $callable): ?Throwable
    {
        try {
            $callable();
        } catch (Throwable $throwable) {
            return $throwable;
        }

        return null;
    }

    /**
     * @return array<string>
     */
    protected function extractAttachmentLabels(ProductAttachmentCollectionTransfer $collectionTransfer): array
    {
        $labels = [];

        foreach ($collectionTransfer->getProductAttachments() as $attachment) {
            $labels[] = $attachment->getLabelOrFail();
        }

        return $labels;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductAttachmentTransfer> $attachments
     *
     * @return array<array<string, mixed>>
     */
    protected function extractAttachmentData(array $attachments): array
    {
        $attachmentData = [];

        foreach ($attachments as $attachment) {
            $attachmentData[] = [
                ProductAttachmentTransfer::ID_PRODUCT_ATTACHMENT => $attachment->getIdProductAttachment(),
                ProductAttachmentTransfer::LABEL => $attachment->getLabel(),
                ProductAttachmentTransfer::URL => $attachment->getUrl(),
                ProductAttachmentTransfer::FK_LOCALE => $attachment->getFkLocale(),
            ];
        }

        return $attachmentData;
    }

    /**
     * @return array<int>
     */
    protected function extractAttachmentIds(ProductAttachmentCollectionTransfer $collectionTransfer): array
    {
        $attachmentIds = [];

        foreach ($collectionTransfer->getProductAttachments() as $attachment) {
            $attachmentIds[] = $attachment->getIdProductAttachmentOrFail();
        }

        return $attachmentIds;
    }

    /**
     * @return array<int, \Generated\Shared\Transfer\ProductAttachmentTransfer>
     */
    protected function groupAttachmentsById(ProductAttachmentCollectionTransfer $collectionTransfer): array
    {
        $attachmentsById = [];

        foreach ($collectionTransfer->getProductAttachments() as $attachment) {
            $attachmentsById[$attachment->getIdProductAttachmentOrFail()] = $attachment;
        }

        return $attachmentsById;
    }
}
