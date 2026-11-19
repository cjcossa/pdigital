<?php

namespace App\Http\Middleware;

use App\Interfaces\GroupRepositoryInterface;
use Closure;
use Illuminate\Http\Request;

class EnsureSystemAdmin
{
    public function __construct(private GroupRepositoryInterface $groupRepository)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            $request->user() && $this->groupRepository->isSystemAdmin($request->user()),
            403
        );

        return $next($request);
    }
}
