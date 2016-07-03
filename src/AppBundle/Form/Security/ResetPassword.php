<?php

namespace AppBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ResetPassword extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
            ->add('reset', 'submit', array('label' => 'Send Reset Code'));
    }

    public function getName()
    {
        return 'app_security_reset_password';
    }
}
