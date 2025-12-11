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

    // public function index(Request $request): JsonResponse 
    // {
    //     // parent::getResponse() = $this->_groupRepository->getPreUser(PreUserData::fromArray($request->all()));

    //     // return ApiResponse::send(
    //     //     success: parent::getResponse()->success, 
    //     //     message: parent::getResponse()->message, 
    //     //     status: parent::getResponse()->success ? 200 : 500, 
    //     //     data: parent::getResponse()->data
    //     // );
    // }

    public function store(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'gname' => 'required|string',
                'oid' => 'required|uuid|exists:users,id',
                'jcod' => 'required|string',
                'img' => 'nullable|string',
                'desc' => 'nullable|string'
            ]);
        
            parent::setResponse(
                $this->_groupRepository->createGroup(
                    GroupData::fromArray($validated)
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

    // public function update(Request $request): JsonResponse 
    // {
    //     // parent::getResponse() = $this->_groupRepository->updatePreUser(PreUserData::fromArray($request->all()));

    //     // return ApiResponse::send(
    //     //     success: parent::getResponse()->success, 
    //     //     message: parent::getResponse()->message, 
    //     //     status: parent::getResponse()->success ? 200 : 500, 
    //     //     data: parent::getResponse()->data
    //     // );
    // }

    // public function destroy(Request $request): JsonResponse 
    // {
    //     $userId = $request->route('id');
    //     $this->userRepository->deleteUser($userId);

    //     return response()->json(null, Response::HTTP_NO_CONTENT);
    // }

    // public function user(Request $request): JsonResponse
    // {
    //     $group = $request->input('group');
        
    //     return response()->json([
    //         'data' => $this->userRepository->getUsersByGroup($group)
    //     ]);
    // }

    // public function saving(): JsonResponse
    // {   
    //     return response()->json([
    //         'data' => $this->userRepository->getUsersToSaving()
    //     ]);
    // }
}
