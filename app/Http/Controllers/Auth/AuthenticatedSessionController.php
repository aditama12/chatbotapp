use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

public function store(Request $request): JsonResponse
{
$request->validate([
'email' => 'required|email',
'password' => 'required',
]);

$user = User::where('email', $request->email)->first();

if (!$user || !Hash::check($request->password, $user->password)) {
return response()->json([
'message' => 'Email atau password salah'
], 401);
}

$token = $user->createToken('auth_token')->plainTextToken;

return response()->json([
'message' => 'Login berhasil',
'user' => $user,
'token' => $token
]);
}