<?php

namespace App\Http\Controllers;

use App\Interfaces\UserJoinRequestRepositoryInterface;
use Illuminate\Http\Request;
use App\DTO\UserJoinData;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;

class UserJoinRequestController extends Controller 
{
    private UserJoinRequestRepositoryInterface $_userJoinRequestRepository;

    public function __construct(UserJoinRequestRepositoryInterface $userJoinRequestRepository) 
    {
        $this->_userJoinRequestRepository = $userJoinRequestRepository;
    }

    public function store(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'rid' => 'required|uuid|exists:roles,id',
                'tid' => 'required|uuid|exists:users,id',
                'gid' => 'required|uuid|exists:groups,id',
                'uid' => 'required|uuid|exists:users,id',
               // 'gcid' => 'nullable|uuid' O ideal é associar ao grupo cycle no ato de aprovacao pelo gestor
            ]);
        
            parent::setResponse(
                $this->_userJoinRequestRepository->createUserJoin(
                    UserJoinData::fromArray($validated)
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
    public function update(Request $request)
    {
        parent::setResponse($this->_userJoinRequestRepository->updateUserJoin(UserJoinData::fromArray($request->all())));

        return ApiResponse::send(
            success: parent::getResponse()->success, 
            message: parent::getResponse()->message, 
            status: parent::getResponse()->success ? 200 : 500, 
            data: parent::getResponse()->data
        );
    }
}
