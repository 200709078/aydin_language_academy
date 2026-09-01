<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display registered users for administrators.
     */
    public function index(Request $request): View
    {
        $sort = in_array($request->query('sort'), ['name', 'created_at'], true)
            ? $request->query('sort')
            : 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $usersQuery = User::query()
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'type',
                'profile_photo_path',
                'created_at',
                'updated_at',
            ]);

        $usersQuery->orderBy($sort, $direction)->orderBy('id');

        $users = $usersQuery
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('direction', 'sort', 'users'));
    }
}
