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

use IPub\Phone;

require __DIR__ . '/../../bootstrap.php';

class PhoneInputTest extends Tester\TestCase
{
	/**
	 * @var Phone\Phone
	 */
	private $phone;

	/**
	 * Set up
	 */
	public function setUp()
	{
		parent::setUp();

		$dic = $this->createContainer();

		// Get phone helper from container
		$this->phone = $dic->getByType(Phone\Phone::CLASS_NAME);
	}

	public function testHtmlPartNumber()
	{
		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);
		// Add form control to form
		$form->addComponent($control, 'phone');

		// Set some value
		$control->setCountries(['AUTO']);
		$control->setValue('+420234567890');

		$dq = Tester\DomQuery::fromHtml((string) $control->getControlPart(FormPhone\Controls\Phone::FIELD_NUMBER));

		Assert::true($dq->has('input[value=234567890]'));
	}

	public function testHtmlPartCountry()
	{
		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);
		// Add form control to form
		$form->addComponent($control, 'phone');

		// Set some value
		$control->setCountries(['AUTO']);
		$control->setValue('+420234567890');

		$dq = Tester\DomQuery::fromHtml((string) $control->getControlPart(FormPhone\Controls\Phone::FIELD_COUNTRY));

		Assert::true($dq->has('select option[value=CZ][selected]'));
	}

	/**
	 * @throws Nette\InvalidStateException
	 */
	public function testRegistrationMultiple()
	{
		FormPhone\Controls\Phone::register($this->phone);
		FormPhone\Controls\Phone::register($this->phone);
	}

	public function testRegistration()
	{
		FormPhone\Controls\Phone::register($this->phone);

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
		$control = new FormPhone\Controls\Phone($this->phone);
		// Add form control to form
		$form->addComponent($control, 'phone');

		return $control;
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer()
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addConfig(__DIR__ . '/files/config.neon', $config::NONE);

		return $config->createContainer();
	}
}

\run(new PhoneInputTest());
