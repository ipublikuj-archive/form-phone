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
	 * @return array[]|array
	 */
	public function dataValidPhoneNumbers()
	{
		return [
			['+1-734-555-1212', '+17345551212'],
			['+420234567890', '+420234567890'],
			['234 567 890', '+420234567890'],
			['+420.234.567.890', '+420234567890'],
			['+420-234-567-890', '+420234567890'],
			['00420234567890', '+420234567890'],
			['420234567890', '+420234567890'],
			[420234567890, '+420234567890'],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataInvalidPhoneNumbers()
	{
		return [
			['foo'],
			['123'],
			[123],
			['+1@800@692@7753'],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataEmptyPhoneNumbers()
	{
		return [
			[NULL, NULL],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataAllowedCountries()
	{
		return [
			[['CZ', 'SK'], ['CZ', 'SK']],
			[['US'], ['US']],
		];
	}

	/**
	 * @return array[]|array
	 */
	public function dataInvalidAllowedCountries()
	{
		return [
			[['CZ', 'SK', 'XY'], ['CZ', 'SK', 'XY']],
			[['US', 'XY'], ['US', 'XY']],
			[['XY'], ['XY']],
		];
	}

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

	/**
	 * @dataProvider dataValidPhoneNumbers
	 *
	 * @param string
	 * @param string
	 */
	public function testValidPhoneNumbers($input, $expected)
	{
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);
		$control->setAllowedCountries(['CZ', 'US']);
		$control->setValue($input);

		Assert::type('IPub\Phone\Entities\Phone', $control->getValue());
		Assert::equal($expected, $control->getValue()->getRawOutput());
	}

	/**
	 * @dataProvider dataInvalidPhoneNumbers
	 *
	 * @param string
	 */
	public function testInvalidPhoneNumbers($input)
	{
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);
		$control->setAllowedCountries(['CZ', 'US']);

		Assert::exception(function() use ($control, $input) {
			$control->setValue($input);
		}, 'IPub\FormPhone\Exceptions\InvalidArgumentException');
	}

	/**
	 * @dataProvider dataEmptyPhoneNumbers
	 *
	 * @param string
	 * @param string
	 */
	public function testEmptyPhoneNumbers($input, $expected)
	{
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);
		$control->setAllowedCountries(['CZ', 'US']);
		$control->setValue($input);

		Assert::equal($expected, $control->getValue());
	}

	/**
	 * @dataProvider dataAllowedCountries
	 *
	 * @param string
	 * @param string
	 */
	public function testSetAllowedCountries($input, $expected)
	{
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);
		$control->setAllowedCountries($input);

		Assert::equal($expected, $control->getAllowedCountries());
	}

	/**
	 * @dataProvider dataInvalidAllowedCountries
	 *
	 * @param string
	 * @param string
	 */
	public function testSetInvalidAllowedCountries($input, $expected)
	{
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);

		Assert::exception(function() use ($control, $input) {
			$control->setAllowedCountries($input);
		}, 'IPub\FormPhone\Exceptions\NoValidCountryException');
	}


	public function testDefaultCountry()
	{
		// Create form control
		$control = $this->createControl();

		$dq = Tester\DomQuery::fromHtml((string) $control->getControlPart(FormPhone\Controls\Phone::FIELD_COUNTRY));
		Assert::false($dq->has('select option[selected]'));

		// Define default country
		$control->setDefaultCountry('CZ');

		$dq = Tester\DomQuery::fromHtml((string) $control->getControlPart(FormPhone\Controls\Phone::FIELD_COUNTRY));

		Assert::true($dq->has('select option[selected]'));
		Assert::true($dq->has('select option[value=CZ][selected]'));

		// Define default country
		$control->setDefaultCountry(NULL);

		$dq = Tester\DomQuery::fromHtml((string) $control->getControlPart(FormPhone\Controls\Phone::FIELD_COUNTRY));
		Assert::false($dq->has('select option[selected]'));
	}

	public function testInvalidDefaultCountry()
	{
		// Create form control
		$control = $this->createControl();

		Assert::exception(function() use ($control) {
			$control->setDefaultCountry('xy');
		}, 'IPub\FormPhone\Exceptions\NoValidCountryException');

		Assert::exception(function() use ($control) {
			$control->setDefaultCountry('CZE');
		}, 'IPub\FormPhone\Exceptions\NoValidCountryException');
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
		$control->setValue('+420234567890');

		$dq = Tester\DomQuery::fromHtml((string) $control->getControlPart(FormPhone\Controls\Phone::FIELD_COUNTRY));

		Assert::true($dq->has('select option[value=CZ][selected]'));
	}

	public function testHtml()
	{
		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);
		// Add form control to form
		$form->addComponent($control, 'phone');

		// Set some value
		$control->setValue('+420234567890');

		$dq = Tester\DomQuery::fromHtml((string) $control->getControl());

		Assert::true($dq->has('input[value=234567890]'));
		Assert::true($dq->has('select option[value=CZ][selected]'));
	}

	public function testHtmlPartLabel()
	{
		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new FormPhone\Controls\Phone($this->phone);
		// Add form control to form
		$form->addComponent($control, 'phone');

		Assert::null($control->getLabelPart());
	}

	public function testLoadHttpDataEmpty()
	{
		// Create form control
		$control = $this->createControl();

		Assert::false($control->isFilled());
		Assert::null($control->getValue());
	}

	public function testLoadHttpDataValid()
	{
		// Create form control
		$control = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'CZ', FormPhone\Controls\Phone::FIELD_NUMBER => '234567890'],
		]);

		Assert::type('IPub\Phone\Entities\Phone', $control->getValue());
		Assert::equal('+420234567890', $control->getValue()->getRawOutput());
	}

	public function testLoadHttpDataInvalid()
	{
		// Create form control
		$control = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => NULL, FormPhone\Controls\Phone::FIELD_NUMBER => '123'],
		]);

		Assert::false($control->isFilled());
		Assert::null($control->getValue());
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
	 * @return FormPhone\Controls\Phone
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
