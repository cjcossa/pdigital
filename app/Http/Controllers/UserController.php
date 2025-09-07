<?php

namespace App\Http\Controllers;

use App\DTO\UserData;
use App\Helpers\ApiResponse;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class UserController extends Controller 
{
    private UserRepositoryInterface $_userRepository;

    public function __construct(UserRepositoryInterface $userRepository) 
    {
        $this->_userRepository = $userRepository;
    }

    public function index(): JsonResponse 
    {
        return response()->json([
            'data' => $this->_userRepository->getAllUsers()
        ]);
    }

    public function store(Request $request): JsonResponse 
    {
        $userDetails = $request->only([
            'username',
            'name',
            'phone_number',
            'group',
            'password'
        ]);

        return response()->json(
            [
                'data' => $this->_userRepository->createUser($userDetails)
            ],
            Response::HTTP_CREATED
        );
    }

    public function show(Request $request): JsonResponse 
    {
        $userId = $request->route('id');

        return response()->json([
            'data' => $this->_userRepository->getUserById($userId)
        ]);
    }

    public function update(Request $request): JsonResponse 
    {
        $userId = $request->route('id');
        $userDetails = $request->only([
            'name',
            'phone_number',
            'group',
            'password'
        ]);

        return response()->json([
            'data' => $this->_userRepository->updateUser($userId, $userDetails)
        ]);
    }

    public function destroy(Request $request): JsonResponse 
    {
        $userId = $request->route('id');
        $this->_userRepository->deleteUser($userId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function user(Request $request): JsonResponse
    {
        $group = $request->input('group');
        
        return response()->json([
            'data' => $this->_userRepository->getUsersByGroup($group)
        ]);
    }

    public function saving(): JsonResponse
    {   
        return response()->json([
            'data' => $this->_userRepository->getUsersToSaving()
        ]);
    }

    public function authenticate(Request $request): JsonResponse
    {   
        try
        {
            $validated = $request->validate([
                'pin' => 'required|string',
                'primaryphone' => 'required|string'
            ]);
        
            parent::setResponse($this->_userRepository->loginUser(UserData::fromArray($validated)));

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

    public function logout(Request $request): JsonResponse
    {   
        return response()->json([
            'data' => $this->_userRepository->logoutUser($request)
        ]);
    }
}
