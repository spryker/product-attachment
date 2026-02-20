<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Communication\Plugin\ProductManagement;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductAttachment\Communication\Form\Product\AttachmentForm;
use Spryker\Zed\ProductManagementExtension\Dependency\Plugin\ProductAbstractFormExpanderPluginInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method \Spryker\Zed\ProductAttachment\Communication\ProductAttachmentCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductAttachment\ProductAttachmentConfig getConfig()
 */
class ProductAttachmentProductAbstractFormExpanderPlugin extends AbstractPlugin implements ProductAbstractFormExpanderPluginInterface
{
    protected const string OPTION_LOCALE = 'locale';

    /**
     * {@inheritDoc}
     * - Expands product abstract form with attachment form fields for default and localized attachments.
     *
     * @api
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function expand(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $this->addAttachmentForms($builder, $options);

        return $builder;
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function addAttachmentForms(FormBuilderInterface $builder, array $options): void
    {
        $localeCollection = $this->getLocaleCollection();

        foreach ($localeCollection as $localeTransfer) {
            $formName = $this->getAttachmentFormName($localeTransfer->getLocaleNameOrFail());

            $this->addAttachmentForm($builder, $formName, $options);
        }
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function addAttachmentForm(FormBuilderInterface $builder, string $name, array $options): void
    {
        $builder->add($name, CollectionType::class, [
            'entry_type' => AttachmentForm::class,
            'entry_options' => [
                static::OPTION_LOCALE => $options[static::OPTION_LOCALE] ?? null,
            ],
            'label' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'prototype_name' => '__attachment_name__',
            'attr' => [
                'template_path' => '@ProductAttachment/Product/_partials/attachment-form.twig',
            ],
        ]);
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

    /**
     * @return array<\Generated\Shared\Transfer\LocaleTransfer>
     */
    protected function getLocaleCollection(): array
    {
        $result = [];

        $result[] = (new LocaleTransfer())
            ->setLocaleName($this->getConfig()->getProductAttachmentDefaultLocale());

        foreach ($this->getFactory()->getLocaleFacade()->getLocaleCollection() as $localeTransfer) {
            $result[] = $localeTransfer;
        }

        return $result;
    }
}
