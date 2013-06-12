<?php

namespace CTF\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GlobalStateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enableCtf', 'choice', array(
                'label' => 'Toggle CTF',
                'required' => true,
                'choices' => array('0' => 'Off', '1' => 'On'),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('enableChat', 'choice', array(
                'label' => 'Toggle Chat',
                'required' => true,
                'choices' => array('0' => 'Off', '1' => 'On'),
                'expanded' => true,
                'multiple' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\AdminBundle\Entity\GlobalState'
        ));
    }

    public function getName()
    {
        return 'ctf_adminbundle_globalstatetype';
    }
}
