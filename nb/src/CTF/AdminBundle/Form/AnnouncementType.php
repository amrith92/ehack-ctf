<?php

namespace CTF\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('announcement', 'textarea', array(
                'label' => 'Announcement',
                'required' => true,
                'trim' => true
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\AdminBundle\Entity\Announcement'
        ));
    }

    public function getName()
    {
        return 'ctf_adminbundle_announcementtype';
    }
}
