<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Registration\NewPasswordFormType;
use SS6\ShopBundle\Form\Front\Registration\RegistrationFormType;
use SS6\ShopBundle\Form\Front\Registration\ResetPasswordFormType;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class RegistrationController extends Controller {

	public function registerAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$userDataFactory = $this->get('ss6.shop.customer.user_data_factory');
		/* @var $userDataFactory \SS6\ShopBundle\Model\Customer\UserDataFactory */

		$form = $this->createForm(new RegistrationFormType());

		try {
			$userData = $userDataFactory->createDefault($domain->getId());

			$form->setData($userData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$customerEditFacade = $this->get('ss6.shop.customer.customer_edit_facade');
				/* @var $customerEditFacade \SS6\ShopBundle\Model\Customer\CustomerEditFacade */

				$userData = $form->getData();
				$userData->domainId = $domain->getId();
				$user = $customerEditFacade->register($userData);

				$this->login($user);

				$flashMessageSender->addSuccessFlash('Byli jste úspěšně zaregistrováni');
				return $this->redirect($this->generateUrl('front_homepage'));
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
			$form->get('email')->addError(new FormError('V databázi se již nachází zákazník s tímto e-mailem'));
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$flashMessageSender->addErrorFlash('Prosím zkontrolujte si správnost vyplnění všech údajů');
		}

		return $this->render('@SS6Shop/Front/Content/Registration/register.html.twig', [
			'form' => $form->createView(),
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	private function login(User $user) {
		$token = new UsernamePasswordToken($user, $user->getPassword(), 'frontend', $user->getRoles());
		$this->get('security.context')->setToken($token);

		// dispatch the login event
		$request = $this->get('request');
		$event = new InteractiveLoginEvent($request, $token);
		$this->get('event_dispatcher')->dispatch('security.interactive_login', $event);
	}

	public function resetPasswordAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$registrationFacade = $this->get('ss6.shop.customer.registration_facade');
		/* @var $registrationFacade \SS6\ShopBundle\Model\Customer\RegistrationFacade */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$em = $this->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$form = $this->createForm(new ResetPasswordFormType());

		$form->handleRequest($request);

		if ($form->isValid()) {
			$formData = $form->getData();
			$email = $formData['email'];

			try {
				$em->beginTransaction();
				$registrationFacade->resetPassword($email, $domain->getId());
				$em->commit();

				$flashMessageSender->addSuccessFlashTwig('Odkaz pro vyresetování hesla byl zaslán na email <strong>{{ email }}</strong>.', [
					'email' => $email,
				]);
				return $this->redirect($this->generateUrl('front_registration_reset_password'));
			} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundByEmailAndDomainException $ex) {
				$flashMessageSender->addErrorFlashTwig(
					'Zákazník s emailovou adresou <strong>{{ email }}</strong> neexistuje.'
					. ' <a href="{{ registrationLink }}">Zaregistrovat</a>', [
						'email' => $ex->getEmail(),
						'registrationLink' => $this->generateUrl('front_registration_register'),
					]);
			} catch (\Exception $ex) {
				$em->rollback();
				throw $ex;
			}
		}

		return $this->render('@SS6Shop/Front/Content/Registration/resetPassword.html.twig', [
			'form' => $form->createView(),
		]);
	}

	public function setNewPasswordAction(Request $request) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$registrationFacade = $this->get('ss6.shop.customer.registration_facade');
		/* @var $registrationFacade \SS6\ShopBundle\Model\Customer\RegistrationFacade */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$em = $this->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$email = $request->query->get('email');
		$hash = $request->query->get('hash');

		if (!$registrationFacade->isResetPasswordHashValid($email, $domain->getId(), $hash)) {
			$flashMessageSender->addErrorFlash('Platnost odkazu pro změnu hesla vypršela.');
			return $this->redirect($this->generateUrl('front_homepage'));
		}

		$form = $this->createForm(new NewPasswordFormType());

		$form->handleRequest($request);

		if ($form->isValid()) {
			$formData = $form->getData();

			$newPassword = $formData['newPassword'];

			try {
				$em->beginTransaction();
				$user = $registrationFacade->setNewPassword($email, $domain->getId(), $hash, $newPassword);
				$this->login($user);
				$em->commit();
			} catch (\SS6\ShopBundle\Model\Customer\Exception\UserNotFoundByEmailAndDomainException $ex) {
				$em->rollback();
				$flashMessageSender->addErrorFlashTwig('Zákazník s emailovou adresou <strong>{{ email }}</strong> neexistuje.'
					. ' <a href="{{ registrationLink }}">Zaregistrovat</a>', [
						'email' => $ex->getEmail(),
						'registrationLink' => $this->generateUrl('front_registration_register'),
					]);
			} catch (\SS6\ShopBundle\Model\Customer\Exception\InvalidResetPasswordHashException $ex) {
				$em->rollback();
				$flashMessageSender->addErrorFlash('Platnost odkazu pro změnu hesla vypršela.');
			} catch (\Exception $ex) {
				$em->rollback();
				throw $ex;
			}

			$flashMessageSender->addSuccessFlash('Heslo bylo úspěšně změněno');
			return $this->redirect($this->generateUrl('front_homepage'));
		}

		return $this->render('@SS6Shop/Front/Content/Registration/setNewPassword.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
