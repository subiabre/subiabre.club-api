<?php

namespace App\Validator\Authorization;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"CLASS", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class UniqueUserInGroup extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The user "{{ value }}" already has an AuthorizationCard in this AuthorizationList.';

    public function __construct()
    {
        parent::__construct();
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
