<?php

namespace CTF\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CTF\UserBundle\Form\Event\UserEditEventListener;

class UserType extends AbstractType
{
    private $userEditListener;
    
    public function __construct(UserEditEventListener $listener) {
        $this->userEditListener = $listener;
    }
    
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
            ->add('phone', 'integer', array(
                'required' => true,
                'label' => 'Mobile'
            ))
            ->add('country', null, array(
                'label' => 'Country',
                'empty_value' => 'Choose a Country',
                'required' => true,
                'mapped' => true,
                'property_path' => 'country',
                'property' => 'name'
            ))
            ->add('city', 'text', array(
                'required' => true,
                'label' => 'City / Town'
            ))
            ->add('website', 'textarea', array(
                'label' => 'Website(s)'
            ))
            ->add('location')
            ->add('org', null, array(
                'label' => 'Organization'
            ))
        ;
        
        $builder->addEventSubscriber($this->userEditListener);
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
