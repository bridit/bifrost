<?php

if (!function_exists('call_if')) {

  /**
   * Call function only if condition is true
   *
   * @param bool $condition
   * @param Closure $callable
   */
  function call_if(bool $condition, \Closure $callable)
  {
    if ($condition) {
      call_user_func($callable);
    }
  }

}

if (!function_exists('like')) {

  /**
   * Similar to SQL LIKE function.
   *
   * @param string|null $subject
   * @param string $pattern
   * @return bool
   */
  function like(?string $subject, string $pattern)
  {
    $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));

    return (bool)preg_match("/^{$pattern}$/", $subject);
  }

}

if (!function_exists('ilike')) {

  /**
   * Similar to SQL ILIKE function (case insensitive LIKE).
   *
   * @param string|null $subject
   * @param string $pattern
   * @return bool
   */
  function ilike(?string $subject, string $pattern)
  {
    $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));

    return (bool)preg_match("/^{$pattern}$/i", $subject);
  }

}
