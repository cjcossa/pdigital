<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Interfaces\GroupRepositoryInterface;
use App\Interfaces\SavingRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SavingController extends Controller 
{
    private SavingRepositoryInterface $savingRepository;
    private GroupRepositoryInterface $groupRepository;

    public function __construct(
        SavingRepositoryInterface $savingRepository,
        GroupRepositoryInterface $groupRepository
    )
    {
        $this->savingRepository = $savingRepository;
        $this->groupRepository = $groupRepository;
    }

    public function index(): JsonResponse 
    {
        return response()->json([
            'data' => $this->savingRepository->getAllSavings()
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'social_amount'  => 'required|numeric|min:0',
            'saving_date'    => 'required|date',
            'user_id'        => 'required|exists:users,id',
            'group_id'       => 'required|uuid|exists:groups,id',
            'wallet_code'    => 'nullable|string',
            'group_cycle_id' => [
                'required',
                Rule::exists('group_cycles', 'id')
                    ->where('group_id', $request->input('group_id'))
                    ->where('status', Status::ACTIVE->value),
            ],
            'trace_id'       => 'sometimes|nullable|uuid|exists:users,id',
            'phone_number'   => 'nullable|string',
            'status'         => 'required|string',
            'transaction_reference' => 'nullable|string',
            'description'    => 'nullable|string|max:255',
        ]);
        abort_unless($this->groupRepository->canManageGroup($request->user(), $validated['group_id']), 403);
        $this->assertGroupMember($validated['user_id'], $validated['group_id']);
        $validated['trace_id'] = $request->user()->id;

        return response()->json(
            [
                'data' => $this->savingRepository->createSaving($validated)
            ],
            Response::HTTP_CREATED
        );
    }

    public function show(Request $request): JsonResponse 
    {
        $savingId = $request->route('id');

        return response()->json([
            'data' => $this->savingRepository->getSavingById($savingId)
        ]);
    }

    public function update(Request $request): JsonResponse 
    {
        $savingId = $request->route('id');
        $saving = $this->savingRepository->getSavingById($savingId);
        $validated = $request->validate([
            'amount'         => 'sometimes|numeric|min:1',
            'social_amount'  => 'sometimes|numeric|min:0',
            'saving_date'    => 'sometimes|date',
            'user_id'        => 'sometimes|exists:users,id',
            'group_id'       => 'sometimes|uuid|exists:groups,id',
            'wallet_code'    => 'sometimes|nullable|string',
            'group_cycle_id' => [
                'sometimes',
                Rule::exists('group_cycles', 'id')
                    ->where('group_id', $request->input('group_id', $saving->group_id))
                    ->where('status', Status::ACTIVE->value),
            ],
            'trace_id'       => 'sometimes|nullable|uuid|exists:users,id',
            'phone_number'   => 'sometimes|nullable|string',
            'status'         => 'sometimes|string',
            'transaction_reference' => 'sometimes|nullable|string',
            'description'    => 'nullable|string|max:255',
        ]);
        $groupId = $validated['group_id'] ?? $saving->group_id;
        abort_unless($this->groupRepository->canManageGroup($request->user(), $groupId), 403);
        $this->assertGroupMember($validated['user_id'] ?? $saving->user_id, $groupId);
        $validated['trace_id'] = $request->user()->id;

        return response()->json([
            'data' => $this->savingRepository->updateSaving($savingId, $validated)
        ]);
    }

    public function delete(Request $request): JsonResponse 
    {
        $savingId = $request->route('id');
        $saving = $this->savingRepository->getSavingById($savingId);
        abort_unless($this->groupRepository->canManageGroup($request->user(), $saving->group_id), 403);
        $this->savingRepository->deleteSaving($savingId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function balance(Request $request): JsonResponse 
    {
        $userId = $request->route('id');

        return response()->json([
            'data' => $this->savingRepository->getSavingBalanceByUser($userId)
        ]);
    }
    public function total(Request $request): JsonResponse 
    {
        $date = $request->input('saving_date');
        return response()->json([
            'data' => $this->savingRepository->getTotalAmountSavings($date)
        ]);
    }
    public function saving(Request $request): JsonResponse 
    {
        $userId = $request->input('id');
        $date = $request->input('saving_date');

        return response()->json([
            'data' => $this->savingRepository->getSavingByDate($userId, $date)
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