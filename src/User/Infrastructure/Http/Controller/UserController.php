<?php

namespace Doonamis\User\Infrastructure\Http\Controller;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use League\Csv\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('deleted_at', null)->get();
        return response()->json($users);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->deleted_at = now();
        $user->save();
        return response()->json(['message' => 'User deleted successfully'], JsonResponse::HTTP_NO_CONTENT);
    }

    public function uploadFromCsv(Request $request)
    {
        $file = $request->file('file');

        if($file->getClientOriginalExtension() !== 'csv') {
            return response()->json(['message' => 'Solamente se aceptan archivos CSV'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords(); 

        try {
            foreach ($records as $record) {
                $user = User::where('email', trim($record['email']))->first();
                if(is_null($user)) {
                    $user = new User();
                }
                $user->name = trim($record['name']);
                $user->last_name = trim($record['last_name']);
                $user->password = Hash::make(trim($record['password']));
                $user->phone = trim($record['phone']);
                $user->address = trim($record['address']);
                $user->email = trim($record['email']);
                $user->save();
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'El archivo CSV no es válido'], JsonResponse::HTTP_BAD_REQUEST);
        }
        return response()->json(['message' => 'Users uploaded successfully'], JsonResponse::HTTP_CREATED);
    }
}