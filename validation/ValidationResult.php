<?php

class ValidationResult
{

  public string $validationType;
  public string $validationMessage;

  /**
   * @param string $validationType
   * @param string $validationMessage
   */
  public function __construct(string $validationType, string $validationMessage)
  {
    $this->validationType = $validationType;
    $this->validationMessage = $validationMessage;
  }

  /**
   * @return string
   */
  public function getValidationType(): string
  {
    return $this->validationType;
  }

  /**
   * @param string $validationType
   */
  public function setValidationType(string $validationType): void
  {
    $this->validationType = $validationType;
  }

  /**
   * @return string
   */
  public function getValidationMessage(): string
  {
    return $this->validationMessage;
  }

  /**
   * @param string $validationMessage
   */
  public function setValidationMessage(string $validationMessage): void
  {
    $this->validationMessage = $validationMessage;
  }
}
