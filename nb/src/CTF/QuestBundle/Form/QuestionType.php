<?php

namespace CTF\QuestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stage', 'entity', array(
                'label' => 'Stage',
                'class' => 'CTFQuestBundle:Stage',
                'property' => 'name',
                'mapped' => false,
                'required' => true
            ))
            ->add('level', 'integer', array(
                'label' => 'Level',
                'required' => true
            ))
            ->add('title', 'text', array(
                'label' => 'Title',
                'required' => true,
                'attr' => array('class' => 'input-xxlarge')
            ))
            ->add('content', 'textarea', array(
                'label' => 'Description',
                'required' => true,
                'attr' => array('class' => 'rich')
            ))
            ->add('answerTemplate', 'textarea', array(
                'label' => 'Answer Template',
                'required' => true,
                'attr' => array('rows' => '3')
            ))
            ->add('hints', 'textarea', array(
                'label' => 'Hints',
                'required' => true,
                'attr' => array('class' => 'rich')
            ))
            ->add('attachment', 'file', array(
                'label' => 'Attachment',
                'mapped' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CTF\QuestBundle\Entity\Question'
        ));
    }

    public function getName()
    {
        return 'ctf_questbundle_questiontype';
    }
}
