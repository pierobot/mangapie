<?php

namespace App;

use App\Interfaces\WatchDescriptorInterface;

abstract class WatchDescriptor implements WatchDescriptorInterface
{
    private $wd;
    private $wdParent;
    private $path;
    private $data;
    private $isSymbolicLink;

    protected function __construct($wd, $wdParent, $path, $isSymbolicLink, $data)
    {
        $this->wd = $wd;
        $this->wdParent = $wdParent;
        $this->path = $path;
        $this->data = $data;
        $this->isSymbolicLink = $isSymbolicLink;
    }

    public static function make($wd, $wdParent, $isDirectory, $path, $isSymbolicLink, $data = [])
    {
        return $isDirectory ? new DirectoryDescriptor($wd, $wdParent, $path, $isSymbolicLink , $data) :
                              new ArchiveDescriptor($wd, $wdParent, $path, $isSymbolicLink, $data);
    }

    public function getWd()
    {
        return $this->wd;
    }

    public function hasParent()
    {
        return $this->getParent() !== false;
    }

    public function getParent()
    {
        return $this->wdParent;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getData()
    {
        return $this->data;
    }

    public function isSymbolicLink()
    {
        return $this->isSymbolicLink;
    }
}

final class DirectoryDescriptor extends WatchDescriptor
{
    public function __construct($wd, $parent, $path, $isSymbolicLink, $data)
    {
        parent::__construct($wd, $parent, $path, $isSymbolicLink, $data);
    }

    public function isDirectory()
    {
        return true;
    }

    public function isArchive()
    {
        return false;
    }
}

final class ArchiveDescriptor extends WatchDescriptor
{
    public function __construct($wd, $parent, $path, $isSymbolicLink, $data)
    {
        parent::__construct($wd, $parent, $path, $isSymbolicLink, $data);
    }

    public function isDirectory()
    {
        return false;
    }

    public function isArchive()
    {
        return true;
    }
}

final class Watcher
{
    private $fd;
    private $directoryDescriptors = [];

    public function __construct()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return;
        }

        $this->fd = inotify_init();
        if ($this->fd === false)
            dd('Unable to initialize main inotify descriptor.');

