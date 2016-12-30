<?php

namespace AppBundle\Controller;

use AppBundle\Exception\FormInvalidException;
use AppBundle\Form\Security\ChangeEmail;
use AppBundle\Form\Security\NewPassword;
use SecuritiesService\Domain\Entity\User;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Domain\ValueObject\Password;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends Controller
{
    protected $cacheTime = null;

    public function indexAction()
    {
        $this->toView('activeTab', 'dashboard');

        return $this->renderTemplate('account:index');
    }

    public function settingsAction()
    {
        $this->toView('activeTab', 'settings');
        $visitor = $this->getUser();
        $usersService = $this->get('app.services.users');
        $user = $usersService->findByEmail(new Email($visitor->getUsername()));

        $emailForm = $this->createForm(ChangeEmail::class, [
            'currentEmail' => $user->getEmail(),
        ]);
        $passwordForm = $this->createForm(NewPassword::class);

        $emailForm->handleRequest($this->request);
        $passwordForm->handleRequest($this->request);

        if ($emailForm->isValid()) {
            $data = $emailForm->getData();
            try {
                // check the password was correct
                $this->validatePassword($data, $user);

                if ((string) $user->getEmail() == $data['email']) {
                    throw new FormInvalidException('E-mail was not changed');
                }

                // will throw ValidationException if invalid
                $newEmail = new Email($data['email']);

                // check the e-mail address doesn't already exist
                if ($usersService->emailExists($newEmail)) {
                    throw new FormInvalidException(
                        'Another user with this address already exists'
                    );
                }

                // now we can save it
                $usersService->updateEmailAddress($user, $newEmail);

                $this->addFlash(
                    'ok',
                    'Email address changed. You may need to login again'
                );
            } catch (ValidationException $e) {
                $emailForm->addError(new FormError($e->getMessage()));
            } catch (FormInvalidException $e) {
                $emailForm->addError(new FormError($e->getMessage()));
            }
        } elseif ($passwordForm->isValid()) {
            $data = $passwordForm->getData();
            try {
                // check the password was correct
                $this->validatePassword($data, $user);

                // ensure the passwords are ok
                $password = $data['password'];
                $passwordConfirm = $data['password_confirm'];

                if ($password != $passwordConfirm) {
                    $msg = 'Password fields do not match';

                    $passwordForm->get('password')->addError(new FormError($msg));
                    $passwordForm->get('password_confirm')->addError(new FormError($msg));
                    throw new FormInvalidException($msg);
                }

                $passwordDigest = new Password($password);

                // now we can save it
                $usersService->updatePassword($user, $passwordDigest);

                $this->addFlash(
                    'ok',
                    'Password changed sucessfully'
                );
            } catch (ValidationException $e) {
                $passwordForm->addError(new FormError($e->getMessage()));
            } catch (FormInvalidException $e) {
                $passwordForm->addError(new FormError($e->getMessage()));
            }
        }


        $this->toView('emailForm', $emailForm->createView());
        $this->toView('passwordForm', $passwordForm->createView());

        return $this->renderTemplate('account:settings');
    }

    private function validatePassword($data, User $user)
    {
        $oldPassword = $data['old_password'];
        if (!$user->passwordMatches($oldPassword)) {
            throw new FormInvalidException('Current Password incorrect');
        }
    }
}
