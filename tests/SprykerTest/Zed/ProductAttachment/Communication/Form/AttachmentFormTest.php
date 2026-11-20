<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductAttachment\Communication\Form;

use Codeception\Test\Unit;
use Spryker\Zed\ProductAttachment\Communication\Form\Product\AttachmentForm;
use Spryker\Zed\ProductAttachment\ProductAttachmentDependencyProvider;
use Spryker\Zed\ProductAttachmentExtension\Dependency\Plugin\ProductAttachmentFormExpanderPluginInterface;
use SprykerTest\Zed\ProductAttachment\ProductAttachmentCommunicationTester;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductAttachment
 * @group Communication
 * @group Form
 * @group AttachmentFormTest
 * Add your own group annotations below this line
 */
class AttachmentFormTest extends Unit
{
    protected const string FIELD_EXTENSION = 'is_assistant_knowledge';

    protected ProductAttachmentCommunicationTester $tester;

    public function testGivenNoFormExpanderPluginsWhenBuildFormThenFormHasOnlyTheCoreFields(): void
    {
        // Arrange
        $this->tester->setDependency(ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_FORM_EXPANDER, []);

        // Act
        $attachmentForm = $this->createAttachmentForm();

        // Assert
        $this->assertSame($this->getCoreFieldNames(), array_keys($attachmentForm->all()));
    }

    public function testGivenFormExpanderPluginWhenBuildFormThenFormHasTheCoreFieldsAndTheExtensionField(): void
    {
        // Arrange
        $this->tester->setDependency(
            ProductAttachmentDependencyProvider::PLUGINS_PRODUCT_ATTACHMENT_FORM_EXPANDER,
            [$this->createCheckboxFormExpanderPlugin(static::FIELD_EXTENSION)],
        );

        // Act
        $attachmentForm = $this->createAttachmentForm();

        // Assert
        $this->assertSame([...$this->getCoreFieldNames(), static::FIELD_EXTENSION], array_keys($attachmentForm->all()));
    }

    protected function createAttachmentForm(): FormInterface
    {
        return Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->getFormFactory()
            ->create(AttachmentForm::class);
    }

    /**
     * @return list<string>
     */
    protected function getCoreFieldNames(): array
    {
        return [
            AttachmentForm::FIELD_ID_PRODUCT_ATTACHMENT,
            AttachmentForm::FIELD_FK_LOCALE,
            AttachmentForm::FIELD_LABEL,
            AttachmentForm::FIELD_URL,
            AttachmentForm::FIELD_SORT_ORDER,
        ];
    }

    protected function createCheckboxFormExpanderPlugin(string $fieldName): ProductAttachmentFormExpanderPluginInterface
    {
        return new class ($fieldName) implements ProductAttachmentFormExpanderPluginInterface {
            public function __construct(protected string $fieldName)
            {
            }

            /**
             * @param array<string, mixed> $options
             */
            public function expand(FormBuilderInterface $builder, array $options): FormBuilderInterface
            {
                return $builder->add($this->fieldName, CheckboxType::class, ['required' => false]);
            }
        };
    }
}
