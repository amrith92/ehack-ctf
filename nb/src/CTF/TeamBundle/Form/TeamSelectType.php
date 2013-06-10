<?php

namespace CTF\TeamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use CTF\TeamBundle\Form\Event\TeamSelectListener;

class TeamSelectType extends AbstractType {
    
    protected $teamSelectListener;
    
    public function __construct(TeamSelectListener $listener) {
        $this->teamSelectListener = $listener;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('is_selecting', 'choice', array(
            'multiple' => false,
            'expanded' => true,
            'choices' => array('select' => 'Select Team', 'create' => 'Create Team'),
            'label' => 'Select Or Create?'
        ))
        ;
        
        $builder->addEventSubscriber($this->teamSelectListener);
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\TeamBundle\Entity\TeamSelectDTO'
        ));
    }
    
    public function getName() {
        return 'ctf_teambundle_teamselecttype';
    }
}
