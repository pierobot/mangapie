<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests\Library\LibraryCreateRequest;
use App\Http\Requests\Library\LibraryDeleteRequest;
use App\Http\Requests\Library\LibraryStatusRequest;
use App\Http\Requests\Library\LibraryUpdateRequest;

use \App\Library;
use Imtigger\LaravelJobStatus\JobStatus;

class LibraryController extends Controller
{
    public function create(LibraryCreateRequest $request)
    {
        $name = \Input::get('name');
        $path = \Input::get('path');

        // ensure we have a valid path
        if (is_dir($path) == false) {
            return redirect()->back()->withErrors([
                'library' => "'" . $path . "'" . ' does not exist'
            ]);
        }

        // create the library
        $library = Library::create([
            'name' => $name,
            'path' => $path,
        ]);

        if ($library == null) {
            return redirect()->back()->withErrors([
                'library' => 'Unable to create library.'
            ]);
        }

        session()->flash('success', 'Library was successfully created.');

        return redirect()->back();
    }

    public function update(LibraryUpdateRequest $request)
    {
        $library = Library::find($request->get('library_id'));

        $action = $request->get('action');
        if ($action === 'rename') {
            $library->update([
                'name' => $request->get('name')
            ]);

            session()->flash('success', 'Library was successfully renamed.');
        } else {
            $job = new \App\Jobs\ScanLibrary($library);

            $this->dispatch($job);

            session()->flash('success', 'The library is being refreshed.');
        }

        return redirect()->back();
    }

    public function status(LibraryStatusRequest $request)
    {
        $jobIds = \Request::get('ids');

        $jobs = [];
        foreach ($jobIds as $jobId) {
            $job = JobStatus::find($jobId);

            if ($job !== null) {
                if ($job->progress_now == 0 && $job->is_ended) {
                    $job->progress_now = 1;
                    $job->progress_max = 1;
                }

                $jobs[] = [
                    'id' => $jobId,
                    'status' => $job->status,
                    'progress' => $job->progress_percentage,
                ];
            }
        }

        return response()->json([
            'jobs' => $jobs
        ]);
    }

    public function delete(LibraryDeleteRequest $request)
    {
        $library = Library::find($request->get('library_id'));
        $library->forceDelete();

        session()->flash('success', 'Library was successfully deleted.');

        return redirect()->back();
    }
}
