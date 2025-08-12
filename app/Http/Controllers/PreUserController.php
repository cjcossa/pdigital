<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePreUserRequest;
use App\Interfaces\PreUserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DTO\PreUserData;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;

class PreUserController extends Controller 
{
    private PreUserRepositoryInterface $_preUserRepository;
    private $_response;

    public function __construct(PreUserRepositoryInterface $preUserRepository) 
    {
        $this->_preUserRepository = $preUserRepository;
        $this->_response = [];
    }

    // public function index(): JsonResponse 
    // {
    //     return response()->json([
    //         'data' => $this->userRepository->getAllUsers()
    //     ]);
    // }

    public function store(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'pin' => 'required|string',
                'primaryphone' => 'required|string|unique:pre_users,primary_phone',
                'phones' => 'nullable|array',
                'phones.*' => 'string'
            ]);
        
            $this->_response = $this->_preUserRepository->createPreUser(PreUserData::fromArray($validated));

            return ApiResponse::send(
                success: $this->_response->success, 
                message: $this->_response->message, 
                status: $this->_response->success ? 201 : 500, 
                data: $this->_response->data
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
    //     $userId = $request->route('id');
    //     $userDetails = $request->only([
    //         'name',
    //         'phone_number',
    //         'group',
    //         'password'
    //     ]);

    //     return response()->json([
    //         'data' => $this->userRepository->updateUser($userId, $userDetails)
    //     ]);
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
