<?php

namespace App\Controller;

use App\Entity\PaymentOperation;
use App\Service\PaymentValidatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentValidatorController extends AbstractController
{
    /**
     * @Route("/api/payment/validate", name="payment_validator", methods={"POST"})
     */
    public function index(
        Request $request,
        ValidatorInterface $validator,
        PaymentValidatorService $paymentValidatorService
    ): Response
    {
        $constraints = new Assert\Collection([
            'username' => [
                new Assert\Required(),
                new Assert\NotBlank(),
                new Assert\Length(['min' => 3, 'max' => 255]),
                new Assert\Type('string')
            ],
            'amount' => [
                new Assert\Required(),
                new Assert\NotNull(),
                new Assert\Positive()
            ]
        ]);
        $errors = $validator->validate($request->request->all(), $constraints);

        if (count($errors) > 0) {
            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[] = ['field' => $error->getPropertyPath(), 'message' => $error->getMessage()];
            }

            return $this->json(['errors' => $errorMessages], 400);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $operation = new PaymentOperation();
        $operation->setUsername($request->request->get('username'));
        $operation->setAmount($request->request->get('amount'));
        $operation->setDate();

        $isOperationValid = $paymentValidatorService->validate($operation);

        if ($isOperationValid) {
            $entityManager->persist($operation);
            $entityManager->flush();

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false], 400);
    }
}
