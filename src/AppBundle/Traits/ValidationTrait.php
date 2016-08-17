<?php

namespace AppBundle\Traits;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ValidationTrait
 * @package AppBundle\Traits
 * Used in entities to validate them.
 */
trait ValidationTrait
{
    private $errors;

    public function isValid(ValidatorInterface $validator, $group)
    {
        $errors = $validator->validate($this, null, $group);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errors[$error->getPropertyPath()] = $error->getMessage();
            }

            return false;
        }

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}