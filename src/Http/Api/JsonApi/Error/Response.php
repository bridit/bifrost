<?php

namespace Bifrost\Http\Api\JsonApi\Error;

use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class Response
{

  /**
   * The HTTP status code.
   *
   * @var int
   */
  protected int $status;

  /**
   * The errors on response.
   *
   * @var Error[]
   */
  protected array $errors;

  /**
   * The headers on response.
   *
   * @var array
   */
  protected array $headers;

  /**
   * AbstractResponse constructor.
   * @param array $errors
   * @param array $headers
   */
  public function __construct(array $errors, array $headers = [])
  {
    $this->errors = $errors;
    $this->setHeaders($headers);

    $httpStatusCode = Collection::make($errors)
        ->filter(fn($error) => !blank($error->getStatus()))
        ->groupBy(fn($error) => $error->getStatus())
        ->sortByDesc(fn($item) => count($item))
        ->keys()
        ->first() ?? 500;

    $this->setStatus((int) $httpStatusCode);
  }

  public static function createFromValidationException(ValidationException $exception, array $headers = []): self
  {
    $errors = [];
    $titles = (method_exists($exception->validator, 'getTitles') && is_callable([$exception->validator, 'getTitles']))
      ? $exception->validator->getTitles()
      : [];

    foreach ($exception->errors() as $attribute => $error)
    {
      foreach ($error as $detail)
      {
        $errors[] = Error::create($detail)
          ->setStatus($exception->status)
          ->setTitle(Arr::get($titles, $attribute, $exception->getMessage()))
          ->setSource(new Source('/data/attributes/' . $attribute));
      }
    }

    return new self($errors, $headers);
  }

  public static function createFromException(Throwable $exception, array $headers = []): self
  {
    $debug = Config::get('app.debug', false);
    $code = static::getExceptionCode($exception);
    $statusCode = static::getHttpStatus($exception, $code);
    $detail = $debug === true
      ? $exception->getMessage()
      : HttpFoundationResponse::$statusTexts[$statusCode] ?? HttpFoundationResponse::$statusTexts[500];

    if ($code === $statusCode) {
      $code = null;
    }

    $error = Error::create($detail, $code)
      ->setStatus($statusCode);

    if ($debug === false) {
      return new self([$error], $headers);
    }

    $source = new Source();
    $source->file = $exception->getFile();
    $source->line = $exception->getLine();
    $source->trace = Collection::make($exception->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all();

    $error
      ->setTitle(get_class($exception))
      ->setSource($source);

    return new self([$error], $headers);
  }

  protected static function getExceptionCode(Throwable $exception): ?string
  {
    return !blank($exception->getCode()) && $exception->getCode() !== 0
      ? (string) $exception->getCode()
      : null;
  }

  protected static function getHttpStatus(Throwable $exception, ?string $code): string
  {
    if (method_exists($exception, 'getStatusCode')) {
      return (string) $exception->getStatusCode();
    }

    return is_numeric($code) && $code >= 100 && $code <= 599
      ? (string) $code
      : '500';
  }

  /**
   * Get the HTTP status code.
   *
   * @return  int
   */
  public function getStatus(): int
  {
    return $this->status;
  }

  /**
   * Set the HTTP status code.
   *
   * @param int $status The HTTP status code.
   * @return  self
   */
  public function setStatus(int $status): self
  {
    $this->status = $status;

    return $this;
  }

  /**
   * Get the errors on response.
   *
   * @return Error[]
   */
  public function getErrors(): array
  {
    return $this->errors;
  }

  /**
   * @return array
   */
  public function getHeaders(): array
  {
    return $this->headers;
  }

  /**
   * @param array $headers
   * @return self
   */
  public function setHeaders(array $headers): self
  {
    unset($headers['content-type']);

    $this->headers = array_merge($headers, ['Content-Type' => 'application/vnd.api+json']);

    return $this;
  }

  /**
   * Returns JSON response.
   *
   * @return JsonResponse
   */
  public function json(): JsonResponse
  {
    $errors = array_map(fn($error) => $error->toArray('snake', false), $this->getErrors());

    return new JsonResponse(['errors' => $errors], $this->getStatus(), $this->getHeaders(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  }

}
