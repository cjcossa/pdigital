<?php

namespace App\Http\Controllers;

use App\Interfaces\UserJoinRequestRepositoryInterface;
use App\Models\Role;
use Illuminate\Http\Request;
use App\DTO\UserJoinData;
use App\Helpers\ApiResponse;
use Illuminate\Validation\Rule;
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
                'gid' => 'required|uuid|exists:groups,id',
                'gcid' => [
                    'nullable',
                    'uuid',
                    Rule::exists('group_cycles', 'id')
                        ->where('group_id', $request->input('gid')),
                ],
            ]);

            $validated = [
                'rid' => Role::query()->where('name', 'member')->firstOrFail()->id,
                'tid' => $request->user()->id,
                'gid' => $validated['gid'],
                'uid' => $request->user()->id,
                'gcid' => $validated['gcid'] ?? null,
            ];
        
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
