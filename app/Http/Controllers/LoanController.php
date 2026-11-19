<?php

namespace App\Http\Controllers;

use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Interfaces\GroupRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
class LoanController extends Controller 
{
    private LoanRepositoryInterface $loanRepository;
    private PaymentRepositoryInterface $paymentRepository;
    private GroupRepositoryInterface $groupRepository;

    public function __construct(
        LoanRepositoryInterface $loanRepository,
        PaymentRepositoryInterface $paymentRepository,
        GroupRepositoryInterface $groupRepository
    ) {
        $this->loanRepository = $loanRepository;
        $this->paymentRepository = $paymentRepository;
        $this->groupRepository = $groupRepository;
    }

    public function index(): JsonResponse 
    {
        return response()->json([
            'data' => $this->loanRepository->getAllLoans()
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $loanDetails = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'user_request_id' => 'nullable|uuid|exists:users,id',
            'group_id' => 'required|uuid|exists:groups,id',
            'group_cycle_id' => [
                'nullable',
                'uuid',
                \Illuminate\Validation\Rule::exists('group_cycles', 'id')
                    ->where('group_id', $request->input('group_id')),
            ],
            'interest_rate_id' => 'required|uuid|exists:interest_rates,id',
            'guarantor_id' => 'nullable|uuid|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'phone_number' => 'required|string',
            'type' => 'required|string',
            'payment_date' => 'required|date',
            'status' => 'required|string',
            'transaction_reference' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        abort_unless($this->groupRepository->canManageGroup($request->user(), $loanDetails['group_id']), 403);
        $this->assertGroupMember($loanDetails['user_id'], $loanDetails['group_id']);
        $loanDetails['trace_id'] = $request->user()->id;

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
        $loan = $this->loanRepository->getLoanById($loanId);
        abort_unless($this->groupRepository->canManageGroup($request->user(), $loan->group_id), 403);
        $loanDetails = $request->validate([
            'user_id' => 'sometimes|uuid|exists:users,id',
            'amount' => 'sometimes|numeric|min:1',
            'guarantor_id' => 'sometimes|nullable|uuid|exists:users,id',
            'payment_date' => 'sometimes|date',
            'status' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'type' => 'sometimes|string',
        ]);
        if (isset($loanDetails['user_id'])) {
            $this->assertGroupMember($loanDetails['user_id'], $loan->group_id);
        }

        return response()->json([
            'data' => $this->loanRepository->updateLoan($loanId, $loanDetails)
        ]);
    }

    public function destroy(Request $request): JsonResponse 
    {
        $loanId = $request->route('id');
        $loan = $this->loanRepository->getLoanById($loanId);
        abort_unless($this->groupRepository->canManageGroup($request->user(), $loan->group_id), 403);
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

    private function assertGroupMember(string $userId, string $groupId): void
    {
        $isMember = \Illuminate\Support\Facades\DB::table('group_users')
            ->where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', \App\Enums\Status::ACTIVE->value)
            ->exists();

        abort_unless($isMember, 422, 'O utilizador não pertence ao grupo seleccionado.');
    }
}
