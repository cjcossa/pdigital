<?php

namespace App\Http\Controllers;

use App\DTO\GroupCycleData;
use App\DTO\GroupData;
use App\Http\Requests\StorePreUserRequest;
use App\Interfaces\GroupCycleRepositoryInterface;
use App\Interfaces\GroupRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DTO\PreUserData;
use App\DTO\RoleData;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;

class GroupCycleController extends Controller 
{
    private GroupCycleRepositoryInterface $_groupCycleRepository;
    private GroupRepositoryInterface $_groupRepository;

    public function __construct(
        GroupCycleRepositoryInterface $groupCycleRepository,
        GroupRepositoryInterface $groupRepository
    )
    {
        $this->_groupCycleRepository = $groupCycleRepository;
        $this->_groupRepository = $groupRepository;
    }

    public function index(string $groupId, Request $request): JsonResponse
    {
        $this->_groupRepository->getAccessibleGroupById($groupId, $request->user());

        return response()->json([
            'data' => $this->_groupCycleRepository->getGroupCycles($groupId)
        ]);
    }

    public function store(Request $request) 
    {
        try
        {
            abort_unless($this->_groupRepository->canManageGroups($request->user()), 403);

            $validated = $request->validate([
                'gcname' => 'required|string',
                'gid' => 'required|uuid|exists:groups,id',
                'sdate' => 'required|date_format:Y-m-d|after_or_equal:today',
                'edate'   => 'required|date_format:Y-m-d|after:sdate',
                'contribution_amount' => 'required|numeric|min:0',
            ]);

            $this->_groupRepository->getAccessibleGroupById($validated['gid'], $request->user());
            $validated['tid'] = $request->user()->id;
        
            parent::setResponse(
                $this->_groupCycleRepository->createGroupCycle(
                    GroupCycleData::fromArray($validated)
                    )
            );

            return ApiResponse::send(
                success: parent::getResponse()->success, 
                message: parent::getResponse()->message, 
                status: parent::getResponse()->success ? 201 : 500, 
                data: parent::getResponse()->data
            );

        }
        catch(ValidationException $e)
        {
            return ApiResponse::send(
                success: false,
                message: 'Mensagem de erro',
                status: 422, 
                errors: $e->errors()
            );
        }
    }
}
