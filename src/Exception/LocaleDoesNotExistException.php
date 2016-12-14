<?php

/**
 * This file is part of ResMan library.
 *
 * Copyright (c) 2015 DTForce, s.r.o. (http://www.dtforce.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */


namespace DTForce\ResManTranslate\Exception;

use Exception;


final class LocaleDoesNotExistException extends Exception
{

	/**
	 * @var string
	 */
	private $localeName;


	/**
	 * @param string $localeName
	 */
	public function __construct($localeName)
	{
		$this->localeName = $localeName;
		parent::__construct(sprintf("Locale (%s) does not exist.", $this->localeName));
	}


	/**
	 * @return string
	 */
	public function getLocaleName()
	{
		return $this->localeName;
	}

}
