<?php

namespace App\Services;

use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use Exception;

class LoanService
{
    private LoanRepositoryInterface $loanRepository;
    private PaymentRepositoryInterface $paymentRepository;

    public function __construct(
        LoanRepositoryInterface $loanRepository,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->loanRepository = $loanRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Calcula o total do empréstimo com juros
     */
    public function calculateTotal(float $amount, float $interestRate): float
    {
        return $amount + ($amount * $interestRate / 100);
    }

    /**
     * Retorna quanto ainda falta pagar
     */
    public function getRemainingAmount(int $loanId): float
    {
        $loan = $this->loanRepository->getLoanById($loanId);

        $paid = $this->paymentRepository
            ->getPaymentAmounByLoan($loanId)
            ->total_paid ?? 0;

        return $loan->amount - $paid;
    }

    /**
     * Verifica se um pagamento é válido
     */
    public function canAcceptPayment(int $loanId, float $amount): bool
    {
        return $amount <= $this->getRemainingAmount($loanId);
    }

    /**
     * Atualiza o estado do empréstimo se estiver pago
     */
    public function updateLoanStatusIfPaid(int $loanId): void
    {
        $remaining = $this->getRemainingAmount($loanId);

        if ($remaining <= 0) {
            $this->loanRepository->updateLoan($loanId, [
                'status' => 'paid'
            ]);
        }
    }
}