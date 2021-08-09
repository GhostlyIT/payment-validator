<?php

namespace App\Service;

use App\Entity\PaymentOperation;
use App\Repository\Interfaces\BlackListRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PaymentValidatorService
{
    private BlackListRepositoryInterface $blackListRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(BlackListRepositoryInterface $blackListRepository, EntityManagerInterface $entityManager)
    {
        $this->blackListRepository = $blackListRepository;
        $this->entityManager = $entityManager;
    }

    public function validate(PaymentOperation $operation): bool
    {
        return $this->isNameIsNotInBlackList($operation->getUsername())
            && $this->isAmountLessOrEquals($operation->getAmount(), 100000)
            && $this->isLastMonthOperationsSumLessThen(1000000, $operation->getUsername());
    }

    private function isNameIsNotInBlackList(string $name): bool
    {
        return !in_array($name, $this->blackListRepository->getAll());
    }

    private function isAmountLessOrEquals(float $amount, float $maxAllowedAmount): bool
    {
        return $amount <= $maxAllowedAmount;
    }

    private function isLastMonthOperationsSumLessThen(float $maxAllowedSum, string $username): bool
    {
        $sum = $this->entityManager->getRepository(PaymentOperation::class)->getAmountForLastMonthByUsername($username);

        if (!$sum) $sum = 0;

        return $sum < $maxAllowedSum;
    }
}