<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Interfaces\GroupRepositoryInterface;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller 
{
    private PaymentRepositoryInterface $paymentRepository;
    private GroupRepositoryInterface $groupRepository;
    private LoanRepositoryInterface $loanRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        GroupRepositoryInterface $groupRepository,
        LoanRepositoryInterface $loanRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->groupRepository = $groupRepository;
        $this->loanRepository = $loanRepository;
    }

    public function index(): JsonResponse 
    {
        return response()->json([
            'data' => $this->paymentRepository->getAllPayments()
        ]);
    }
    public function store(Request $request): JsonResponse 
    {
        if ($loanId = $request->route('loan')) {
            $request->merge(['loan_id' => $loanId]);
        }

        $paymentDetails = $request->validate([
            'loan_id' => 'required|uuid|exists:loans,id',
            'user_id' => 'required|uuid|exists:users,id',
            'group_id' => 'required|uuid|exists:groups,id',
            'group_cycle_id' => [
                'required',
                'uuid',
                \Illuminate\Validation\Rule::exists('group_cycles', 'id')
                    ->where('group_id', $request->input('group_id')),
            ],
            'wallet_code' => 'required|string',
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'interest' => 'required|numeric|min:0',
            'payment_date' => 'sometimes|date',
            'transaction_reference' => 'required|string',
            'description' => 'nullable|string',
        ]);
        abort_unless($this->groupRepository->canManageGroup($request->user(), $paymentDetails['group_id']), 403);

        $loan = $this->loanRepository->getLoanById($paymentDetails['loan_id']);
        abort_unless(
            $loan->group_id === $paymentDetails['group_id']
                && $loan->user_id === $paymentDetails['user_id'],
            422,
            'Os dados do pagamento não correspondem ao empréstimo.'
        );
        $this->assertGroupMember($paymentDetails['user_id'], $paymentDetails['group_id']);
        $paymentDetails['trace_id'] = $request->user()->id;

        if (isset($paymentDetails['payment_date'])) {
            $paymentDetails['created_at'] = $paymentDetails['payment_date'];
            unset($paymentDetails['payment_date']);
        }

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
        $payment = $this->paymentRepository->getPaymentById($paymentId);
        abort_unless($this->groupRepository->canManageGroup($request->user(), $payment->group_id), 403);
        $loanDetails = $request->validate([
            'loan_id' => 'sometimes|uuid|exists:loans,id',
            'amount' => 'sometimes|numeric|min:0.01',
            'interest' => 'sometimes|numeric|min:0',
            'payment_date' => 'sometimes|date',
            'transaction_reference' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
        ]);
        if (isset($loanDetails['payment_date'])) {
            $loanDetails['created_at'] = $loanDetails['payment_date'];
            unset($loanDetails['payment_date']);
        }
        if (isset($loanDetails['loan_id'])) {
            $loan = $this->loanRepository->getLoanById($loanDetails['loan_id']);
            abort_unless(
                $loan->group_id === $payment->group_id
                    && $loan->user_id === $payment->user_id,
                422,
                'O empréstimo seleccionado não corresponde ao pagamento.'
            );
        }

        return response()->json([
            'data' => $this->paymentRepository->updatePayment($paymentId, $loanDetails)
        ]);
    }

    public function destroy(Request $request): JsonResponse 
    {
        $paymentId = $request->route('id');
        $payment = $this->paymentRepository->getPaymentById($paymentId);
        abort_unless($this->groupRepository->canManageGroup($request->user(), $payment->group_id), 403);
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

    private function assertGroupMember(string $userId, string $groupId): void
    {
        $isMember = DB::table('group_users')
            ->where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', Status::ACTIVE->value)
            ->exists();

        abort_unless($isMember, 422, 'O utilizador não pertence ao grupo seleccionado.');
    }
}
