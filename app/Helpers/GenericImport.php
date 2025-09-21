<?php

namespace App\Helpers;

use Maatwebsite\Excel\Concerns\ToArray;

class GenericImport implements ToArray
{
	/**
	 * Return the raw data from the spreadsheet as an array.
	 * This satisfies the Maatwebsite Excel ToArray concern.
	 */
	public function array(array $array)
	{
		return $array;
	}
}

