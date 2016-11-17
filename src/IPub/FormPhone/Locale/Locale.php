<?php
/**
 * Locale.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        iPublikuj:FormPhone!
 * @subpackage     Locale
 * @since          1.0.4
 *
 * @date           20.08.16
 */

declare(strict_types = 1);

namespace IPub\FormPhone\Locale;

use Giggsey;

class Locale extends Giggsey\Locale\Locale
{
	/**
	 * @link http://stackoverflow.com/a/10375234/403165
	 *
	 * @param string $countryCode
	 * @param string $languageCode
	 *
	 * @return string|NULL
	 */
	public static function countryCodeToLocale(string $countryCode, string $languageCode = '')
	{
		$locale = 'en-' . $countryCode;
		$localeRegion = locale_get_region($locale);
		$localeLanguage = locale_get_primary_language($locale);
		$localeArray = [
			'language' => $localeLanguage,
			'region'   => $localeRegion,
		];

		if (strtoupper($countryCode) === $localeRegion && ($languageCode === '' || strtolower($languageCode) === $localeLanguage)) {
			return locale_compose($localeArray);
		}

		return NULL;
	}
}
