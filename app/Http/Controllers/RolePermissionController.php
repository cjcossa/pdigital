<?php

namespace App\Http\Controllers;

use App\DTO\GroupData;
use App\Http\Requests\StorePreUserRequest;
use App\Interfaces\RolePermissionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DTO\PreUserData;
use App\DTO\RolePermissionData;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;

class RolePermissionController extends Controller 
{
    private RolePermissionRepositoryInterface $_rolePermissionRepository;

    public function __construct(RolePermissionRepositoryInterface $rolePermissionRepository) 
    {
        $this->_rolePermissionRepository = $rolePermissionRepository;
    }

    public function store(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'rid' => 'required|uuid|exists:roles,id',
                'pid' => 'required|uuid|exists:permissions,id',
                'tid' => 'required|uuid|exists:users,id',
            ]);
        
            parent::setResponse(
                $this->_rolePermissionRepository->createRolePermission(
                    RolePermissionData::fromArray($validated)
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
