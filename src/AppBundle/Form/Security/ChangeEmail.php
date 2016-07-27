<?php

namespace AppBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeEmail extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-mail address',
                'data' => (string) $options['data']['currentEmail']
            ])
            ->add('old_password', PasswordType::class, ['label' => 'Current Password'])
            ->add('save', SubmitType::class, ['label' => 'Update e-mail address']);
    }

    public function getName()
    {
        return 'app_account_change_email';
    }
}
