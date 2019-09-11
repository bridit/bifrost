<?php

namespace Bifrost\Exceptions;

use Throwable;

class JsonApiException extends \Exception
{
  /**
   * A unique identifier for this particular occurrence of the problem.
   * @var null|string
   */
  protected $id = null;

  /**
   * The HTTP status code applicable to this problem, expressed as a string value.
   * @var null|string
   */
  protected $status = null;

  /**
   * An application-specific error code, expressed as a string value.
   * @var null|int
   */
  protected $code = null;

  /**
   * A short, human-readable summary of the problem that SHOULD NOT change from occurrence to occurrence of the problem,
   * except for purposes of localization.
   * @var null|string
   */
  protected $title = null;

  /**
   * A human-readable explanation specific to this occurrence of the problem. Like title, this field’s value can be localized.
   * @var null|string
   */
  protected $detail = null;

  /**
   * An object containing references to the source of the error, optionally including any of the following members:
   * - pointer: a JSON Pointer [RFC6901] to the associated entity in the request document
   * [e.g. "/data" for a primary data object, or "/data/attributes/title" for a specific attribute].
   * - parameter: a string indicating which URI query parameter caused the error.
   * @var null|ErrorSource
   */
  protected $source = null;

  /**
   * A meta object containing non-standard meta-information about the error.
   * @var null|string
   */
  protected $meta = null;

  /**
   * A links object containing the following member:
   * - about: a link that leads to further details about this particular occurrence of the problem.
   *
   * @var null|string
   */
  protected $links = null;

  public function __construct($message = "", $code = 0, Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);

    $this->setStatus($code);
    $this->setTitle($message);
  }

  /**
   * @return string|null
   */
  public function getId(): ?string
  {
    return $this->id;
  }

  /**
   * @param string|null $id
   */
  public function setId(?string $id): void
  {
    $this->id = $id;
  }

  /**
   * @return string|null
   */
  public function getStatus(): ?string
  {
    return $this->status;
  }

  /**
   * @param string|null $status
   */
  public function setStatus(?string $status): void
  {
    $this->status = $status;
  }

  /**
   * @return int|null
   */
  public function getCode(): ?int
  {
    return $this->code;
  }

  /**
   * @param int|null $code
   */
  public function setCode(?int $code): void
  {
    $this->code = $code;
  }

  /**
   * @return string|null
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * @param string|null $title
   */
  public function setTitle(?string $title): void
  {
    $this->title = $title;
  }

  /**
   * @return string|null
   */
  public function getDetail(): ?string
  {
    return $this->detail;
  }

  /**
   * @param string|null $detail
   */
  public function setDetail(?string $detail): void
  {
    $this->detail = $detail;
  }

  /**
   * @return ErrorSource|null
   */
  public function getSource(): ?ErrorSource
  {
    return $this->source;
  }

  /**
   * @param ErrorSource|null $source
   */
  public function setSource(?ErrorSource $source): void
  {
    $this->source = $source;
  }

  /**
   * @return string|null
   */
  public function getMeta(): ?string
  {
    return $this->meta;
  }

  /**
   * @param string|null $meta
   */
  public function setMeta(?string $meta): void
  {
    $this->meta = $meta;
  }

  /**
   * @return string|null
   */
  public function getLinks(): ?string
  {
    return $this->links;
  }

  /**
   * @param string|null $links
   */
  public function setLinks(?string $links): void
  {
    $this->links = $links;
  }

  /**
   * @return array
   */
  public function toArray()
  {
    return array_filter([
      'id' => $this->id,
      'code' => $this->code,
      'status' => $this->status,
      'title' => $this->title,
      'detail' => $this->detail,
      'source' => $this->source,
      'meta' => $this->meta,
      'links' => $this->links,
    ]);
  }
}