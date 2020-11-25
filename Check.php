<?php

namespace Bifrost\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Check
 * @package Core
 */
class Check
{
  use Macroable;

  /**
   * Is subject equal to given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function eq($subject, $value): bool
  {
    return $subject === $value;
  }

  /**
   * Is subject different from given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function notEq($subject, $value): bool
  {
    return $subject !== $value;
  }

  /**
   * Is subject greater than given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function gt($subject, $value): bool
  {
    return $subject > $value;
  }

  /**
   * Is subject greater or equal than given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function gte($subject, $value): bool
  {
    return $subject >= $value;
  }

  /**
   * Is subject lower than given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function lt($subject, $value): bool
  {
    return $subject > $value;
  }

  /**
   * Is subject lower or equal than given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function lte($subject, $value): bool
  {
    return $subject >= $value;
  }

  /**
   * Subject matches given pattern (case sensitive)?
   *
   * @param $subject
   * @param $pattern
   * @return bool
   */
  public static function like($subject, $pattern): bool
  {
    return Str::like($subject, $pattern);
  }

  /**
   * Subject does not match given pattern (case sensitive)?
   *
   * @param $subject
   * @param $pattern
   * @return bool
   */
  public static function notLike($subject, $pattern): bool
  {
    return !static::like($subject, $pattern);
  }

  /**
   * Subject matches given pattern (case insensitive)?
   *
   * @param $subject
   * @param $pattern
   * @return bool
   */
  public static function ilike($subject, $pattern): bool
  {
    return Str::ilike($subject, $pattern);
  }

  /**
   * Subject does not match given pattern (case insensitive)?
   *
   * @param $subject
   * @param $pattern
   * @return bool
   */
  public static function notIlike($subject, $pattern): bool
  {
    return !static::ilike($subject, $pattern);
  }

  /**
   * Subject is between given values?
   *
   * @param $subject
   * @param $min
   * @param $max
   * @return bool
   */
  public static function between($subject, $min, $max): bool
  {
    return $subject >= $min && $subject <= $max;
  }

  /**
   * Subject is not between given values?
   *
   * @param $subject
   * @param $min
   * @param $max
   * @return bool
   */
  public static function notBetween($subject, $min, $max): bool
  {
    return !static::between($subject, $min, $max);
  }

  /**
   * Is subject present in given iterable value?
   *
   * @param $subject
   * @param string|array|Collection $value
   * @return bool
   */
  public static function in($subject, $value): bool
  {
    if (is_array($value)) {
      return in_array($subject, $value);
    }

    if (is_collection($value)) {
      return $value->contains($subject);
    }

    if (is_string($value) || is_numeric($value)) {
      return stripos((string) $value, (string) $subject) !== false;
    }

    return false;
  }

  /**
   * Is subject not present in given iterable value?
   *
   * @param $subject
   * @param string|array|Collection $value
   * @return bool
   */
  public static function notIn($subject, $value): bool
  {
    return !static::in($subject, $value);
  }

  /**
   * Is subject present in given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function exists($subject, $value): bool
  {
    return static::in($subject, $value);
  }

  /**
   * Is subject not present in given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function notExists($subject, $value): bool
  {
    return !static::in($subject, $value);
  }

  /**
   * Is subject present in given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function contains($subject, $value): bool
  {
    return static::in($subject, $value);
  }

  /**
   * Is subject not present in given value?
   *
   * @param $subject
   * @param $value
   * @return bool
   */
  public static function notContains($subject, $value): bool
  {
    return !static::in($subject, $value);
  }
}
