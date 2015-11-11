<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\User;
use AppBundle\Domain\Exception\FormInvalidException;
use AppBundle\Domain\Exception\ValidationException;
use AppBundle\Domain\ValueObject\Email;
use AppBundle\Domain\ValueObject\IDUnset;
use AppBundle\Domain\ValueObject\Key;
use AppBundle\Domain\ValueObject\Password;
use AppBundle\Form\User\Create;
use DateTime;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UsersController extends Controller
{
    public function listAction()
    {
        $perPage = 50;
        $currentPage = $this->getCurrentPage();

        $result = $this->get('app.services.users')
            ->findAndCountLatest($perPage, $currentPage);

        $this->toView('users', $result->getDomainModels());
        $this->toView('total', $result->getTotal());

        $this->setPagination(
            $result->getTotal(),
            $currentPage,
            $perPage
        );

        return $this->renderTemplate('users:list');
    }

    public function showAction(Request $request)
    {
        $key = $request->get('user_key');
        $result = $this->get('app.services.users')
            ->findByKey(new Key($key));

        $user = $result->getDomainModel();
        if (!$user) {
            throw new HttpException(404, 'User ' . $key . ' does not exist.');
        }

        $this->toView('user', $user);
        return $this->renderTemplate('users:show');
    }

    public function changePassword(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    }

    public function newAction(Request $request)
    {
        $form = $this->createForm(new Create());

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            try {
                try {
                    $email = new Email($data['email']);
                } catch (ValidationException $e) {
                    $form->get('email')->addError(new FormError($e->getMessage()));
                    throw $e; // re-throw
                }

                // see if a user with that e-mail address already exists
                $count = $this->get('app.services.users')->countByEmail($email);
                if ($count > 0) {
                    $message = 'A user already exists with this e-mail address';
                    $form->get('email')->addError(new FormError($message));
                    throw new FormInvalidException($message);
                }

                $name = $data['name'];

                $creationTime = new DateTime();
                $temporaryPassword = md5(time());
                $passwordDigest = new Password($temporaryPassword);

                $user = new User(
                    new IDUnset(),
                    $creationTime,
                    $creationTime,
                    $name,
                    $email,
                    (string) $passwordDigest,
                    true // password is already expired
                );

                $this->get('app.services.users')
                    ->createNewUser($user);

                $this->addFlash(
                    'success',
                    $name . ' has been created'
                );

                $body = $this->renderEmail('welcome', [
                    'name' => $name,
                    'password' => $temporaryPassword
                ]);

                $this->get('app.services.email')->send(
                    $email,
                    $this->settings->getApplicationName(),
                    'Welcome',
                    $body
                );

                return $this->redirectToRoute('users_show', [
                    'user_key' => $user->getKey()
                ]);

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

        return $this->renderTemplate('users:new');
    }

    public function firstUserAction(Request $request)
    {
        $count = $this->get('app.services.users')
            ->countAll();

        if ($count > 0) {
            throw new HttpException(404, 'This page no longer exists');
        }

        $form = $this->createForm(new Create());

        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();
            try {
                try {
                    $email = new Email($data['email']);
                } catch (ValidationException $e) {
                    $form->get('email')->addError(new FormError($e->getMessage()));
                    throw $e; // re-throw
                }

                $name = $data['name'];

                $creationTime = new DateTime();
                $temporaryPassword = md5(time());
                $passwordDigest = new Password($temporaryPassword);

                $user = new User(
                    new IDUnset(),
                    $creationTime,
                    $creationTime,
                    $name,
                    $email,
                    (string) $passwordDigest,
                    true // password is already expired
                );

                $this->get('app.services.users')
                    ->createNewUser($user);

                $this->addFlash(
                    'success',
                    $name . ' has been created. Check e-mail for login details'
                );

                $body = $this->renderEmail('welcome', [
                    'name' => $name,
                    'password' => $temporaryPassword
                ]);

                $this->get('app.services.email')->send(
                    $email,
                    $this->settings->getApplicationName(),
                    'Welcome',
                    $body
                );

                return $this->redirectToRoute('home');

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

        return $this->renderTemplate('security:first-user');
    }
}
