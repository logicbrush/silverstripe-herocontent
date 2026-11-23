<?php
/**
 * src/Forms/SingleValueTagField.php
 *
 * @package default
 */


namespace Logicbrush\HeroContent\Forms;

use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\TagField\TagField;

/**
 * Store the selected tag text directly instead of TagField's default record ID.
 */
class SingleValueTagField extends TagField
{

	/**
	 *
	 * @param DataObjectInterface $record
	 */
	public function loadFrom(DataObjectInterface $record) {
		$this->setValue($record->getField($this->getName()));
	}


	/**
	 *
	 * @param DataObjectInterface $record
	 */
	public function saveInto(DataObjectInterface $record) {
		$values = $this->getValueArray();
		$value = $values ? reset($values) : null;

		if (is_array($value)) {
			$value = $value['Value'] ?? null;
		}

		$record->setField($this->getName(), $value);
	}


}
