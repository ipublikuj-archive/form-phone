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
 * @date           20.12.15
 */

namespace IPubTests\Forms\Phone;

use Nette;
use Nette\Forms;

use Tester;
use Tester\Assert;

use IPub;
use IPub\FormPhone;

use IPub\Phone;

require __DIR__ . '/../bootstrap.php';

/**
 * Phone number form validation tests
 *
 * @package        iPublikuj:Phone!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class PhoneValidationTest extends Tester\TestCase
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

	public function testValidatePhoneWithDefaultCountryWithoutType()
	{
		// Validator with correct country value
		$field = $this->createControl();
		// Set allowed country
		$field->addCountry('BE');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with wrong country value
		$field = $this->createControl();
		// Set allowed country
		$field->addCountry('NL');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple country values, one correct
		$field = $this->createControl();
		// Set allowed country
		$field->setCountries(['BE', 'NL']);
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with multiple country values, value correct for second country in list
		$field = $this->createControl();
		// Set allowed country
		$field->setCountries(['BE', 'NL']);
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with multiple wrong country values
		$field = $this->createControl();
		// Set allowed country
		$field->setCountries(['DE', 'NL']);
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::true($field->hasErrors());
	}

	public function testValidatePhoneWithDefaultCountryWithType()
	{
		// Validator with correct country value, correct type
		$field = $this->createControl();
		// Set allowed country
		$field->addCondition('BE');
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('0499123456')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with correct country value, wrong type
		$field = $this->createControl();
		// Set allowed country
		$field->addCondition('BE');
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with wrong country value, correct type
		$field = $this->createControl();
		// Set allowed country
		$field->addCondition('NL');
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('0499123456')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with wrong country value, wrong type
		$field = $this->createControl();
		// Set allowed country
		$field->addCondition('NL');
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple country values, one correct, correct type
		$field = $this->createControl();
		// Set allowed country
		$field->setCountries(['BE', 'NL']);
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('0499123456')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with multiple country values, one correct, wrong type
		$field = $this->createControl();
		// Set allowed country
		$field->setCountries(['BE', 'NL']);
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple country values, none correct, correct type
		$field = $this->createControl();
		// Set allowed country
		$field->setCountries(['DE', 'NL']);
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('0499123456')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple country values, none correct, wrong type
		$field = $this->createControl();
		// Set allowed country
		$field->setCountries(['DE', 'NL']);
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		Assert::true($field->hasErrors());
	}

	public function testValidatePhoneAutomaticDetectionFromInternationalInput()
	{
		// Validator with correct international input
		$field = $this->createControl();
		// Set allowed country
		$field->addCountry('AUTO');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('+3216123456')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with wrong international input
		$field = $this->createControl();
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('003216123456')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with wrong international input
		$field = $this->createControl();
		// Set allowed country
		$field->addCountry('AUTO');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('+321456')
			->validate();

		Assert::true($field->hasErrors());
	}

	/**
	 * @throws \IPub\Phone\Exceptions\NoValidCountryException
	 */
	public function testValidatePhoneNoDefaultCountryNoCountryField()
	{
		// Validator with no country field or given country
		$field = $this->createControl();
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		// Validator with no country field or given country, wrong type
		$field = $this->createControl();
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();

		// Validator with no country field or given country, correct type
		$field = $this->createControl();
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('0499123456')
			->validate();

		// Validator with no country field or given country, correct type, faulty parameter
		$field = $this->createControl();
		// Set allowed country
		$field->addCountry('AUTO');
		// Set allowed phone type
		$field->addPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('0499123456')
			->validate();
	}

	/**
	 * @throws \IPub\Phone\Exceptions\InvalidArgumentException
	 */
	public function testValidatorOnWrongControl()
	{
		// Validator with given country assigned to wrong control type
		$field = $this->createInvalidControl();
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->setValue('016123456')
			->validate();
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
	 * @param array $data
	 *
	 * @return Forms\Controls\SelectBox
	 */
	private function createInvalidControl($data = [])
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_FILES = [];
		$_POST = $data;

		// Create form
		$form = new Forms\Form;
		// Create form control
		$control = new Forms\Controls\TextArea;
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

\run(new PhoneValidationTest());
