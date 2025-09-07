<?php

namespace App\Http\Controllers;

use App\DTO\GroupCycleData;
use App\DTO\GroupData;
use App\Http\Requests\StorePreUserRequest;
use App\Interfaces\GroupCycleRepositoryInterface;
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

    public function __construct(GroupCycleRepositoryInterface $groupCycleRepository) 
    {
        $this->_groupCycleRepository = $groupCycleRepository;
    }

    public function store(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'gcname' => 'required|string',
                'tid' => 'required|uuid|exists:users,id',
                'gid' => 'required|uuid|exists:groups,id',
                'sdate' => 'required|date_format:Y-m-d|after_or_equal:today',
                'edate'   => 'required|date_format:Y-m-d|after:sdate',
            ]);
        
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
