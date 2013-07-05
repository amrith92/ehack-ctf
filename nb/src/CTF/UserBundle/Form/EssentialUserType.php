<?php

namespace CTF\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CTF\UserBundle\Form\Transformer\PlacesToOrganizationTransformer;
use CTF\UserBundle\Form\Transformer\TextToPointTransformer;

/**
 * \CTF\UserBundle\Form\EssentialUserType
 * 
 * For use in the registration process
 */
class EssentialUserType extends AbstractType {
    
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $entityManager = $options['em'];
        $organizationTransformer = new PlacesToOrganizationTransformer($entityManager);
        $pointTransformer = new TextToPointTransformer();
        
        $builder
            ->add('username', 'text', array(
                'required' => true,
                'read_only' => true
            ))
            ->add('email', 'email', array(
                'required' => true
            ))
            ->add('fname', 'text', array(
                'required' => true,
                'max_length' => 50,
                'label' => 'Name',
                'attr' => array('class' => 'span4', 'placeholder' => 'First Name')
            ))
            ->add('lname', 'text', array(
                'required' => true,
                'max_length' => 50,
                'label' => 'Last Name',
                'attr' => array('class' => 'span4', 'placeholder' => 'Last Name')
            ))
            ->add('phone', 'integer', array(
                'required' => true,
                'label' => 'Mobile'
            ))
            ->add(
                $builder->create('location', 'text', array(
                    'label' => 'Location',
                    'read_only' => true,
                    'required' => false
                ))->addModelTransformer($pointTransformer)
            )
            ->add(
                $builder->create('org', 'text', array(
                    'label' => 'Organization',
                    'attr' => array('autocomplete' => 'off', 'class' => 'input-xlarge')
                ))->addModelTransformer($organizationTransformer)
            )
        ;
    }
    
    /**
     * 
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\UserBundle\Entity\User',
            'validation_groups' => array('registration'),
        ));
        
        $resolver->setRequired(array(
            'em',
        ));

        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'ctf_userbundle_essentialusertype';
    }
}
