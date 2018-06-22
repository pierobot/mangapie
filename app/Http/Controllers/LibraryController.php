<?php

namespace App\Http\Controllers;

use App\Http\Requests\LibraryStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use App\Http\Requests\LibraryCreateRequest;
use App\Http\Requests\LibraryDeleteRequest;
use App\Http\Requests\LibraryUpdateRequest;

use \App\Library;
use \App\Manga;
use Imtigger\LaravelJobStatus\JobStatus;

class LibraryController extends Controller
{
    public function create(LibraryCreateRequest $request)
    {
        $name = \Input::get('name');
        $path = \Input::get('path');

        // ensure we have a valid path
        if (is_dir($path) == false) {
            return \Redirect::action('AdminController@libraries')->withErrors([
                'library' => "'" . $path . "'" . ' does not exist'
            ]);
        }

        // create the library
        $library = Library::create([
            'name' => $name,
            'path' => $path,
        ]);

        if ($library == null) {
            return \Redirect::action('AdminController@libraries')->withErrors([
                'library' => 'Unable to create library.'
            ]);
        }

        // scan and populate the library
        $library->scan();

        \Session::flash('success', 'Library was successfully created.');

        return \Redirect::action('AdminController@libraries');
    }

    public function update(LibraryUpdateRequest $request)
    {
        $libraries = Library::whereIn('id', \Request::get('ids'))->get();

        $jobs = [];
        foreach ($libraries as $library) {
            $job = new \App\Jobs\ScanLibrary($library);

            $this->dispatch($job);

            $jobs[] = [
                'id' => $job->getJobStatusId(),
                'name' => $library->getName(),
            ];
        }

        return \Response::json([
            'jobs' => $jobs
        ]);
    }

    public function status(LibraryStatusRequest $request)
    {
        $jobsAreDone = true;
        $jobsAreSuccessful = true;
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

                if ($job->is_failed)
                    $jobsAreSuccessful = false;

                if ($job->is_ended == false)
                    $jobsAreDone = false;
            }
        }

        if ($jobsAreDone) {
            if ($jobsAreSuccessful) {
                \Session::flash('success', 'The selected libraries have been updated successfully.');
            }
        }

        return \Response::json([
            'jobs' => $jobs
        ]);
    }

    public function delete(LibraryDeleteRequest $request)
    {
        $libraries = Library::whereIn('id', \Request::get('ids'))->get();

        foreach ($libraries as $library) {
            $library->forceDelete();
        }

        \Session::flash('success', 'The selected libraries were successfully deleted.');

        return \Response::json();
    }
}
