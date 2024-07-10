<?php
// Include necessary namespaces
namespace App\Http\Controllers;

use Illuminate\Http\Request; // For handling HTTP requests
use Illuminate\Support\Facades\Auth; // For handling authentication
use App\Models\User; // User model
use Illuminate\Support\Facades\Hash; // For hashing passwords
use Illuminate\Validation\ValidationException; // For handling validation exceptions
use Exception; // For general exception handling

// Define AuthController class which extends Controller
class AuthController extends Controller
{
    // Function to handle user registration
    public function register(Request $request,)
    {
        try {
            // Validate incoming request fields
            $request->validate([
                'nom' => 'required|string|max:255', // Name must be a string, not exceed 255 characters and it is required
                'prenom' => 'required|string|max:255', // Last name must be a string, not exceed 255 characters and it is required
                'telephone' => 'required|string|max:20|unique:users', // Phone number must be a string, not exceed 20 characters and it is required
                'email' => 'required|string|email|max:255|unique:users', // Email must be a string, a valid email, not exceed 255 characters, it is required and it must be unique in the users table
                'password' => 'required|string|min:6', // Password must be a string, at least 6 characters and it is required
            ]);



            // Create new User
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'telephone' => $request->telephone,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hash the password
                'role' => 'Client' // Default role is Client
            ]);

            // Return user data as JSON with a 201 (created) HTTP status code
            return response()->json(['user' => $user], 201);
        } catch (ValidationException $e) {
            // Return validation errors with a 422 (Unprocessable Entity) HTTP status code
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            // Return general error message with a 500 (Internal Server Error) HTTP status code
            return response()->json(['message' => 'An error occurred during registration',
         'error' => $e->getMessage()], 500);
        }
    }

    // Function to handle user login
    public function login(Request $request)
    {
        try {
            // Validate incoming request fields
            $request->validate([
                // 'email' => 'required|string|email', // Email must be a string, a valid email and it is required
                'identifier' => 'required|string', // Identifier can be email or phone
                'password' => 'required|string', // Password must be a string and it is required
            ]);

             // Determine if the identifier is an email or phone
            $identifier = $request->input('identifier');
            $credentials = filter_var($identifier, FILTER_VALIDATE_EMAIL) ?
                ['email' => $identifier, 'password' => $request->input('password')] :
                ['telephone' => $identifier, 'password' => $request->input('password')];

            // Check if the provided credentials are valid
            // if (!Auth::attempt($request->only('email', 'password'))) {
            //     // If not, return error message with a 401 (Unauthorized) HTTP status code
            //     return response()->json(['message' => 'Invalid login details'], 401);
            // }

              if (!Auth::attempt($credentials)) {
            // If not, return error message with a 401 (Unauthorized) HTTP status code
            return response()->json(['message' => 'Invalid login details'], 401);
        }

            // If credentials are valid, get the authenticated user
            $user = $request->user();

            // Create a new token for this user
            $token = $user->createToken('authToken')->plainTextToken;

            // Return user data and token as JSON
            return response()->json(['user' => $user, 'token' => $token]);
        } catch (ValidationException $e) {
            // Return validation errors with a 422 (Unprocessable Entity) HTTP status code
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            // Return general error message with a 500 (Internal Server Error) HTTP status code
            return response()->json(['message' => 'An error occurred during login'], 500);
        }
    }

    // Function to handle user logout
    public function logout(Request $request)
    {
        try {
            // Delete all tokens for the authenticated user
            $request->user()->tokens()->delete();

            // Return success message as JSON
            return response()->json(['message' => 'Logged out']);
        } catch (Exception $e) {
            // Return general error message with a 500 (Internal Server Error) HTTP status code
            return response()->json(['message' => 'An error occurred during logout'], 500);
        }
    }

    // Function to update user role
public function updateRole(Request $request, $id)
{
    

    $request->validate([
        'role' => 'required|string|in:Client,Vendeur,Comptable,Admin'
    ]);

    // Vérifier si l'utilisateur authentifié est un admin
    if ($request->user()->role !== 'Admin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    try {
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return response()->json(['message' => 'Role updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'User not found or another error occurred'], 44);
    }
}

}
