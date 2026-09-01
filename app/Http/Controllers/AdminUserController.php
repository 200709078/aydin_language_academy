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
        $sort = $request->query('sort') === 'name' ? 'name' : null;
        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';

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

        if ($sort === 'name') {
            $usersQuery->orderBy('name', $direction)->orderBy('id');
        } else {
            $usersQuery->latest('created_at');
        }

        $users = $usersQuery
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('direction', 'sort', 'users'));
    }
}
