<?php

/**
 * This file is part of ResMan library.
 *
 * Copyright (c) 2015 DTForce, s.r.o. (http://www.dtforce.com)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace DTForce\ResManTranslate;

use DTForce\ResManTranslate\Exception\LocaleDoesNotExistException;
use Nette\Localization\ITranslator;
use ReflectionClass;
use ReflectionMethod;


final class Translator implements ITranslator
{

	/**
	 * @var ReflectionClass
	 */
	private $translationsClass;

	/**
	 * @var ReflectionMethod
	 */
	private $getValueMethod;

	/**
	 * @var ReflectionMethod
	 */
	private $hasValueMethod;

	/**
	 * @var ReflectionMethod
	 */
	private $getDefaultVersionMethod;

	/**
	 * @var ReflectionMethod
	 */
	private $isVersionAllowedMethod;

	/**
	 * @var string
	 */
	private $locale;


	/**
	 * @param string $translationsClass
	 * @param string $locale
	 * @throws LocaleDoesNotExistException
	 */
	public function __construct($translationsClass, $locale = null)
	{
		$this->translationsClass = new ReflectionClass($translationsClass);

		if ($locale !== null ) {
			if ( ! $this->getMethodIsVersionAllowed()->invoke(null, $locale)) {
				throw new LocaleDoesNotExistException($locale);
			}
			$this->locale = $locale;
		} else {
			$this->locale = $this->getMethodGetDefaultVersion()->invoke(null);
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function translate($message, $count = null)
	{
		$args = func_get_args();
		if ( ! $this->getMethodHasValue()->invoke(null, $message, $this->locale)) {
			//TODO report missing translations
			return $message;
		}

		$translatedMessage = $this->getMethodGetValue()->invoke(null, $message, $this->locale);
		if (isset($args[2])) {
			$translatedMessage = $this->replacePlaceholders($translatedMessage, $args[2]);
		}
		return $translatedMessage;
	}


	/**
	 * Replaces placeholders in form {{key}} by value as defined in replacements.
	 * @param string $string
	 * @param array $replacements
	 * @return string
	 */
	protected function replacePlaceholders($string, $replacements)
	{
		if ($replacements === null || count($replacements) === 0) {
			return $string;
		}

		foreach ($replacements as $key => $value) {
			$string = str_replace(("{{" . $key . "}}"), $value, $string);
		}
		return $string;
	}


	/**
	 * @return ReflectionMethod
	 */
	private function getMethodGetValue()
	{
		if ($this->getValueMethod === null) {
			$this->getValueMethod = $this->translationsClass->getMethod('getValue');
		}
		return $this->getValueMethod;
	}


	/**
	 * @return ReflectionMethod
	 */
	private function getMethodHasValue()
	{
		if ($this->hasValueMethod === null) {
			$this->hasValueMethod = $this->translationsClass->getMethod('hasValue');
		}
		return $this->hasValueMethod;
	}


	/**
	 * @return ReflectionMethod
	 */
	private function getMethodGetDefaultVersion()
	{
		if ($this->getDefaultVersionMethod === null) {
			$this->getDefaultVersionMethod = $this->translationsClass->getMethod('getDefaultVersion');
		}
		return $this->getDefaultVersionMethod;
	}


	/**
	 * @return ReflectionMethod
	 */
	private function getMethodIsVersionAllowed()
	{
		if ($this->isVersionAllowedMethod === null) {
			$this->isVersionAllowedMethod = $this->translationsClass->getMethod('isVersionAllowed');
		}
		return $this->isVersionAllowedMethod;
	}

}
