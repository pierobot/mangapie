<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonInterval;

class StreamableStorageFile
{
    protected $EXPIRES_IN_DAYS = 1;
    /** @var string $root */
    protected $root;
    /** @var string $relativeFilePath */
    protected $relativeFilePath;

    /** @var int $maxAge */
    protected $maxAge;
    /** @var Carbon $expiry */
    protected $expiry;

    /**
     * StreamableStorageFile constructor.
     *
     * @param string $root The root directory of the app storage.
     * @param string $relativeFilePath The relative file path in relation to root.
     * @param CarbonInterval $expiry An interval that defines the expiry.
     */
    public function __construct(
        string $root,
        string $relativeFilePath,
        CarbonInterval $expiry = null)
    {
        $this->root = $root;
        $this->relativeFilePath = $relativeFilePath;

        $this->setExpiry($expiry ? $expiry : CarbonInterval::days($this->EXPIRES_IN_DAYS));
    }

    public function setExpiry(CarbonInterval $expiry) : StreamableStorageFile
    {
        $now = Carbon::now();
        $this->expiry = $now->copy()->add($expiry->toDateInterval());
        $this->maxAge = $now->copy()->add($expiry->toDateInterval())->diffInSeconds($now);

        return $this;
    }

    /**
     * Gets an array of headers with required to use sendfile (X-Accel-Redirect or X-Sendfile).
     *
     * @return array
     * @throws \Exception
     */
    protected function sendfileHeaders() : array
    {
        $webServer = config('app.web_server');

        if ($webServer === 'nginx') {
            $headers = [
                'X-Accel-Redirect' => DIRECTORY_SEPARATOR . $this->relativeFilePath,
            ];
        } elseif ($webServer === 'apache') {
            $headers = [
                'X-Sendfile' => $this->root . DIRECTORY_SEPARATOR . $this->relativeFilePath
            ];
        } else {
            $headers = [];
        }

        return $headers;
    }

    /**
     * Gets an array of headers that contains the content type and length.
     *
     * @return array
     */
    protected function contentHeaders() : array
    {
        $headers = [];
        $fullPath = $this->root . DIRECTORY_SEPARATOR . $this->relativeFilePath;
        $resource = finfo_open(FILEINFO_MIME_TYPE);

        if ($resource) {
            $mime = finfo_file($resource, $fullPath);
            finfo_close($resource);

            $headers['Content-Type'] = $mime;
        }

        $headers['Content-Length'] = filesize($fullPath);

        return $headers;
    }

    /**
     * Gets an array of headers that correspond to the expiry.
     *
     * @return array
     */
    protected function expiryHeaders() : array
    {
        /* TODO: Allow for custom expiry time. The default is 3 days. */
        return [
            'Cache-Control' => "public, max-age={$this->maxAge}",
            'Expires' => $this->expiry->toRfc2822String()
        ];
    }

    /**
     * Gets a response for this file.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function response() : \Illuminate\Http\Response
    {
        $fullPath = $this->root . DIRECTORY_SEPARATOR . $this->relativeFilePath;
        if (! file_exists($fullPath)) {
            return response()->make(404);
        }

        $headers = array_merge($this->sendfileHeaders(), $this->contentHeaders(), $this->expiryHeaders());

        return response()->make()->withHeaders($headers);
    }
}
