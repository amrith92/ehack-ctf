<?php

namespace CTF\UserBundle\Form\Event;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEditEventListener implements EventSubscriberInterface {
    
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param factory FormFactoryInterface
     */
    public function __construct(FormFactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_BIND => 'preBind',
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }
    
    /**
     * @param event FormEvent
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        // Before binding the form, the "user" will be null
        if (null === $data) {
            return;
        }

        $form = $event->getForm();
        
        if (null !== $data->getCountry()) {
            $cty = $data->getCountry()->getId();
            $formOptions = array(
                'class' => 'CTF\UserBundle\Entity\Zone',
                'multiple' => false,
                'expanded' => false,
                'property' => 'name',
                'mapped' => true,
                'query_builder' => function(\CTF\UserBundle\Entity\ZoneRepository $er) use ($cty) {
                    return $er->findStatesByCountryIdQueryBuilder($cty);
                },
            );
            $form->add($this->factory->createNamed('state', 'entity', null, $formOptions));
        }
        
        if (null == $data->getPassword()) {
            $form->add($this->factory->createNamed('password', 'repeated', null, array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'property_path' => 'password'
            )));
        }
    }
    
    /**
     * @param event FormEvent
     */
    public function preBind(FormEvent $event)
    {
        $data = $event->getData();

        // Before binding the form, the "user" will be null
        if (null === $data) {
            return;
        }

        $form = $event->getForm();
        
        if (null !== $data['country']) {
            $cty = $data['country'];
            $formOptions = array(
                'class' => 'CTF\UserBundle\Entity\Zone',
                'multiple' => false,
                'expanded' => false,
                'property' => 'name',
                'mapped' => true,
                'query_builder' => function(\CTF\UserBundle\Entity\ZoneRepository $er) use ($cty) {
                    return $er->findStatesByCountryIdQueryBuilder($cty);
                },
            );
            $form->add($this->factory->createNamed('state', 'entity', null, $formOptions));
        }
        
        $form->add($this->factory->createNamed('password', 'repeated', $data['password'], array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'required' => true,
            'first_options'  => array('label' => 'Password'),
            'second_options' => array('label' => 'Repeat Password'),
            'property_path' => 'password'
        )));
    }
}
