<?php namespace Wasp\Forms\Validation;

/**
 * The Base Rule class
 *
 * @package Wasp
 * @subpackage Forms
 * @author Dan Cox
 */
class Rule
{
	
	/**
	 * The field value
	 *
	 * @var Mixed
	 */
	protected $value;

	/**
	 * The error message associated with this rule
	 *
	 * @var String
	 */
	protected $message;

	/**
	 * Set up the rule
	 *
	 * @author Dan Cox
	 */
	public function __construct($message = null)
	{
		if (!is_null($message))
		{
			$this->message = $message;
		}
	}

	/**
	 * Sets the value
	 *
	 * @return Rule
	 * @author Dan Cox
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * Sets the error message
	 *
	 * @return Rule
	 * @author Dan Cox
	 */
	public function setMessage($message)
	{	
		$this->message = $message;
		return $this;
	}

	/**
	 * Returns the set message
	 *
	 * @return String
	 * @author Dan Cox
	 */
	public function getMessage()
	{	
		return $this->message;
	}
	

} // END class Rule
