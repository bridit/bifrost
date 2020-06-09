<?php

namespace Bifrost\Validation;

class Factory extends \Illuminate\Validation\Factory
{

  /**
   * Create a new Validator instance.
   *
   * @param  array  $data
   * @param  array  $rules
   * @param  array  $messages
   * @param  array  $customAttributes
   * @return \Illuminate\Validation\Validator
   */
  public function make(array $data, array $rules, array $messages = [], array $customAttributes = [])
  {
    $validator = $this->resolve(
      $data, $rules, $messages, $customAttributes
    );

    // The presence verifier is responsible for checking the unique and exists data
    // for the validator. It is behind an interface so that multiple versions of
    // it may be written besides database. We'll inject it into the validator.
    if (! is_null($this->verifier)) {
      $validator->setPresenceVerifier($this->verifier);
    }

    // Next we'll set the IoC container instance of the validator, which is used to
    // resolve out class based validator extensions. If it is not set then these
    // types of extensions will not be possible on these validation instances.
    if (! is_null($this->container)) {
      $validator->setContainer($this->container);
    }

    $this->addExtensions($validator);

    return $validator;
  }

  /**
   * Resolve a new Validator instance.
   *
   * @param  array  $data
   * @param  array  $rules
   * @param  array  $messages
   * @param  array  $customAttributes
   * @return \Illuminate\Validation\Validator
   */
  protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
  {
    if (is_null($this->resolver)) {
      return new Validator($this->translator, $data, $rules, $messages, $customAttributes);
    }

    return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $customAttributes);
  }

}
