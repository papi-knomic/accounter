<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\CustomResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function register( RegisterUserRequest $request ) : JsonResponse
    {
        $fields = $request->validated();

        $user = $this->userRepository->create($fields);

        return CustomResponse::successResponseWithData($user, 'Successful!', 201 );
    }

    public function login( LoginUserRequest $request ) : JsonResponse
    {
        $userData = $request->validated();

        if (Auth::attempt($userData)) {
            $token = config('keys.token');
            $accessToken = Auth::user()->createToken($token)->plainTextToken;
            $data = auth()->user();
            return CustomResponse::successResponseWithData($data, 'Login successful', 200, $accessToken);
        }
        return CustomResponse::errorResponse('Invalid Login credentials', 400);
    }

    public function profile( Request $request ): JsonResponse
    {
        $token = config('keys.token');
        $accessToken = Auth::user()->createToken($token)->plainTextToken;
        $data = auth()->user();
        $user =  User::whereId($data->id)
            ->first();
        $profileResource = new ProfileResource($user);
        return CustomResponse::successResponseWithData($profileResource, 'Profile data gotten', 200, $accessToken);
    }

    public function viewProfile( User $user ): JsonResponse
    {
        $profileResource = new ProfileResource($user);
        return CustomResponse::successResponseWithData($profileResource,'Success');
    }

    public function update( UpdateProfileRequest $request ) : JsonResponse
    {
        $fields = $request->validated();
		$userID = \auth()->id();

        $this->userRepository->update($fields);

        $user = User::find($userID);
        $profileResource = new ProfileResource($user);

        return CustomResponse::successResponseWithData( $profileResource,'Profile updated', 201);
    }

}
