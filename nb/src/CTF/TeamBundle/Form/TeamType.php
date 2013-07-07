<?php

namespace CTF\TeamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Name'
            ))
            ->add('status')
            ->add('teamPic', 'hidden', array(
                'label' => 'Team Display Picture URL',
                'required' => true
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\TeamBundle\Entity\Team'
        ));
    }

    public function getName()
    {
        return 'ctf_teambundle_teamtype';
    }
}
