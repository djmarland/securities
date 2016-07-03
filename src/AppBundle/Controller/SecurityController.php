<?php
namespace AppBundle\Controller;

use AppBundle\Exception\FormInvalidException;
use AppBundle\Form\Security\ResetPassword;
use AppBundle\Form\Security\Register;
use Exception;
use SecuritiesService\Domain\Exception\ValidationException;
use SecuritiesService\Domain\ValueObject\Email;
use SecuritiesService\Domain\ValueObject\Password;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function registerAction(Request $request)
    {
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

                // create the new user and save
                $this->get('app.services.users')
                    ->createNewUser($email, $passwordDigest);


                // log the user in, and redirect to homepage
                $form->addError(new FormError('actually, it is good'));

            } catch (ValidationException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (FormInvalidException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (Exception $e) {
                $this->get('logger')->error($e->getMessage());
                $form->addError(new FormError('There was an error saving the form. Please try again'));
            }
        }

        $this->toView('form', $form->createView());

        return $this->renderTemplate('security:register');
    }

    public function loginAction(Request $request)
    {
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

        return $this->renderTemplate('security:login');
    }

    public function resetPasswordAction(Request $request)
    {
        $form = $this->createForm(new ResetPassword());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            try {
                $email = new Email($data['email']);

                // @todo generate the login token
                // @todo send the e-mail
                // set success message on the current form (not flash)
                $this->addFlash(
                    'success',
                    'If we recognise your e-mail address you will receive an e-mail shortly.
                    Check your inbox.'
                );
            } catch (ValidationException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (FormInvalidException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (Exception $e) {
                $this->get('logger')->error($e->getMessage());
                $form->addError(new FormError('There was an error saving the form. Please try again'));
            }
        }

        $this->toView('form', $form->createView());

        return $this->renderTemplate('security:reset-password');
    }

    public function setPasswordAction(Request $request)
    {

    }
}