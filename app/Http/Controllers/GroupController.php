<?php

namespace App\Http\Controllers;

use App\DTO\GroupData;
use App\Http\Requests\StorePreUserRequest;
use App\Interfaces\GroupRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DTO\PreUserData;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;

class GroupController extends Controller 
{
    private GroupRepositoryInterface $_groupRepository;

    public function __construct(GroupRepositoryInterface $groupRepository) 
    {
        $this->_groupRepository = $groupRepository;
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->_groupRepository->getGroupsForUser($request->user())
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->_groupRepository->getAccessibleGroupById(
                $request->route('id'),
                $request->user()
            )
        ]);
    }

    public function store(Request $request) 
    {
        try
        {
            abort_unless($this->_groupRepository->canManageGroups($request->user()), 403);

            $validated = $request->validate([
                'gname' => 'required|string',
                'jcod' => 'required|string',
                'img' => 'nullable|string',
                'desc' => 'nullable|string'
            ]);
        
            parent::setResponse(
                $this->_groupRepository->createGroup(
                    GroupData::fromArray($validated + ['oid' => $request->user()->id])
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

    // public function show(Request $request): JsonResponse 
    // {
    //     $userId = $request->route('id');

    //     return response()->json([
    //         'data' => $this->userRepository->getUserById($userId)
    //     ]);
    // }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:200',
            'join_code' => 'sometimes|required|string|size:8|unique:groups,join_code,' . $request->route('id') . ',id',
            'image' => 'sometimes|nullable|string',
            'description' => 'sometimes|nullable|string',
        ]);

        $groupId = $request->route('id');
        $this->_groupRepository->getAccessibleGroupById($groupId, $request->user());
        $this->_groupRepository->updateGroup($groupId, $validated);

        return response()->json([
            'data' => $this->_groupRepository->getAccessibleGroupById($groupId, $request->user())
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        $groupId = $request->route('id');
        $this->_groupRepository->getAccessibleGroupById($groupId, $request->user());
        $this->_groupRepository->deleteGroup($groupId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
