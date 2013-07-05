<?php

namespace CTF\QuestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AnswerType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('answer', 'textarea', array(
            'label' => 'Answer',
            'required' => true,
            'attr' => array('rows' => 3)
        ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
    }
    
    public function getName() {
        return 'ctf_questbundle_answertype';
    }
}
