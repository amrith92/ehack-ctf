<?php

namespace CTF\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'required' => true,
                'read_only' => true
            ))
            ->add('email', 'email', array(
                'required' => true,
                'read_only' => true
            ))
            ->add('fname', 'text', array(
                'required' => true,
                'max_length' => 50,
                'label' => 'First Name'
            ))
            ->add('lname', 'text', array(
                'required' => true,
                'max_length' => 50,
                'label' => 'Last Name'
            ))
            ->add('dob', 'date', array(
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd-MM-yyyy',
                'attr' => array('class' => 'date'),
                'label' => 'Date Of Birth'
            ))
            ->add('aboutMe', 'textarea', array(
                'required' => true,
                'label' => 'About Me or Tagline'
            ))
            ->add('gender', 'choice', array(
                'choices'   => array('' => 'Select One', 'Male' => 'Male', 'Female' => 'Female'),
                'required'  => true
            ))
            ->add('phone')
            ->add('state')
            ->add('city')
            ->add('website', 'textarea', array(
                'label' => 'Website(s)'
            ))
            ->add('location')
            ->add('org', null, array(
                'label' => 'Organization'
            ))
        ;
    }

    /**
     * 
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\UserBundle\Entity\User'
        ));
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'ctf_userbundle_usertype';
    }
}
