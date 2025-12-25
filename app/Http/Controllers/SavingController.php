<?php

namespace App\Http\Controllers;

use App\Interfaces\SavingRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SavingController extends Controller 
{
    private SavingRepositoryInterface $savingRepository;

    public function __construct(SavingRepositoryInterface $savingRepository) 
    {
        $this->savingRepository = $savingRepository;
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
            'amount'        => 'required|numeric|min:1',
            'social_fund'   => 'required|numeric|min:0',
            'saving_date'   => 'required|date',
            'user_id'       => 'required|exists:users,id',
            'group_cycle_id' => 'required|exists:group_cycles,id',
            'description'   => 'nullable|string|max:255',
        ]);

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
        $validated = $request->validate([
            'amount'        => 'sometimes|numeric|min:1',
            'social_fund'   => 'sometimes|numeric|min:0',
            'saving_date'   => 'sometimes|date',
            'user_id'       => 'sometimes|exists:users,id',
            'group_cycle_id' => 'required|exists:group_cycles,id',
            'description'   => 'nullable|string|max:255',
        ]);

        return response()->json([
            'data' => $this->savingRepository->updateSaving($savingId, $validated)
        ]);
    }

    public function destroy(Request $request): JsonResponse 
    {
        $savingId = $request->route('id');
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
}
