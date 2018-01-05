<?php

namespace Andy\DiffuseRiverBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterType extends AbstractType
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
            ->add('code', TextType::class, array(
                'label' => 'Код (например: СO2)',
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('edIzm', TextType::class, array(
                'label' => 'Единицы измерения',
                'attr' => array(
                    'class' => 'form-control'
                )
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Andy\DiffuseRiverBundle\Entity\Parameter'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'andy_diffuseriverbundle_parameter';
    }


}
