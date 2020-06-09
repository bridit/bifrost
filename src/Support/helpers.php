<?php

if (!function_exists('call_if')) {

  function call_if(bool $condition, \Closure $callable)
  {
    if ($condition) {
      call_user_func($callable);
    }
  }

}

if(!function_exists('to_snake_case')){

    function to_snake_case(string $input)
    {
        return strtolower( preg_replace(
            ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"], ["_$1", "_$1_$2"], lcfirst($input) ) );
    }

}

if(!function_exists('to_camel_case')){

    function to_camel_case(string $input, array $noStrip = []) {
        // non-alpha and non-numeric characters become spaces
        $input = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $input);
        $input = trim($input);
        // uppercase the first character of each word
        $input = ucwords($input);
        $input = str_replace(" ", "", $input);
        $input = lcfirst($input);

        return $input;
    }

}