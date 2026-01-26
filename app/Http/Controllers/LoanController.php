<?php

namespace App\Http\Controllers;

use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
class LoanController extends Controller 
{
    private LoanRepositoryInterface $loanRepository;
    private PaymentRepositoryInterface $paymentRepository;

    public function __construct(LoanRepositoryInterface $loanRepository, PaymentRepositoryInterface $paymentRepository) 
    {
        $this->loanRepository = $loanRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function index(): JsonResponse 
    {
        return response()->json([
            'data' => $this->loanRepository->getAllLoans()
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        // $loanDetails = $request->only([
        //     'amount',
        //     'user_id',
        //     'amount',
        //     'guarantor',
        //     'loan_date',
        //     'description',
        //     'type'
        // ]);

         // Pega todos os campos do request que são relevantes
        $loanDetails = $request->only([
            'user_id',
            'user_request_id',
            'group_id',
            'group_cycle_id',
            'interest_rate_id',
            'guarantor_id',
            'trace_id',
            'amount',
            'phone_number',
            'type',
            'payment_date',
            'status',
            'transaction_reference',
            'description'
        ]);

          // DEBUG: mostra no console (terminal do Laravel)
        Log::info('2 Loan request data:', $loanDetails);

        // Outra opção para ver direto no browser/console do Postman
        // dd($loanDetails); // <- isto interrompe a execução e imprime o array

        $loan = $this->loanRepository->createLoan($loanDetails);

        return response()->json(
            [
                'data' => $loan
            ],
            Response::HTTP_CREATED
        );



        // return response()->json(
        //     [
        //         'data' => $this->loanRepository->createLoan($loanDetails)
        //     ],
        //     Response::HTTP_CREATED
        // );


    }
//new things
//Simulacao de alguns metodos
public function simulate(Request $request)
{
     $request->validate([
            'amount' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0',
        ]);

    return response()->json([
        'total' => $this->loanRepository->calculateTotalAmount(
            $request->amount,
            $request->interest_rate
        )
    ]);
}



public function remainingAmount(string $loanId)
{
    Log::info('Loan remaining amount request received', [
        'loan_id' => $loanId,
        'endpoint' => 'GET /api/loans/{loanId}/remaining'
    ]);

    $remaining = $this->loanRepository->getRemainingAmount($loanId);

    Log::info('Loan remaining amount calculated', [
        'loan_id' => $loanId,
        'remaining' => $remaining
    ]);

    return response()->json([
        'loan_id'   => $loanId,
        'remaining' => $remaining
    ]);
}


public function pay(Request $request, $loanId)
{
     $request->validate([
         'amount' => 'required|numeric|min:1',
     ]);

    if (!$this->loanRepository->canAcceptPayment($loanId, $request->amount)) {
        return response()->json(['error' => 'Pagamento excede o valor em dívida'], 422);
    }

    $this->paymentRepository->createPayment($loanId, $request->amount);

    $this->loanRepository->updateLoanStatusIfPaid($loanId);

    return response()->json(['message' => 'Pagamento registado com sucesso']);
}

////////////////



    public function show(Request $request): JsonResponse 
    {
        $loanId = $request->route('id');

        return response()->json([
            'data' => $this->loanRepository->getLoanById($loanId)
        ]);
    }

    public function update(Request $request): JsonResponse 
    {
        $loanId = $request->route('id');
        $loanDetails = $request->only([
            'user_id',
            'amount',
            'guarantor',
            'loan_date',
            'status',
            'description',
            'type'
        ]);

        return response()->json([
            'data' => $this->loanRepository->updateLoan($loanId, $loanDetails)
        ]);
    }

    public function destroy(Request $request): JsonResponse 
    {
        $loanId = $request->route('id');
        $this->loanRepository->deleteLoan($loanId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function total(Request $request): JsonResponse 
    {
        $date = $request->input('loan_date');
        return response()->json([
            'data' => $this->loanRepository->getTotalAmountLoans($date)
        ]);
    }
    public function loan(Request $request): JsonResponse 
    {
        $userId = $request->input('id');
        $date = $request->input('loan_date');

        return response()->json([
            'data' => $this->loanRepository->getLoanByDate($userId, $date)
        ]);
    }

    public function topay(): JsonResponse 
    {
        return response()->json([
            'data' => $this->loanRepository->getLoansToPay()
        ]);
    }
}
