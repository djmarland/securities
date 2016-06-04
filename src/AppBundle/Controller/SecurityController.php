<?php
namespace AppBundle\Controller;

use AppBundle\Domain\Exception\FormInvalidException;
use AppBundle\Domain\Exception\ValidationException;
use AppBundle\Domain\ValueObject\Email;
use AppBundle\Form\Security\ResetPassword;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
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