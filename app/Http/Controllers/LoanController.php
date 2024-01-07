<?php

namespace App\Http\Controllers;

use App\Interfaces\LoanRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanController extends Controller 
{
    private LoanRepositoryInterface $loanRepository;

    public function __construct(LoanRepositoryInterface $loanRepository) 
    {
        $this->loanRepository = $loanRepository;
    }

    public function index(): JsonResponse 
    {
        return response()->json([
            'data' => $this->loanRepository->getAllLoans()
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $loanDetails = $request->only([
            'amount',
            'user_id',
            'amount',
            'guarantor',
            'loan_date',
            'description',
            'type'
        ]);

        return response()->json(
            [
                'data' => $this->loanRepository->createLoan($loanDetails)
            ],
            Response::HTTP_CREATED
        );
    }

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
