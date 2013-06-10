<?php

namespace CTF\TeamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TeamSelectType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('orRadioButton', 'choice', array(
            'multiple' => false,
            'expanded' => true,
            'label' => 'Select or Create a Team',
            'choices' => array('select' => 'Select Team', 'create' => 'Create Team'),
            'preferred_choices' => array('select')
        ))
        ->add('teams', 'entity', array(
            'class' => 'CTFTeamBundle:Team',
            'property' => 'name',
            'empty_value' => 'Select One',
            'label' => 'Select a Team'
        ))
        ;
    }
    
    public function getName() {
        return 'ctf_teambundle_teamselecttype';
    }
}
