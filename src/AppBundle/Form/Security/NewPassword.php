<?php

namespace AppBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class NewPassword extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, ['label' => 'Current Password'])
            ->add('password', PasswordType::class, ['label' => 'New Password'])
            ->add('password_confirm', PasswordType::class, ['label' => 'Confirm new Password'])
            ->add('save', SubmitType::class, ['label' => 'Set new password']);
    }

    public function getName()
    {
        return 'app_account_new_password';
    }
}
