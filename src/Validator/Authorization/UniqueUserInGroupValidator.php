<?php

namespace App\Validator\Authorization;

use App\Entity\Authorization\AuthorizationCard;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueUserInGroupValidator extends ConstraintValidator
{
    /**
     * @param AuthorizationCard $card
     */
    public function validate($card, Constraint $constraint): void
    {
        if (!$card instanceof AuthorizationCard) {
            throw new UnexpectedValueException($card, AuthorizationCard::class);
        }

        if (!$constraint instanceof UniqueUserInGroup) {
            throw new UnexpectedValueException($constraint, ConfirmedPaymentReceipt::class);
        }

        $collectionElements = [];
        foreach ($card->getAuthorizationGroup()->getAuthorizationCards() as $card) {
            $value = $card->getUser()->getUserIdentifier();

            if (\in_array($value, $collectionElements, true)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $value)
                    ->addViolation();

                return;
            }

            $collectionElements[] = $value;
        }
    }
}
