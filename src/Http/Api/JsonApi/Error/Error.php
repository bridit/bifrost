<?php

namespace Bifrost\Http\Api\JsonApi\Error;

use Throwable;
use Illuminate\Contracts\Support\Arrayable;
use Bifrost\Support\Concerns\NotEmptyArrayable;

class Error implements Arrayable
{
  use NotEmptyArrayable;

  /**
   * A unique identifier for this particular occurrence of the problem.
   *
   * @var null|string|int
   */
  protected $id;

  /**
   * @var null|Links
   */
  protected ?Links $links;

  /**
   * The HTTP status code applicable to this problem, expressed as a string value.
   *
   * @var null|string
   */
  protected ?string $status;

  /**
   * An application-specific error code, expressed as a string value.
   *
   * @var null|string
   */
  protected ?string $code;

  /**
   * A short, human-readable summary of the problem that SHOULD NOT change from
   * occurrence to occurrence of the problem, except for purposes of localization.
   *
   * @var null|string
   */
  protected ?string $title;

  /**
   * A human-readable explanation specific to this occurrence of the problem.
   * Like title, this field’s value can be localized.
   *
   * @var null|string
   */
  protected ?string $detail;

  /**
   * An object containing references to the source of the error.
   *
   * @var null|Source
   */
  protected ?Source $source;

  /**
   * @param string|null $detail
   * @param string|null $code
   * @return Error
   */
  public static function create(?string $detail = null, ?string $code = null)
  {
    return new self($detail, $code);
  }

  /**
   * @param Throwable $e
   * @return Error
   */
  public static function createFromException(Throwable $e)
  {
    return static::create($e->getMessage(), (string) $e->getCode());
  }

  /**
   * Error constructor.
   * @param string|null $detail
   * @param string|null $code
   */
  public function __construct(?string $detail = null, ?string $code = null)
  {
    $this->detail = $detail;
    $this->code = $code;
    $this->id = null;
    $this->links = null;
    $this->status = null;
    $this->title = null;
  }

  /**
   * Get a unique identifier for this particular occurrence of the problem.
   *
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set a unique identifier for this particular occurrence of the problem.
   *
   * @param string $id
   * @return self
   */
  public function setId(string $id): self
  {
    $this->id = $id;

    return $this;
  }

  /**
   * Get the value of links.
   *
   * @return Links
   */
  public function getLinks()
  {
    return $this->links;
  }

  /**
   * Set the value of links.
   *
   * @param Links $links
   * @return self
   */
  public function setLinks(Links $links): self
  {
    $this->links = $links;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus(): ?string
  {
    return $this->status;
  }

  /**
   * Set the HTTP status code applicable to this problem, expressed as a string value.
   *
   * @param string $status
   * @return self
   */
  public function setStatus(string $status): self
  {
    $this->status = $status;

    return $this;
  }

  /**
   * Get an application-specific error code, expressed as a string value.
   *
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set an application-specific error code, expressed as a string value.
   *
   * @param string $code
   * @return self
   */
  public function setCode(string $code): self
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get occurrence to occurrence of the problem, except for purposes of localization.
   *
   * @return string
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * Set occurrence to occurrence of the problem, except for purposes of localization.
   *
   * @param string $title
   * @return self
   */
  public function setTitle(string $title): self
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get like title, this field’s value can be localized.
   *
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }

  /**
   * Set like title, this field’s value can be localized.
   *
   * @param string $detail
   * @return self
   */
  public function setDetail(string $detail): self
  {
    $this->detail = $detail;

    return $this;
  }

  /**
   * Get an object containing references to the source of the error.
   *
   * @return Source
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Set an object containing references to the source of the error.
   *
   * @param Source $source
   * @return self
   */
  public function setSource(Source $source): self
  {
    $this->source = $source;

    return $this;
  }

}
