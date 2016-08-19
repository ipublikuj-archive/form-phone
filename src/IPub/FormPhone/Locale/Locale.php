<?php

namespace IPub\FormPhone\Locale;

use Giggsey;

class Locale extends Giggsey\Locale\Locale
{
	/**
	 * @link http://stackoverflow.com/a/10375234/403165
	 * @param string $countryCode
	 * @param string $languageCode
	 *
	 * @return string
	 */
	public static function countryCodeToLocale($countryCode, $languageCode = '')
	{
		$locale = 'en-' . $countryCode;
		$localeRegion = locale_get_region($locale);
		$localeLanguage = locale_get_primary_language($locale);
		$localeArray= [
			'language' => $localeLanguage,
			'region' => $localeRegion,
		];

		if (strtoupper($countryCode) === $localeRegion && ($languageCode === '' || strtolower($languageCode) === $localeLanguage)) {
			return locale_compose($localeArray);
		}

		return NULL;
	}
}
