<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller 
{
    private PaymentRepositoryInterface $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository) 
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index(): JsonResponse 
    {
        return response()->json([
            'data' => $this->paymentRepository->getAllPayments()
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $paymentDetails = $request->only([
            'loan_id',
            'amount',
            'interest',
            'payment_date',
            'description'
        ]);

        return response()->json(
            [
                'data' => $this->paymentRepository->createPayment($paymentDetails)
            ],
            Response::HTTP_CREATED
        );
    }

    public function show(Request $request): JsonResponse 
    {
        $paymentId = $request->route('id');

        return response()->json([
            'data' => $this->paymentRepository->getPaymentById($paymentId)
        ]);
    }

    public function update(Request $request): JsonResponse 
    {
        $paymentId = $request->route('id');
        $loanDetails = $request->only([
            'loan_id',
            'amount',
            'interest',
            'payment_date',
            'description'
        ]);

        return response()->json([
            'data' => $this->paymentRepository->updatePayment($paymentId, $loanDetails)
        ]);
    }

    public function destroy(Request $request): JsonResponse 
    {
        $paymentId = $request->route('id');
        $this->paymentRepository->deletePayment($paymentId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function total(Request $request): JsonResponse 
    {
        $date = $request->input('payment_date');
        return response()->json([
            'data' => $this->paymentRepository->getTotalAmountPayments($date)
        ]);
    }
    public function payment(Request $request): JsonResponse 
    {
        $loanId = $request->input('id');
        $date = $request->input('payment_date');

        return response()->json([
            'data' => $this->paymentRepository->getPaymentByDate($loanId, $date)
        ]);
    }

    public function remaing(Request $request): JsonResponse 
    {
        $loanId = $request->route('id');

        //return $this->paymentRepository->getRemaingPaymentByLoan($loanId);

        return response()->json([
           'data' => $this->paymentRepository->getRemaingPaymentByLoan($loanId)
        ]);
        
    }
}
