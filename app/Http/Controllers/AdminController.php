<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\PatchHeatRequest;
use App\Http\Requests\Admin\PatchImageExtractionRequest;
use App\Http\Requests\Admin\PatchSchedulerRequest;
use App\Http\Requests\Admin\PatchViewsRequest;
use App\Http\Requests\Admin\PatchViewsTimeRequest;
use App\Http\Requests\Admin\PostHeatRequest;
use App\Http\Requests\Admin\PostSearchUsersRequest;
use App\Http\Requests\Admin\PutDefaultRolesRequest;
use App\Http\Requests\Admin\PatchRegistrationRequest;

use App\Http\Requests\Admin\PutSchedulerRequest;
use App\Http\Requests\Admin\PutViewsTimeRequest;

use App\Http\Requests\Role\PatchRoleRequest;
use App\Http\Requests\Role\CreateRoleRequest;

use App\Image;

use App\Library;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function statistics()
    {
        return view('admin.statistics');
    }

    public function config()
    {
        return view('admin.config');
    }

    public function libraries()
    {
        return view('admin.libraries');
    }

    public function users()
    {
        $users = User::orderBy('name', 'asc')->paginate(18);
        $roles = Role::all();
        $libraries = Library::all();

        $users->onEachSide(1)->withPath(\URL::to('admin/users'));

        return view('admin.users')
            ->with('users', $users)
            ->with('roles', $roles)
            ->with('libraries', $libraries);
    }

    public function searchUsers(PostSearchUsersRequest $request)
    {
        // good enough for now - not going to be used extensively
        $users = User::where('name', $request->get('name'))
            ->orWhere('name', 'like', '%' . $request->get('name') . '%')
            ->get();

        $roles = Role::all();

        return view('admin.users')
            ->with('users', $users)
            ->with('roles', $roles);
    }


    public function patchImages(Request $request)
    {
        $queuedSuccessfully = ! empty(\Queue::push(new \App\Jobs\CleanupImageDisk()));

        $response = redirect()->action('AdminController@index');

        if (! $queuedSuccessfully)
            return $response->withErrors('Unable to queue the cleanup job.');

        session()->flash('success', 'Successfully queued the cleanup job. Check the queue worker(s) for progress.');

        return $response;
    }

    public function deleteImages(Request $request)
    {
        $disk = \Storage::disk('images');
        $dirs = $disk->directories();
        $dirCount = count($dirs);
        $dirDeletedCount = 0;

        foreach ($dirs as $dir) {
            $deletedSuccessfully = $disk->deleteDirectory($dir);
            if ($deletedSuccessfully)
                ++$dirDeletedCount;
        }

        $response = redirect()->action('AdminController@index');

        if ($dirDeletedCount !== $dirCount)
            return $response->withErrors('Unable to completely wipe the images disk.');

        session()->flash('success', 'Successfully wiped the images disk.');

        return $response;
    }

    public function patchRegistration(PatchRegistrationRequest $request)
    {
        if ($request->has('enabled')) {
            \Cache::tags(['config', 'registration'])->forever('enabled', true);

            return redirect()->back()->with('success', 'Registration is now enabled.');
        } else {
            \Cache::tags(['config', 'registration'])->forget('enabled');

            return redirect()->back()->with('success', 'Registration is now disabled');
        }
    }

    public function putDefaultRoles(PutDefaultRolesRequest $request)
    {
        $roleIds = $request->get('role_ids', []);
        $defaultRoles = [];

        \Cache::tags(['config', 'registration'])->forget('roles');

        foreach ($roleIds as $id) {
            $defaultRoles[$id] = $id;
        }

        \Cache::tags(['config', 'registration'])->forever('roles', $defaultRoles);

        return redirect()->back()->with('success', 'Default roles have been updated.');
    }

    public function patchHeat(PatchHeatRequest $request)
    {
        if ($request->has('enabled')) {
            \Cache::tags(['config', 'heat'])->forever('enabled', true);

            return redirect()->back()->with('success', 'Heat is now enabled.');
        } else {
            \Cache::tags(['config', 'heat'])->forget('enabled');

            return redirect()->back()->with('success', 'Heat is now disabled.');
        }
    }

    public function postHeat(PostHeatRequest $request)
    {
        \Cache::tags(['config', 'heat'])->forget('default');
        \Cache::tags(['config', 'heat'])->forget('threshold');
        \Cache::tags(['config', 'heat'])->forget('heat');
        \Cache::tags(['config', 'heat'])->forget('cooldown');

        if ($request->get('action') === 'reset') {
            \Cache::tags(['config', 'heat'])->forever('default', 100);
            \Cache::tags(['config', 'heat'])->forever('threshold', 50);
            \Cache::tags(['config', 'heat'])->forever('heat', 3.0);
            \Cache::tags(['config', 'heat'])->forever('cooldown', 0.01);
        } else {
            \Cache::tags(['config', 'heat'])->forever('default', $request->get('heat_default'));
            \Cache::tags(['config', 'heat'])->forever('threshold', $request->get('heat_threshold'));
            \Cache::tags(['config', 'heat'])->forever('heat', $request->get('heat_heat'));
            \Cache::tags(['config', 'heat'])->forever('cooldown', $request->get('heat_cooldown'));
        }

        return redirect()->back()->with('success', 'The heat values have been updated.');
    }

    public function patchViews(PatchViewsRequest $request)
    {
        if ($request->has('enabled')) {
            \Cache::tags(['config', 'views'])->forever('enabled', true);

            return redirect()->back()->with('success', 'The view counter is now enabled.');
        } else {
            \Cache::tags(['config', 'views'])->forever('enabled', false);

            return redirect()->back()->with('success', 'The view counter is now disabled.');
        }
    }

    public function patchViewsTime(PatchViewsTimeRequest $request)
    {
        if ($request->has('enabled')) {
            \Cache::tags(['config', 'views', 'time'])->forever('enabled', true);

            return redirect()->back()->with('success', 'The views counter based on time is now enabled.');
        } else {
            \Cache::tags(['config', 'views', 'time'])->forever('enabled', false);

            return redirect()->back()->with('success', 'The view counter based on time is now disabled.');
        }
    }

    public function putViewsTime(PutViewsTimeRequest $request)
    {
        \Cache::tags(['config', 'views', 'time'])->forget('threshold');
        if ($request->get('action') === 'reset') {
            \Cache::tags(['config', 'views', 'time'])->forever('threshold', '3h');
        } else {
            \Cache::tags(['config', 'views', 'time'])->forever('threshold', $request->get('threshold'));
        }

        return redirect()->back()->with('success', 'The view time threshold has been updated.');
    }

    public function patchImageExtraction(PatchImageExtractionRequest $request)
    {
        if ($request->has('enabled')) {
            \Cache::tags(['config', 'image', 'extract'])->forever('enabled', true);

            return redirect()->back()->with('success', 'Image extraction is now enabled.');
        } else {
            \Cache::tags(['config', 'image', 'extract'])->forget('enabled');

            return redirect()->back()->with('success', 'Image extraction is now disabled.');
        }
    }

    public function patchScheduler(PatchSchedulerRequest $request)
    {
        if ($request->has('enabled')) {
            \Cache::tags(['config', 'image', 'scheduler'])->forever('enabled', true);

            return redirect()->back()->with('success', 'The image cleanup scheduler is now enabled.');
        } else {
            \Cache::tags(['config', 'image', 'scheduler'])->forget('enabled');

            return redirect()->back()->with('success', 'The image cleanup scheduler is now disabled.');
        }
    }

    public function putScheduler(PutSchedulerRequest $request)
    {
        \Cache::tags(['config', 'image', 'scheduler'])->forget('cron');
        if ($request->get('action') === 'reset') {
            \Cache::tags(['config', 'image', 'scheduler'])->forever('cron', '@daily');
        } else {
            \Cache::tags(['config', 'image', 'scheduler'])->forever('cron', $request->get('cron'));
        }

        return redirect()->back()->with('success', 'The cron value has been updated.');
    }
}
