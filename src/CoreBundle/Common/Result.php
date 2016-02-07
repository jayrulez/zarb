<?php

namespace CoreBundle\Common;

class Result
{
	private $_errors = [];
	private $_data = null;

	public function addError($errorMessage)
	{
		$this->_errors[] = $errorMessage;
		
		return $this;
	}

	public function clearErrors()
	{
		$this->_errors = [];

		return $this;
	}

	public function setData($data)
	{
		$this->_data = $data;

		return $this;
	}

	private function getIsSuccessful()
	{
		return empty($this->_errors);
	}

	private function getData()
	{
		return $this->getIsSuccessful() ? $this->_data : $this->_errors;
	}

	public function toArray()
	{
		return [
			"success"=> $this->getIsSuccessful(),
			"data"=> $this->getData()
		];
	}
}