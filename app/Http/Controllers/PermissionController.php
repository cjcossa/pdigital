<?php

namespace App\Http\Controllers;

use App\DTO\GroupData;
use App\Http\Requests\StorePreUserRequest;
use App\Interfaces\PermissionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\DTO\PreUserData;
use App\DTO\PermissionData;
use App\Helpers\ApiResponse;
use Illuminate\Validation\ValidationException;

class PermissionController extends Controller 
{
    private PermissionRepositoryInterface $_permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository) 
    {
        $this->_permissionRepository = $permissionRepository;
    }

    public function store(Request $request) 
    {
        try
        {
            $validated = $request->validate([
                'pname' => 'required|string|unique:roles,name',
                'tid' => 'required|uuid|exists:users,id',
                'desc' => 'nullable|string'
            ]);
        
            parent::setResponse(
                $this->_permissionRepository->createPermission(
                    PermissionData::fromArray($validated)
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
