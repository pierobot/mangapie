<?php

namespace App\Http\Controllers;

use App\Avatar;
use App\Http\Requests\User\PutStatusRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserEditRequest;

use App\ReaderHistory;
use App\Role;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return [
            'show' => 'view',
            // TODO: combine comments and activity into one page for the profile; remove from here
            'comments' => 'view',
            'activity' => 'view',
            'history' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
        ];
    }

    public function show(User $user)
    {
        // TODO: Make config option for the # to load

        $recentFavorites = $user->favorites
            ->sortByDesc('updated_at')
            ->take(4)
            ->load('manga');
        $recentReads = $user->readerHistory
            ->sortByDesc('updated_at')
            ->unique('manga_id')
            ->take(4)
            ->load('manga');

        return view('user.activity')
            ->with('user', $user)
            ->with('recentFavorites', $recentFavorites)
            ->with('recentReads', $recentReads);
    }

    // TODO: deprecate and make config option for the # to load
    public function comments(User $user)
    {
        $comments = $user->comments
            ->sortByDesc('created_at')
            ->take(10)
            ->load('manga');

        return view('user.comments')
            ->with('user', $user)
            ->with('comments', $comments);
    }

    public function activity(User $user)
    {
        // TODO: Make config option for the # to load

        $recentFavorites = $user->favorites
            ->sortByDesc('updated_at')
            ->take(4)
            ->load('manga');
        $recentReads = $user->readerHistory
            ->sortByDesc('updated_at')
            ->unique('manga_id')
            ->take(4)
            ->load('manga');

        return view('user.activity')
            ->with('user', $user)
            ->with('recentFavorites', $recentFavorites)
            ->with('recentReads', $recentReads);
    }

    /**
     * Route for viewing a user's history.
     *
     * @param User $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function history(User $user)
    {
        // Create a query that will take 30 distinct manga IDs from a user's reading history
        $distinctQuery = ReaderHistory::query()->from('reader_histories AS b')
            ->select(['b.manga_id'])
            ->distinct()
            ->where('b.user_id', '=', $user->id)
            ->take(30);

        /*
         * Create another query and inner join the one above using forbidden black magic.
         * As far as I'm aware, this will only work with MySQL.
         *
         * TODO: Find workaround for SQL Server & PostgreSQL. Should I refactor into a separate table?
         */
        $items = ReaderHistory::query()->from('reader_histories AS a')
            ->select(['a.manga_id'])
            ->selectRaw(
                /*
                 * Without selectRaw, this query would throw because of strict mode's ONLY_FULL_GROUP_BY.
                 * Sure, you could disable strict mode but that's not really a solution.
                 *
                 * The workaround, is to follow the rules and use an aggregator. The values we are interested in
                 * for the archive_id and updated_at columns are for the latest read archive. Therefore, we can use
                 * the GROUP_CONCAT aggregator with the DISTINCT keyword to order the rows, in descending order,
                 * by the updated_at column and then apply a limit of 1 to give us the latest.
                 */
                'GROUP_CONCAT(DISTINCT a.archive_id ORDER BY a.updated_at DESC LIMIT 1) AS archive_id,' .
                'GROUP_CONCAT(DISTINCT a.page ORDER BY a.updated_at DESC LIMIT 1) AS page,' .
                'GROUP_CONCAT(DISTINCT a.page_count ORDER BY a.updated_at DESC LIMIT 1) AS page_count,' .
                'GROUP_CONCAT(DISTINCT a.updated_at ORDER BY a.updated_at DESC LIMIT 1) AS updated_at'
            )
            ->groupBy(['a.manga_id'])
            ->joinSub($distinctQuery, 'b', 'a.manga_id', '=', 'b.manga_id')
            ->orderByDesc('a.updated_at')
            ->with(['manga', 'archive'])
            ->get();

        /* gh issue #200
         * Seems like the sql server might not always order correctly?
         * Regardless, a sort of 30 items on our end is not expensive
         * This should be solved in  the future once we refactor this whole method to work on all DBMS
         */
        $items = $items->sortByDesc('updated_at');

        return view('user.history')
            ->with('user', $user)
            ->with('items', $items);
    }

    public function avatar(User $user)
    {
        $avatar = new Avatar($user);

        return $avatar->response();
    }

    public function statistics(User $user = null)
    {
        if (empty ($user))
            $user = request()->user();

        return view('lists.statistics')->with('user', $user);
    }

    public function completed(User $user = null)
    {
        if (empty ($user))
            $user = request()->user();

        $user = $user->load('completed');

        return view('lists.completed')->with('user', $user);
    }

    public function dropped(User $user = null)
    {
        if (empty ($user))
            $user = request()->user();

        $user = $user->load('dropped');

        return view('lists.dropped')->with('user', $user);
    }

    public function onHold(User $user = null)
    {
        if (empty ($user))
            $user = request()->user();

        $user = $user->load('onhold');

        return view('lists.onhold')->with('user', $user);
    }

    public function reading(User $user = null)
    {
        if (empty ($user))
            $user = request()->user();

        $user = $user->load('reading');

        return view('lists.reading')->with('user', $user);
    }

    public function planned(User $user = null)
    {
        if (empty ($user))
            $user = request()->user();

        $user = $user->load('planned');

        return view('lists.planned')->with('user', $user);
    }

    /**
     * Creates a user with the provided role(s) and permission(s).
     *
     * @note If library permissions are explicitly requested but are inherited from a role,
     * then the specific permission is not created.
     *
     * @param UserCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(UserCreateRequest $request)
    {
        /** @var User $user */
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => \Hash::make($request->get('password'))
        ]);

        $roleIds = $request->get('roles');
        $roles = Role::whereIn('id', $roleIds)->get();
        foreach ($roles as $role) {
            $user->grantRole($role->name);
        }

        \Session::flash('success', 'User was successfully created.');

        return \Redirect::back();
    }

    public function edit(UserEditRequest $request)
    {
        /** @var User $user */
        $user = User::where('name', $request->get('name'))->first();
        $user->update([
            'name' => $request->get('new-name')
        ]);

        \Session::flash('success', 'User was successfully edited!');

        return \Redirect::back();
    }

    public function destroy(User $user)
    {
        // TODO: add observer to remove anything related
        $user->forceDelete();

        \Session::flash('success', 'User was successfully deleted!');

        return \Redirect::back();
    }

    public function putStatus(PutStatusRequest $request)
    {
        /** @var User $user */
        $user = $request->user()->load(['completed', 'dropped', 'onhold', 'planned', 'reading']);
        $mangaId = $request->get('manga_id');
        $status = $request->get('status');

        try {
            \DB::transaction(function () use ($status, $user, $mangaId) {

                // a series can only belong in one of these tables
                $old = $user->completed->where('manga_id', $mangaId)->first();
                $old = empty($old) ? $user->dropped->where('manga_id', $mangaId)->first() : $old;
                $old = empty($old) ? $user->onHold->where('manga_id', $mangaId)->first() : $old;
                $old = empty($old) ? $user->planned->where('manga_id', $mangaId)->first() : $old;
                $old = empty($old) ? $user->reading->where('manga_id', $mangaId)->first() : $old;

                if (! empty($old))
                    $old->forceDelete();

                if ($status === 'completed') {
                    $user->completed()->updateOrCreate([
                        'manga_id' => $mangaId
                    ]);
                } elseif ($status === 'dropped') {
                    $user->dropped()->updateOrCreate([
                        'manga_id' => $mangaId
                    ]);
                } elseif ($status === 'on_hold') {
                    $user->onHold()->updateOrCreate([
                        'manga_id' => $mangaId
                    ]);
                } elseif ($status === 'planned') {
                    $user->planned()->updateOrCreate([
                        'manga_id' => $mangaId
                    ]);
                } elseif ($status === 'reading') {
                    $user->reading()->updateOrCreate([
                        'manga_id' => $mangaId
                    ]);
                }
            });

            return \Redirect::back();
        } catch (\Throwable $exception) {
            return \Redirect::back()->withErrors('Unable to update series status. Try again later.');
        }
    }
}
