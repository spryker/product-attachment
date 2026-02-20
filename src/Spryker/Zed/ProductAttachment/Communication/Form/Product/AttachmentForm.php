<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductAttachment\Communication\Form\Product;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AttachmentForm extends AbstractType
{
    public const string FIELD_ID_PRODUCT_ATTACHMENT = 'id_product_attachment';

    public const string FIELD_FK_LOCALE = 'fk_locale';

    public const string FIELD_LABEL = 'label';

    public const string FIELD_URL = 'url';

    public const string FIELD_SORT_ORDER = 'sort_order';

    public const string FIELD_FK_PRODUCT_ABSTRACT = 'fk_product_abstract';

    protected const string OPTION_LOCALE = 'locale';

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'compound' => true,
            static::OPTION_LOCALE => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'attachment';
    }

    /**
     * @deprecated Use {@link getBlockPrefix()} instead.
     */
    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    /**
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $this
            ->addIdProductAttachmentField($builder)
            ->addFkLocaleField($builder)
            ->addLabelField($builder)
            ->addUrlField($builder)
            ->addSortOrderField($builder);
    }

    protected function addIdProductAttachmentField(FormBuilderInterface $builder): static
    {
        $builder->add(static::FIELD_ID_PRODUCT_ATTACHMENT, HiddenType::class);

        return $this;
    }

    protected function addFkLocaleField(FormBuilderInterface $builder): static
    {
        $builder->add(static::FIELD_FK_LOCALE, HiddenType::class);

        return $this;
    }

    protected function addLabelField(FormBuilderInterface $builder): static
    {
        $builder->add(static::FIELD_LABEL, TextType::class, [
            'label' => 'Label',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        return $this;
    }

    protected function addUrlField(FormBuilderInterface $builder): static
    {
        $builder->add(static::FIELD_URL, UrlType::class, [
            'label' => 'URL',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        return $this;
    }

    protected function addSortOrderField(FormBuilderInterface $builder): static
    {
        $builder->add(static::FIELD_SORT_ORDER, IntegerType::class, [
            'label' => 'Sort Order',
            'required' => false,
            'attr' => [
                'min' => 0,
            ],
        ]);

        return $this;
    }
}
