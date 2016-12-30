<?php
namespace AppBundle\Controller;

use AppBundle\Exception\FormInvalidException;
use AppBundle\Form\Security\ResetPassword;
use AppBundle\Form\Security\Register;
use AppBundle\Form\Security\SetPassword;
use AppBundle\Security\Visitor;
use SecuritiesService\Domain\Exception\EntityNotFoundException;
use SecuritiesService\Domain\Exception\InvalidCredentialsException;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Domain\ValueObject\Password;
use SecuritiesService\Domain\ValueObject\ResetToken;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller
{
    public function registerAction(Request $request)
    {
        if ($this->userIsLoggedIn()) {
            $this->addFlash(
                'info',
                'You are already logged in'
            );
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(Register::class);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            // begin to process form
            try {
                // attempt to create an e-mail address object
                try {
                    $email = new Email($data['email']);
                } catch (ValidationException $e) {
                    $form->get('email')->addError(new FormError($e->getMessage()));
                    throw $e; // re-throw
                }

                $usersService = $this->get('app.services.users');

                // ensure the passwords are ok
                $password = $data['password'];
                $passwordConfirm = $data['password_confirm'];

                if ($password != $passwordConfirm) {
                    $msg = 'Password fields do not match';

                    $form->get('password')->addError(new FormError($msg));
                    $form->get('password_confirm')->addError(new FormError($msg));
                    throw new FormInvalidException($msg);
                }

                // check the e-mail address doesn't already exist
                if ($usersService->emailExists($email)) {
                    throw new FormInvalidException('E-mail already exists');
                }


                $passwordDigest = new Password($password);

                // create the new user and save
                $user = $usersService->createNewUser($email, $passwordDigest);

                // log the user in, and redirect to welcome
                $visitor = new Visitor($user);
                $token = new UsernamePasswordToken($visitor, null, 'main', $visitor->getRoles());
                $this->get('security.token_storage')->setToken($token);

                return $this->redirectToRoute('welcome');
            } catch (ValidationException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (FormInvalidException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        $this->toView('form', $form->createView());

        return $this->renderTemplate('security:register', 'Register');
    }

    public function loginAction(Request $request)
    {
        if ($this->userIsLoggedIn()) {
            $this->addFlash(
                'info',
                'You are already logged in'
            );
            return $this->redirectToRoute('home');
        }


        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $message = null;

        if ($error) {
            $message = $error->getMessage();
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $this->toView('lastEmail', $lastUsername);
        $this->toView('loginError', $error);
        $this->toView('loginErrorMessage', $message);

        return $this->renderTemplate('security:login', 'Login');
    }

    public function resetPasswordAction(Request $request)
    {
        if ($this->userIsLoggedIn()) {
            $this->addFlash(
                'info',
                'You are already logged in'
            );
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ResetPassword::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            try {
                $email = new Email($data['email']);
                $userService = $this->get('app.services.users');

                try {
                    $user = $userService->findByEmail($email);

                    $token = $userService->generateAndSavePasswordToken(
                        $user,
                        $this->getApplicationTime()
                    );

                    $webLink = $this->appConfig->getSiteHostName() .
                        $this->generateUrl('set_password') . '?token=' .
                        $token;

                    $emailBody = $this->renderEmail('reset-password', [
                        'name' => $user->getName(),
                        'link' => $webLink,
                    ]);

                    $this->get('app.email.sender')->send(
                        $user->getEmail(),
                        'Reset Password',
                        $emailBody
                    );
                } catch (EntityNotFoundException $e) {
                    // silently catch.
                    // don't inform if the e-mail exists.
                }

                $this->addFlash(
                    'info',
                    'If we recognise your e-mail address you will receive an e-mail shortly.
                    Check your inbox.'
                );
            } catch (ValidationException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (FormInvalidException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        $this->toView('form', $form->createView());

        return $this->renderTemplate('security:reset-password', 'Reset password');
    }

    public function setPasswordAction(Request $request)
    {
        if ($this->userIsLoggedIn()) {
            $this->addFlash(
                'info',
                'You are already logged in'
            );
            return $this->redirectToRoute('home');
        }

        // get a token
        $token = $request->get('token');
        if (!$token) {
            throw new HttpException('Invalid credentials', 403);
        }
        $token = new ResetToken($token);

        $form = $this->createForm(SetPassword::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            try {
                // ensure the passwords are ok
                $password = $data['password'];
                $passwordConfirm = $data['password_confirm'];

                if ($password != $passwordConfirm) {
                    $msg = 'Password fields do not match';

                    $form->get('password')->addError(new FormError($msg));
                    $form->get('password_confirm')->addError(new FormError($msg));
                    throw new FormInvalidException($msg);
                }

                $passwordDigest = new Password($password);
                $this->get('app.services.users')
                    ->resetPassword($token, $passwordDigest, $this->getApplicationTime());

                $this->addFlash(
                    'ok',
                    'Password changed. You can now log in'
                );

                return $this->redirectToRoute('login');
            } catch (ValidationException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (InvalidCredentialsException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (FormInvalidException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        $this->toView('form', $form->createView());

        return $this->renderTemplate('security:set-password', 'Set new password');
    }

    public function welcomeAction(Request $request)
    {
        return $this->renderTemplate('security:welcome', 'Wecome');
    }
}
