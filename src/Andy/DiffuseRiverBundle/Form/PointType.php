<?php

namespace Andy\DiffuseRiverBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Название',
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('coords', TextType::class, array(
                'label' => 'Координаты',
                'attr' => array(
                    'class' => 'form-control',
                    'readonly' => 'true',
                )
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Andy\DiffuseRiverBundle\Entity\Point'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'andy_diffuseriverbundle_point';
    }


}
