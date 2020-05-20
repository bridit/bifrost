<?php

namespace Bifrost\Exceptions;

use Throwable;
use Exception;
use Bifrost\Http\Api\JsonApi\Error\Error;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class JsonApiException extends Exception implements Throwable, HttpExceptionInterface
{

  protected Error $error;

  /**
   * @param string|null $message
   * @param string|null $code
   * @param Throwable|null $previous
   * @return static
   */
  public static function create(?string $message = null, ?string $code = null, ?Throwable $previous = null): self
  {
    return new self($message, $code, $previous);
  }

  /**
   * JsonApiException constructor.
   * @param string|null $message
   * @param string|null $code
   * @param Throwable|null $previous
   */
  public function __construct(?string $message = null, ?string $code = null, ?Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);

    $this->error = new Error();
    $this->error->setDetail($message);
    $this->error->setCode($code);
  }

  /**
   * @return Error
   */
  public function getError(): Error
  {
    return $this->error;
  }

  public function getStatusCode()
  {
    return $this->error->getStatus();
  }

  public function getHeaders()
  {
    return ['Content-Type' => 'application/vnd.api+json'];
  }
}
