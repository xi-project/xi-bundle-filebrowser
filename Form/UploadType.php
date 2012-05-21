<?php

namespace Xi\FilebrowserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class UploadType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('file', 'file', array(
                'property_path' => false,
            ))
        ;
    }

    public function getName()
    {
        return 'xi_filebrowserbundle_uploadtype';
    }
}