<?php

namespace Xi\Bundle\FilebrowserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class UploadType extends AbstractType
{
    /**
     * @param  FormBuilderInterface $builder
     * @param  array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('file', 'file', array(
                'mapped' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'xi_filebrowserbundle_uploadtype';
    }
}