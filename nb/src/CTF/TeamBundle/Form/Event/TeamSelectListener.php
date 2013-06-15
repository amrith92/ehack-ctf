<?php

namespace CTF\TeamBundle\Form\Event;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use \Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TeamSelectListener implements EventSubscriberInterface {
    
    /**
     *
     * @var FormFactoryInterface
     */
    private $factory;
    
    /**
     * 
     * @param \Symfony\Component\Form\FormFactoryInterface $factory
     */
    public function __construct(FormFactoryInterface $factory) {
        $this->factory = $factory;
    }
    
    /**
     * 
     * @return array
     */
    public static function getSubscribedEvents() {
        return array(
            FormEvents::PRE_BIND => 'preBind'
        );
    }
    
    /**
     * 
     * @param \Symfony\Component\Form\FormEvent $event
     * @return void
     */
    public function preBind(FormEvent $event) {
        $dto = $event->getData();
        
        if (null === $dto) {
            return;
        }
        
        $form = $event->getForm();
        
        if ($dto['is_selecting'] == 'select') {
            $this->addTeamSelectField($form);
        } else if ($dto['is_selecting'] == 'create') {
            $form->remove('team');
            $this->addTeamCreateForm($form);
        }
    }
    
    /**
     * 
     * @param AbstractType $form
     */
    protected function addTeamSelectField($form) {
        $form->add($this->factory->createNamed('team', 'entity', null, array(
            'class' => 'CTFTeamBundle:Team',
            'property' => 'name',
            'empty_value' => 'Select One',
            'label' => 'Select a Team',
            'attr' => array('class' => 'chzn-select')
        )));
        
        $form->add($this->factory->createNamed('message', 'textarea', "Hi! I'd like to join your team. Thanks :)", array(
            'label' => 'Message',
            'required' => true
        )));
    }
    
    /**
     * 
     * @param AbstractType $form
     */
    protected function addTeamCreateForm($form) {
        /*$form->add(
            $this->factory->createNamed('team', 'form',
                array (
                    'by_reference' => true,
                    'compund' => true,
                    'data_class' => '\CTF\TeamBundle\Entity\Team'
                )
            )
            ->add($this->factory->createNamed('name', 'text'))
            ->add($this->factory->createNamed('status', 'text'))
            ->add($this->factory->createNamed('teamPic', 'url'))
        );*/
        
        $form->add(
            $this->factory->createNamed('team', new \CTF\TeamBundle\Form\TeamType())
        );
    }
}