<?php

namespace Doonamis\User\Infrastructure\Http\Controller;

use App\Http\Controllers\Controller;
use App\Models\User;
use Doonamis\User\Application\Command\DeleteUserCommand;
use Doonamis\User\Application\Command\ListUsersCommand;
use Doonamis\User\Application\Command\UploadFromCsvCommand;
use Doonamis\User\Application\Handler\DeleteUserCommandHandler;
use Doonamis\User\Application\Handler\ListUsersCommandHandler;
use Doonamis\User\Application\Handler\UploadFromCsvCommandHandler;
use Doonamis\User\Application\Request\UploadFromCsvRequest;
use Illuminate\Http\JsonResponse;
use League\Csv\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private ListUsersCommandHandler $listUsersCommandHandler,
        private DeleteUserCommandHandler $deleteUserCommandHandler,
        private UploadFromCsvCommandHandler $uploadFromCsvCommandHandler
    ) {}

    public function index(Request $request)
    {
        $command = new ListUsersCommand($request->user()->id);

        $users = $this->listUsersCommandHandler->handle($command);

        return response()->json($users, JsonResponse::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $command = new DeleteUserCommand($id);

        $this->deleteUserCommandHandler->handle($command);

        return response()->json(JsonResponse::HTTP_NO_CONTENT);
    }

    public function uploadFromCsv(UploadFromCsvRequest $request)
    {
        $command = new UploadFromCsvCommand($request->file('file'));

        try {
            $this->uploadFromCsvCommandHandler->handle($command);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
        return response()->json(JsonResponse::HTTP_CREATED);
    }
}