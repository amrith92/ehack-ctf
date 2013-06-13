<?php

namespace CTF\QuestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Name'
            ))
            ->add('description', 'textarea', array(
                'label' => 'Description'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\QuestBundle\Entity\Stage'
        ));
    }

    public function getName()
    {
        return 'ctf_questbundle_stagetype';
    }
}
