<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductAttachment\Communication\Plugin\ProductManagement;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductAttachmentCollectionTransfer;
use Generated\Shared\Transfer\ProductAttachmentTransfer;
use Spryker\Zed\ProductAttachment\Communication\Form\Product\AttachmentForm;
use Spryker\Zed\ProductAttachment\Communication\Plugin\ProductManagement\ProductAttachmentProductAbstractFormDataProviderExpanderPlugin;
use Spryker\Zed\ProductAttachment\ProductAttachmentConfig;
use Spryker\Zed\ProductAttachment\ProductAttachmentDependencyProvider;
use Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentCollectionExpanderPluginInterface;
use SprykerTest\Zed\ProductAttachment\ProductAttachmentCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductAttachment
 * @group Communication
 * @group Plugin
 * @group ProductManagement
 * @group ProductAttachmentProductAbstractFormDataProviderExpanderPluginTest
 * Add your own group annotations below this line
 */
class ProductAttachmentProductAbstractFormDataProviderExpanderPluginTest extends Unit
{
    protected const string LOCALE_SECTION_DEFAULT = 'default';

    protected const string LABEL_SUFFIX = ' (expanded)';

    protected ProductAttachmentCommunicationTester $tester;

    public function testGivenNoCollectionExpanderPluginsWhenExpandThenEachRowHoldsTheCoreFieldsInItsLocaleSection(): void
    {
        // Arrange
        $this->tester->setDependency(ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_EXPANDER, []);
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $localeTransfer = $this->tester->getFirstStoreLocale();
        $defaultAttachment = $this->tester->haveProductAttachmentLinkedToProductAbstract($productAbstractTransfer->getIdProductAbstractOrFail(), 'Manual', null, 1);
        $localizedAttachment = $this->tester->haveProductAttachmentLinkedToProductAbstract($productAbstractTransfer->getIdProductAbstractOrFail(), 'Handbuch', $localeTransfer->getIdLocaleOrFail(), 2);

        // Act
        $formData = (new ProductAttachmentProductAbstractFormDataProviderExpanderPlugin())->expand([], $productAbstractTransfer);

        // Assert
        $this->assertSame([$this->createCoreRow($defaultAttachment)], $this->extractCoreFields($formData[$this->getSectionFormName(static::LOCALE_SECTION_DEFAULT)]));
        $this->assertSame([$this->createCoreRow($localizedAttachment)], $this->extractCoreFields($formData[$this->getSectionFormName($localeTransfer->getLocaleNameOrFail())]));
    }

    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return list<array<string, mixed>>
     */
    protected function extractCoreFields(array $rows): array
    {
        $coreFieldNames = array_flip([
            AttachmentForm::FIELD_ID_PRODUCT_ATTACHMENT,
            AttachmentForm::FIELD_FK_LOCALE,
            AttachmentForm::FIELD_LABEL,
            AttachmentForm::FIELD_URL,
            AttachmentForm::FIELD_SORT_ORDER,
        ]);

        return array_map(static fn (array $row): array => array_intersect_key($row, $coreFieldNames), $rows);
    }

    public function testGivenCollectionExpanderPluginWhenExpandThenEachRowHoldsEveryPropertyOfTheExpandedAttachment(): void
    {
        // Arrange
        $this->tester->setDependency(
            ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_COLLECTION_EXPANDER,
            [$this->createLabelSuffixExpanderPlugin(static::LABEL_SUFFIX)],
        );
        $productAbstractTransfer = $this->tester->haveProductAbstract();
        $attachmentTransfer = $this->tester->haveProductAttachmentLinkedToProductAbstract($productAbstractTransfer->getIdProductAbstractOrFail(), 'Manual', null, 0);

        // Act
        $formData = (new ProductAttachmentProductAbstractFormDataProviderExpanderPlugin())->expand([], $productAbstractTransfer);

        // Assert
        $expectedAttachmentTransfer = (clone $attachmentTransfer)->setLabel('Manual' . static::LABEL_SUFFIX);
        $this->assertSame([$expectedAttachmentTransfer->toArray()], $formData[$this->getSectionFormName(static::LOCALE_SECTION_DEFAULT)]);
    }

    protected function getSectionFormName(string $localeName): string
    {
        return sprintf('%s_%s', (new ProductAttachmentConfig())->getAttachmentFormName(), $localeName);
    }

    /**
     * @return array<string, mixed>
     */
    protected function createCoreRow(ProductAttachmentTransfer $productAttachmentTransfer): array
    {
        return [
            AttachmentForm::FIELD_ID_PRODUCT_ATTACHMENT => $productAttachmentTransfer->getIdProductAttachment(),
            AttachmentForm::FIELD_FK_LOCALE => $productAttachmentTransfer->getFkLocale(),
            AttachmentForm::FIELD_LABEL => $productAttachmentTransfer->getLabel(),
            AttachmentForm::FIELD_URL => $productAttachmentTransfer->getUrl(),
            AttachmentForm::FIELD_SORT_ORDER => $productAttachmentTransfer->getSortOrder(),
        ];
    }

    protected function createLabelSuffixExpanderPlugin(string $suffix): ProductAttachmentCollectionExpanderPluginInterface
    {
        return new class ($suffix) implements ProductAttachmentCollectionExpanderPluginInterface {
            public function __construct(protected string $suffix)
            {
            }

            public function expand(ProductAttachmentCollectionTransfer $productAttachmentCollectionTransfer): ProductAttachmentCollectionTransfer
            {
                foreach ($productAttachmentCollectionTransfer->getProductAttachments() as $productAttachmentTransfer) {
                    $productAttachmentTransfer->setLabel($productAttachmentTransfer->getLabelOrFail() . $this->suffix);
                }

                return $productAttachmentCollectionTransfer;
            }
        };
    }
}
