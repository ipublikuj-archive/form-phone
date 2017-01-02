<?php
/**
 * PhoneValidator.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:FormPhone!
 * @subpackage     Forms
 * @since          1.0.0
 *
 * @date           12.12.15
 */

declare(strict_types = 1);

namespace IPub\FormPhone\Forms;

use Nette;
use Nette\Forms;

use libphonenumber;
use libphonenumber\PhoneNumberUtil;

use IPub\FormPhone;
use IPub\FormPhone\Controls;
use IPub\FormPhone\Exceptions;

use IPub\Phone;

/**
 * Phone number control form field validator
 *
 * @package        iPublikuj:FormPhone!
 * @subpackage     Forms
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class PhoneValidator extends Phone\Forms\PhoneValidator
{
	/**
	 * Define class name
	 */
	const CLASS_NAME = __CLASS__;

	/**
	 * Define validator calling constant
	 */
	const PHONE = 'IPub\FormPhone\Forms\PhoneValidator::validatePhone';

	/**
	 * @param Forms\IControl $control
	 * @param array|NULL $params
	 *
	 * @return bool
	 *
	 * @throws Exceptions\InvalidArgumentException
	 * @throws Exceptions\NoValidCountryException
	 */
	public static function validatePhone(Forms\IControl $control, array $params = NULL) : bool
	{
		if (!$control instanceof Controls\Phone) {
			throw new Exceptions\InvalidArgumentException('This validator could be used only on phone field. You used it on: "' . get_class($control) . '"');
		}

		// Get form element value
		$value = $control->getValue();

		// Value have to be phone entity
		if ($value instanceof Phone\Entities\Phone) {
			$number = $value->getRawOutput();

			// Get instance of phone number util
			$phoneNumberUtil = PhoneNumberUtil::getInstance();

			// Get list of allowed countries from params
			$allowedCountries = self::determineCountries($control->getAllowedCountries());

			// Get list of allowed phone types
			$allowedTypes = self::determineTypes($control->getAllowedPhoneTypes());

			// Perform validation
			foreach ($allowedCountries as $country) {
				try {
					// For default countries or country field, the following throws NumberParseException if
					// not parsed correctly against the supplied country
					// For automatic detection: tries to discover the country code using from the number itself
					$phoneProto = $phoneNumberUtil->parse($number, $country);

					// For automatic detection, the number should have a country code
					// Check if type is allowed
					if (
						$phoneProto->hasCountryCode() &&
						$allowedTypes === [] ||
						in_array($phoneNumberUtil->getNumberType($phoneProto), $allowedTypes)
					) {
						// Automatic detection:
						if ($country == 'ZZ') {
							// Validate if the international phone number is valid for its contained country
							return $phoneNumberUtil->isValidNumber($phoneProto);
						}

						// Validate number against the specified country. Return only if success
						// If failure, continue loop to next specified country
						if ($phoneNumberUtil->isValidNumberForRegion($phoneProto, $country)) {
							return TRUE;
						}
					}

				} catch (libphonenumber\NumberParseException $ex) {
					// Proceed to default validation error
				}
			}
		}

		// All specified country validations have failed
		return FALSE;
	}
}
