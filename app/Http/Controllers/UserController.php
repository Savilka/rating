<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    public function __construct(
        protected Services\UserService $userService
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['name' => 'required|max:50|min:3|regex:/^[\w]*$/']);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 400);
        }

        $validator = Validator::make($request->all(), ['name' => 'unique:users,name']);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 409);
        }

        $user = $this->userService->createNewUserFromData($request->all());

        return response()->json([
            'id'   => $user->id,
            'name' => $user->name,
        ], 201);
    }

    public function addScore(User $user, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['points' => 'required|integer|min:1|max:10000']);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 400);
        }

        $user = $this->userService->addPointsToUser($user, $request->get('points'));

        return response()->json([
            'id'    => $user->id,
            'score' => $user->score,
        ]);
    }
}
