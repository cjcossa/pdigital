<?php

namespace App\Http\Controllers;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller 
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) 
    {
        $this->userRepository = $userRepository;
    }

    public function index(): JsonResponse 
    {
        return response()->json([
            'data' => $this->userRepository->getAllUsers()
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
                'data' => $this->userRepository->createUser($userDetails)
            ],
            Response::HTTP_CREATED
        );
    }

    public function show(Request $request): JsonResponse 
    {
        $userId = $request->route('id');

        return response()->json([
            'data' => $this->userRepository->getUserById($userId)
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
            'data' => $this->userRepository->updateUser($userId, $userDetails)
        ]);
    }

    public function destroy(Request $request): JsonResponse 
    {
        $userId = $request->route('id');
        $this->userRepository->deleteUser($userId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function user(Request $request): JsonResponse
    {
        $group = $request->input('group');
        
        return response()->json([
            'data' => $this->userRepository->getUsersByGroup($group)
        ]);
    }

    public function saving(): JsonResponse
    {   
        return response()->json([
            'data' => $this->userRepository->getUsersToSaving()
        ]);
    }

    public function authenticate(Request $request): JsonResponse
    {   
        $userDetails = $request->only([
            'primary_phone',
            'pin'
        ]);
        
        return response()->json([
            'data' => $this->userRepository->loginUser($userDetails)
        ]);
    }

    public function logout(Request $request): JsonResponse
    {   
        return response()->json([
            'data' => $this->userRepository->logoutUser($request)
        ]);
    }
}
