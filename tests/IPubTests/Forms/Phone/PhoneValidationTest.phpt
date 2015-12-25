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

require __DIR__ . '/../../bootstrap.php';

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
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'BE', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->addAllowedCountry('BE');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with wrong country value
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'NL', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->addAllowedCountry('NL');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple country values, one correct
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'BE', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->setAllowedCountries(['BE', 'NL']);
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with multiple wrong country values
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'DE', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->setAllowedCountries(['DE', 'NL']);
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple wrong country values
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'DE', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->setAllowedCountries(['BE', 'DE', 'NL']);
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());
	}

	public function testValidatePhoneWithDefaultCountryWithType()
	{
		// Validator with correct country value, correct type
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'BE', FormPhone\Controls\Phone::FIELD_NUMBER => '0499123456'],
		]);
		// Set allowed country
		$field->addAllowedCountry('BE');
		// Set allowed phone type
		$field->addAllowedPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with correct country value, wrong type
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'BE', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->addAllowedCountry('BE');
		// Set allowed phone type
		$field->addAllowedPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with wrong country value, correct type
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'NL', FormPhone\Controls\Phone::FIELD_NUMBER => '0499123456'],
		]);
		// Set allowed country
		$field->addAllowedCountry('NL');
		// Set allowed phone type
		$field->addAllowedPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with wrong country value, wrong type
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'NL', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->addAllowedCountry('NL');
		// Set allowed phone type
		$field->addAllowedPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple country values, one correct, correct type
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'BE', FormPhone\Controls\Phone::FIELD_NUMBER => '0499123456'],
		]);
		// Set allowed country
		$field->setAllowedCountries(['BE', 'NL']);
		// Set allowed phone type
		$field->addAllowedPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::false($field->hasErrors());

		// Validator with multiple country values, one correct, wrong type
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'BE', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->setAllowedCountries(['BE', 'NL']);
		// Set allowed phone type
		$field->addAllowedPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple country values, none correct, correct type
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'DE', FormPhone\Controls\Phone::FIELD_NUMBER => '0499123456'],
		]);
		// Set allowed country
		$field->setAllowedCountries(['DE', 'NL']);
		// Set allowed phone type
		$field->addAllowedPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());

		// Validator with multiple country values, none correct, wrong type
		$field = $this->createControl([
			'phone' => [FormPhone\Controls\Phone::FIELD_COUNTRY => 'DE', FormPhone\Controls\Phone::FIELD_NUMBER => '016123456'],
		]);
		// Set allowed country
		$field->setAllowedCountries(['DE', 'NL']);
		// Set allowed phone type
		$field->addAllowedPhoneType('mobile');
		$field
			->addRule(FormPhone\Forms\PhoneValidator::PHONE, 'Invalid phone')
			->validate();

		Assert::true($field->hasErrors());
	}

	/**
	 * @throws \IPub\FormPhone\Exceptions\InvalidArgumentException
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
