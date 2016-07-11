<?php

namespace SS6\ShopBundle\Form\Front\Cart;

use SS6\ShopBundle\Component\Constraints\ConstraintValue;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class CartFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;

	public function __construct(\SS6\ShopBundle\Model\Cart\Cart $cart) {
		$this->cart = $cart;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
			->add('quantities', FormType::COLLECTION, [
				'allow_add' => true,
				'allow_delete' => true,
				'type' => FormType::INTEGER,
				'constraints' => [
						new Constraints\All([
							'constraints' => [
								new Constraints\NotBlank(['message' => 'Musíte zadat množství']),
								new Constraints\GreaterThan(['value' => 0, 'message' => 'Musíte zadat kladné množství']),
								new Constraints\LessThanOrEqual([
									'value' => ConstraintValue::INTEGER_MAX_VALUE,
									'message' => 'Zadejte prosím platné množství',
								]),
							],
						]),
					],
				])
			->add('submit', FormType::SUBMIT);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'cart_form';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