        stream_set_blocking($this->fd, false);
    }

    public function track($path, $data = [], $parentWd = false, $recursive = false)
    {
        $isDirectory = is_dir($path);

        return $this->trackEx($path, $isDirectory, $parentWd, $data, $recursive);
    }

    protected function trackEx($path, $isDirectory, $parentWd, $data = [], $recursive = false)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return false;
        }

        if (file_exists($path) === false)
            return false;

        // add a watch for the given path
        $wd = inotify_add_watch($this->fd, $path, IN_CREATE | IN_DELETE | IN_CLOSE_WRITE |
                                                  IN_MOVED_FROM | IN_MOVED_TO |
                                                  IN_DELETE_SELF | IN_MOVE_SELF |
                                                  IN_UNMOUNT | IN_IGNORED |
                                                  IN_Q_OVERFLOW);
        if ($wd === false)
            return false;

        $isSymbolicLink = false;
        // symbolic links to directories will be recognized as a file so we have to dereference and check ourselves
        if (is_link($path)) {
            $isSymbolicLink = true;
            $isDirectory = is_dir(readlink($path));
        }

        // create a watch descriptor and store it in the appropriate array using the wd as its key
        $descriptor = WatchDescriptor::make($wd, $parentWd, $isDirectory, $path, $isSymbolicLink, $data);
        $this->directoryDescriptors[$wd] = $descriptor;

        // if it's a directory, then track the directories and files it contains
        if ($isDirectory) {
            $dirs = self::directories($path, $recursive);
            $files = self::files($dirs);

            if (empty($dirs) === false) {
                foreach ($dirs as $dir) {
                    $newWd = $this->trackEx(
                        $dir,
                        true,
                        $descriptor->getWd(),
                        $descriptor->getData()
                    );
                }
            }

            if ($files !== false && empty($files) == false) {
                foreach ($files as $file) {
                    $newWd = $this->trackEx(
                        $file,
                        false,
                        $descriptor->getWd(),
                        $descriptor->getData()
                    );
                }
            }
        }

        return $wd;
    }

    protected function descriptor($wd)
    {
        if (array_key_exists($wd, $this->directoryDescriptors)) {
            return $this->directoryDescriptors[$wd];
        }

        return false;
    }

    protected function descriptorByPath($parentWd, $path)
    {
        $result = false;

        foreach ($this->directoryDescriptors as $descriptor) {
            if ($descriptor->getPath() === $path &&
                $descriptor->getParent() === $parentWd) {

                $result = $descriptor;
                break;
            }
        }

        return $result;
    }

    protected function unset($wd)
    {
        $result = false;

        if (array_key_exists($wd, $this->directoryDescriptors)) {
            unset($this->directoryDescriptors[$wd]);

            $result = true;
        }

        return $result;
    }

    protected function root($descriptor)
    {
        for ($tmp = $descriptor;
             ! empty($tmp) && $tmp->hasParent();
             $tmp = $this->descriptor($tmp->getParent())) {
            ;
        }

        return $tmp;
    }

    protected static function directories($path, $recursive = false, &$results = [])
    {
        $dirs = \File::directories($path);

        if ($recursive == false)
            return $dirs;

        $results = array_merge($results, $dirs);
        foreach ($dirs as $dir) {
            Watcher::directories($dir, true, $results);
        }

        return $results;
    }

    protected static function files($directories)
    {
        if (empty($directories))
            return false;

        $results = [];
        foreach ($directories as $directory) {
            $files = \File::files($directory);
            if (empty($files))
                continue;

            foreach ($files as $file) {
                array_push($results, $file->getRealPath());
            }
        }

        return $results;
    }

    public function go($once = false)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return;
        }

        while (true) {
            $events = inotify_read($this->fd);
            // keep polling for events if there are none
            if ($events === false) {
                sleep(1);
                continue;
            }

            foreach ($events as $event) {
                $wd = $event['wd'];
                $mask = $event['mask'];
                $name = $event['name'];

                $isDirectory = (($mask & IN_ISDIR) == IN_ISDIR);
                $isSymbolicLink = false;

                $descriptor = $this->descriptor($wd);
                if ($descriptor === false)
                    continue;

                $path = $descriptor->getPath() . DIRECTORY_SEPARATOR . $name;

//                echo var_dump($event) . "\n";

                /**
                 * Determine if the event is for a symbolic link. If so, determine if it points to a directory.
                 *
                 * This is required because the event's mask value will not contain IN_ISDIR for
                 * symbolic links that point to a directory.
                 *
                 * In other words, without this, we'd execute the logic for a file instead of a directory.
                 */
                if (file_exists($path)) {
                    if (is_link($path)) {
                        $isDirectory = is_dir(readlink($path));
                        $isSymbolicLink = true;
                    }
                } else {
                    $cachedDescriptor = $this->descriptorByPath($wd, $path);
                    if ($cachedDescriptor !== false) {
                        $isDirectory = $cachedDescriptor->isDirectory();
                        $isSymbolicLink = $cachedDescriptor->isSymbolicLink();
                    }
                }

                if ($mask & IN_CREATE) {
                    $result = $this->create($wd, $name, $path, $isDirectory, $isSymbolicLink, $descriptor);
                } elseif ($mask & IN_CLOSE_WRITE) {
                    $result = $this->done($wd, $name, $path, $isDirectory, $descriptor);
                } elseif ($mask & IN_MOVED_FROM) {
                    $result = $this->remove($wd, $name, $path, $isDirectory, $isSymbolicLink, $descriptor);
                } elseif ($mask & IN_MOVED_TO) {
                    $result = $this->done($wd, $name, $path, $isDirectory, $descriptor);
                } elseif ($mask & IN_DELETE) {
                    $result = $this->remove($wd, $name, $path, $isDirectory, $isSymbolicLink, $descriptor);
                } elseif ($mask & IN_DELETE_SELF) {
                    $result = $this->removeSelf($wd);
                } elseif ($mask & IN_MOVE_SELF) {
                    $result = $this->removeSelf($wd);
                } elseif ($mask & IN_IGNORED) {
                    $result = $this->ignore($wd);
                } elseif ($mask & IN_UNMOUNT) {
                    $result = $this->remove($wd, $name, $path, $isDirectory, $isSymbolicLink, $descriptor);
                } elseif ($mask & IN_Q_OVERFLOW) {
                    echo 'Event queue has overflowed. A manual scan will be required.';
                }
            }

            if ($once === true)
                break;
        }
    }

    protected function create(
        int $wd,
        string $name,
        string $path,
        bool $isDirectory,
        bool $isSymbolicLink,
        WatchDescriptorInterface $parentDescriptor)
    {
        return $isDirectory ? $this->createDirectory($wd, $name, $path, $isSymbolicLink, $parentDescriptor) :
                              $this->createFile($wd, $name, $path, $parentDescriptor);
    }

    protected function createDirectory(
        int $wd,
        string $name,
        string $path,
        bool $isSymbolicLink,
        WatchDescriptorInterface $parentDescriptor)
    {
        echo __METHOD__ . "\n";

        /**
         * For regular directories, we do not need to recursively track as they will be empty.
         * Symbolic links to directories, on the other hand, will need to be tracked recursively.
         */
        $newWd = $this->trackEx(
            $path,
            true,
            $wd,
            $parentDescriptor->getData(),
            $isSymbolicLink ? true : false
        );

        if ($newWd === false)
            return false;

        $descriptor = $this->descriptor($newWd);
        if ($descriptor === false)
            return false;

        $rootDescriptor = $this->root($parentDescriptor);

        \Event::dispatch(new Events\NewDirectoryEvent(
            $name,
            $path,
            $rootDescriptor->getPath(),
            $descriptor->getData(),
            $isSymbolicLink
        ));

        return true;
    }

    protected function createFile(
        int $wd,
        string $name,
        string $path,
        WatchDescriptorInterface $parentDescriptor)
    {
        echo __METHOD__ . "\n";

        return true;
    }

    protected function done(
        int $wd,
        string $name,
        string $path,
        bool $isDirectory,
        WatchDescriptorInterface $parentDescriptor)
    {
        return $isDirectory ? $this->doneDirectory($wd, $name, $path, $parentDescriptor) :
                              $this->doneFile($wd, $name, $path, $parentDescriptor);
    }

    protected function doneDirectory(
        int $wd,
        string $name,
        string $path,
        WatchDescriptorInterface $parentDescriptor)
    {
        echo __METHOD__ . "\n";

        $rootDescriptor = $this->descriptor($wd);

        $newWd = $this->trackEx($path,
            true,
            $wd,
            $parentDescriptor->getData(),
            true
        );

        if ($newWd !== false) {
            \Event::dispatch(new Events\NewDirectoryEvent(
                $name,
                $path,
                $rootDescriptor->getPath(),
                $parentDescriptor->getData(),
                false
            ));
        }

        return $newWd !== false;
    }

    protected function doneFile(
        int $wd,
        string $name,
        string $path,
        WatchDescriptorInterface $parentDescriptor)
    {
        echo __METHOD__ . "\n";

        $rootDescriptor = $this->root($parentDescriptor);

        $newWd = $this->trackEx(
            $path,
            false,
            $wd,
            $parentDescriptor->getData()
        );

        if ($newWd !== false) {
            \Event::dispatch(new Events\NewArchiveEvent(
                $name,
                $path,
                $rootDescriptor->getPath(),
                $parentDescriptor->getData()
            ));
        }

        return $newWd !== false;
    }

    protected function removeSelf($wd)
    {
        echo __METHOD__ . "\n";

        return $this->unset($wd);
    }

    protected function remove(
        int $wd,
        string $name,
        string $path,
        bool $isDirectory,
        bool $isSymbolicLink,
        WatchDescriptorInterface $parentDescriptor)
    {
        return $isDirectory ? $this->removeDirectory($wd, $name, $path, $isSymbolicLink, $parentDescriptor) :
                              $this->removeFile($wd, $name, $path, $parentDescriptor);
    }

    protected function removeDirectory(
        int $wd,
        string $name,
        string $path,
        bool $isSymbolicLink,
        WatchDescriptorInterface $parentDescriptor)
    {
        echo __METHOD__ . "\n";

        $rootDescriptor = $this->root($parentDescriptor);

        \Event::dispatch(new Events\RemovedDirectoryEvent(
            $name,
            $path,
            $rootDescriptor->getPath(),
            $parentDescriptor->getData(),
            $isSymbolicLink
        ));

        return true;
    }

    protected function removeFile(
        int $wd,
        string $name,
        string $path,
        WatchDescriptorInterface $parentDescriptor)
    {
        echo __METHOD__ . "\n";

        $rootDescriptor = $this->root($parentDescriptor);

        \Event::dispatch(new Events\RemovedArchiveEvent(
            $name,
            $path,
            $rootDescriptor->getPath(),
            $parentDescriptor->getData()
        ));

        return true;
    }

    protected function ignore($wd)
    {
        echo __METHOD__ . "\n";

        return $this->unset($wd);
    }
}
