<?php
/**
 * FormPhoneExtension.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        iPublikuj:FormPhone!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           15.12.15
 */

declare(strict_types = 1);

namespace IPub\FormPhone\DI;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;

use IPub;
use IPub\Phone;

/**
 * Form phone control extension container
 *
 * @package        iPublikuj:FormPhone!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class FormPhoneExtension extends DI\CompilerExtension
{
	/**
	 * @param Code\ClassType $class
	 */
	public function afterCompile(Code\ClassType $class)
	{
		parent::afterCompile($class);

		$builder = $this->getContainerBuilder();

		/** @var Code\Method $initialize */
		$initialize = $class->methods['initialize'];
		$initialize->addBody('IPub\FormPhone\Controls\Phone::register($this->getService(?));', [
			$builder->getByType(Phone\Phone::CLASS_NAME)
		]);
	}
}
