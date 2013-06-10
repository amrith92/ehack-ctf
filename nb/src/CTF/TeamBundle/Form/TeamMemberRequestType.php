<?php

namespace CTF\TeamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TeamMemberRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status')
            ->add('message')
            ->add('updatedTimestamp')
            ->add('createdTimestamp')
            ->add('user')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\TeamBundle\Entity\TeamMemberRequest'
        ));
    }

    public function getName()
    {
        return 'ctf_teambundle_teammemberrequesttype';
    }
}
