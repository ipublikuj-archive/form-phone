<?php
/**
 * Test: IPub\Forms\PhoneInput
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:FormPhone!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           19.12.15
 */

namespace IPubTests\Forms\Phone;

use Nette;
use Nette\Forms;

use Tester;
use Tester\Assert;

use IPub;
use IPub\FormPhone;

require __DIR__ . '/../../bootstrap.php';

class PhoneInputTest extends Tester\TestCase
{
	public function testHtml()
	{

	}

	/**
	 * @throws Nette\InvalidStateException
	 */
	public function testRegistrationMultiple()
	{
		FormPhone\Controls\Phone::register();
		FormPhone\Controls\Phone::register();
	}

	public function testRegistration()
	{
		FormPhone\Controls\Phone::register();

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = $form->addPhone('phone', 'Phone number');

		Assert::type('IPub\FormPhone\Controls\Phone', $control);
		Assert::equal('phone', $control->getName());
		Assert::equal('Phone number', $control->caption);
		Assert::same($form, $control->getForm());
	}

	/**
	 * @param array $data
	 *
	 * @return FormPhone\Controls\Slug
	 */
	private function createControl($data = [])
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_FILES = [];
		$_POST = $data;

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new FormPhone\Controls\Phone;
		// Add form control to form
		$form->addComponent($control, 'phone');

		return $control;
	}
}

\run(new PhoneInputTest());
