<?php

namespace App\Http\Controllers;

use App\Interfaces\GroupUserRepositoryInterface;
use Illuminate\Http\Request;
use App\DTO\UserJoinData;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;

class GroupUserController extends Controller 
{
    private GroupUserRepositoryInterface $_groupUserRepository;

    public function __construct(GroupUserRepositoryInterface $groupUserRepository) 
    {
        $this->_groupUserRepository = $groupUserRepository;
    }

    public function store(Request $request) 
    {
        try
        {
            parent::setResponse(
                $this->_groupUserRepository->createGroupUser()
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
