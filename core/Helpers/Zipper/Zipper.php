<?php

namespace Core\Helpers\Zipper;

use Exception;
use ZipArchive;

class Zipper
{
    /** @var string $skipMode Select files to skip */
    private string $skipMode = 'NONE';

    /** @var array|string[] $supportedSkipModes Supported skip modes */
    private array $supportedSkipModes = ['HIDDEN', 'ZANYSOFT', 'ALL', 'NONE'];

    /** @var int $mask Mask for the extraction folder (if it should be created) */
    private int $mask = 0777;

    /** @var object $zipArchive ZipArchive internal pointer */
    private $zipArchive = null;

    /** @var string|null $zipFile zip file name */
    private ?string $zipFile = null;

    /** @var string|null $password zip file password (only for extract) */
    private ?string $password = null;

    /** @var string|null $path Current base path */
    private ?string $path = null;

    /** @var array|string[] $zipStatusCodes Array of well known zip status codes */
    private static array $zipStatusCodes = [
        ZipArchive::ER_OK => 'No error',
        ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
        ZipArchive::ER_RENAME => 'Renaming temporary file failed',
        ZipArchive::ER_CLOSE => 'Closing zip archive failed',
        ZipArchive::ER_SEEK => 'Seek error',
        ZipArchive::ER_READ => 'Read error',
        ZipArchive::ER_WRITE => 'Write error',
        ZipArchive::ER_CRC => 'CRC error',
        ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
        ZipArchive::ER_NOENT => 'No such file',
        ZipArchive::ER_EXISTS => 'File already exists',
        ZipArchive::ER_OPEN => 'Can\'t open file',
        ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
        ZipArchive::ER_ZLIB => 'Zlib error',
        ZipArchive::ER_MEMORY => 'Malloc failure',
        ZipArchive::ER_CHANGED => 'Entry has been changed',
        ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
        ZipArchive::ER_EOF => 'Premature EOF',
        ZipArchive::ER_INVAL => 'Invalid argument',
        ZipArchive::ER_NOZIP => 'Not a zip archive',
        ZipArchive::ER_INTERNAL => 'Internal error',
        ZipArchive::ER_INCONS => 'Zip archive inconsistent',
        ZipArchive::ER_REMOVE => 'Can\'t remove file',
        ZipArchive::ER_DELETED => 'Entry has been deleted'
    ];

    /**
     * Class constructor
     *
     * @param string $zipFile ZIP file name
     * @throws Exception
     *
     */
    public function __construct($zipFile)
    {
        if (empty($zipFile)) {
            throw new Exception(self::getStatus(ZipArchive::ER_NOENT));
        }

        $this->zipFile = $zipFile;
    }

    /**
     * Open a zip archive
     * @param string $zipFile ZIP file name
     *
     * @param $zipFile
     * @return Zipper
     * @throws Exception
     */
    public static function open($zipFile)
    {
        try {
            $zip = new Zipper($zipFile);

            $zip->setArchive(self::openZipFile($zipFile));

        } catch (Exception $exception) {
            throw $exception;
        }

        return $zip;
    }

    /**
     * Check a zip archive
     *
     * @param @param string $zip_file ZIP file name
     * @return bool
     * @throws Exception
     */
    public static function check($zip_file)
    {
        try {
            $zip = self::openZipFile($zip_file, ZipArchive::CHECKCONS);

            $zip->close();
        } catch (Exception $exception) {
            throw $exception;
        }

        return true;
    }

    /**
     * Create a new zip archive
     *
     * @param string $zip_file ZIP file name
     * @param bool $overwrite overwrite existing file (if any)
     *
     * @return  Zipper
     * @throws Exception
     */
    public static function create($zip_file, $overwrite = false)
    {
        $overwrite = filter_var($overwrite, FILTER_VALIDATE_BOOLEAN, [
            'options' => [
                'default' => false
            ]
        ]);

        try {
            $zip = new Zipper($zip_file);

            if ($overwrite) {
                $zip->setArchive(self::openZipFile($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE));
            } else {
                $zip->setArchive(self::openZipFile($zip_file, ZipArchive::CREATE));
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $zip;
    }

    /**
     * Set files to skip
     *
     * @param string $mode [HIDDEN, ZANYSOFT, ALL, NONE]
     *
     * @return  Zipper
     * @throws Exception
     */
    final public function setSkipped($mode)
    {
        $mode = strtoupper($mode);

        if (!in_array($mode, $this->supportedSkipModes)) {
            throw new Exception('Unsupported skip mode');
        }

        $this->skipMode = $mode;

        return $this;
    }

    /**
     * Get current skip mode (HIDDEN, ZANYSOFT, ALL, NONE)
     *
     * @return  string
     */
    final public function getSkipped()
    {
        return $this->skipMode;
    }

    /**
     * Set extraction password
     *
     * @param string $password
     *
     * @return  Zipper
     */
    final public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get current extraction password
     *
     * @return  string
     */
    final public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set current base path (just to add relative files to zip archive)
     *
     * @param string $path
     *
     * @return  Zipper
     * @throws Exception
     */
    final public function setPath($path)
    {
        if (!file_exists($path)) {
            throw new Exception('Not existent path');
        }

        $this->path = $path[strlen($path) - 1] == '/' ? $path : $path . '/';

        return $this;
    }

    /**
     * Get current base path
     *
     * @return  string
     */
    final public function getPath()
    {
        return $this->path;
    }

    /**
     * Set extraction folder mask
     *
     * @param int $mask
     *
     * @return  Zipper
     */
    final public function setMask($mask)
    {
        $mask = filter_var($mask, FILTER_VALIDATE_INT, [
            'options' => [
                'max_range' => 0777,
                'default' => 0777
            ], 'flags' => FILTER_FLAG_ALLOW_OCTAL
        ]);

        $this->mask = $mask;

        return $this;
    }

    /**
     * Get current extraction folder mask
     *
     * @return  int
     */
    final public function getMask()
    {
        return $this->mask;
    }

    /**
     * Set the current ZipArchive object
     *
     * @param \ZipArchive $zip
     *
     * @return  Zipper
     */
    final public function setArchive(ZipArchive $zip)
    {
        $this->zipArchive = $zip;

        return $this;
    }

    /**
     * Get current ZipArchive object
     *
     * @return  ZipArchive
     */
    final public function getArchive()
    {
        return $this->zipArchive;
    }

    /**
     * Get current zip file
     *
     * @return  string
     */
    final public function getZipFile()
    {
        return $this->zipFile;
    }

    /**
     * Get an SplFileObject for the zip file
     * @return \SplFileObject
     */
    public function getFileObject()
    {
        return new \SplFileObject($this->zipFile);
    }

    /**
     * Get a list of files in archive (array)
     *
     * @return  array
     * @throws Exception
     */
    public function listFiles()
    {
        $list = [];

        for ($i = 0; $i < $this->zipArchive->numFiles; $i++) {
            $name = $this->zipArchive->getNameIndex($i);

            if ($name === false) {
                throw new Exception(self::getStatus($this->zipArchive->status));
            }

            array_push($list, $name);
        }

        return $list;
    }

    /**
     * Check if zip archive has a file
     *
     * @param string $file File
     * @param int $flags (optional) ZipArchive::FL_NOCASE, ZipArchive::FL_NODIR seperated by bitwise OR
     *
     * @return  bool
     * @throws Exception
     */
    public function has($file, $flags = 0)
    {
        if (empty($file)) {
            throw new Exception('Invalid File');
        }

        return $this->zipArchive->locateName($file, $flags) !== false;
    }

    /**
     * Extract files from zip archive
     *
     * @param string $destination Destination path
     * @param mixed $files (optional) a filename or an array of filenames
     *
     * @return  bool
     * @throws Exception
     */
    public function extract($destination, $files = null)
    {
        if (empty($destination)) {
            throw new Exception('Invalid destination path');
        }

        if (!file_exists($destination)) {
            $omask = umask(0);

            $action = mkdir($destination, $this->mask, true);

            umask($omask);

            if ($action === false) {
                throw new Exception('Error creating folder ' . $destination);
            }
        }

        if (!is_writable($destination)) {
            throw new Exception('Destination path not writable');
        }

        if (is_array($files) && count($files) != 0) {
            $file_matrix = $files;
        } else {
            $file_matrix = $this->getArchiveFiles();
        }

        if (!empty($this->password)) {
            $this->zipArchive->setPassword($this->password);
        }

        $extract = $this->zipArchive->extractTo($destination, $file_matrix);

        if ($extract === false) {
            throw new Exception(self::getStatus($this->zipArchive->status));
        }

        return true;
    }

    /**
     * Add files to zip archive
     *
     * @param mixed $file_name_or_array filename to add or an array of filenames
     * @param bool $flatten_root_folder in case of directory, specify if root folder should be flatten or not
     *
     * @return  Zipper
     * @throws Exception
     */
    public function add($file_name_or_array, $flatten_root_folder = false)
    {
        if (empty($file_name_or_array)) {
            throw new Exception(self::getStatus(ZipArchive::ER_NOENT));
        }

        $flatten_root_folder = filter_var($flatten_root_folder, FILTER_VALIDATE_BOOLEAN, [
            'options' => [
                'default' => false
            ]
        ]);

        try {
            if (is_array($file_name_or_array)) {
                foreach ($file_name_or_array as $file_name) {
                    $this->addItem($file_name, $flatten_root_folder);
                }
            } else {
                $this->addItem($file_name_or_array, $flatten_root_folder);
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $this;
    }

    /**
     * Delete files from zip archive
     *
     * @param mixed $file_name_or_array filename to delete or an array of filenames
     *
     * @return  Zipper
     * @throws Exception
     */
    public function delete($file_name_or_array)
    {
        if (empty($file_name_or_array)) {
            throw new Exception(self::getStatus(ZipArchive::ER_NOENT));
        }

        try {
            if (is_array($file_name_or_array)) {
                foreach ($file_name_or_array as $file_name) {
                    $this->deleteItem($file_name);
                }
            } else {
                $this->deleteItem($file_name_or_array);
            }
        } catch (Exception $exception) {
            throw $exception;
        }

        return $this;
    }

    /**
     * Close the zip archive
     *
     * @return  bool
     * @throws Exception
     */
    public function close()
    {
        if ($this->zipArchive->close() === false) {
            throw new Exception(self::getStatus($this->zipArchive->status));
        }

        return true;
    }

    /**
     * Get a list of file contained in zip archive before extraction
     *
     * @return  array
     */
    private function getArchiveFiles()
    {
        $list = [];

        for ($i = 0; $i < $this->zipArchive->numFiles; $i++) {
            $file = $this->zipArchive->statIndex($i);

            if ($file === false) {
                continue;
            }

            $name = str_replace('\\', '/', $file['name']);

            if ($name[0] == '.' and in_array($this->skipMode, ['HIDDEN', 'ALL'])) {
                continue;
            }

            if ($name[0] == '.' and @$name[1] == '_' and in_array($this->skipMode, ['ZANYSOFT', 'ALL'])) {
                continue;
            }

            array_push($list, $name);
        }

        return $list;
    }

    /**
     * Add item to zip archive
     *
     * @param string $file File to add (realpath)
     * @param bool $flatroot (optional) If true, source directory will be not included
     * @param string|null $base (optional) Base to record in zip file
     *
     * @throws Exception
     */
    private function addItem(string $file, bool $flatroot = false, string $base = null): void
    {
        $file = is_null($this->path) ? $file : $this->path . $file;

        $real_file = str_replace('\\', '/', realpath($file));

        $real_name = basename($real_file);

        if (!is_null($base)) {
            if ($real_name[0] == '.' and in_array($this->skipMode, ['HIDDEN', 'ALL'])) {
                return;
            }

            if ($real_name[0] == '.' and @$real_name[1] == '_' and in_array($this->skipMode, ['ZANYSOFT', 'ALL'])) {
                return;
            }
        }

        if (is_dir($real_file)) {
            if (!$flatroot) {
                $folder_target = is_null($base) ? $real_name : $base . $real_name;

                $new_folder = $this->zipArchive->addEmptyDir($folder_target);

                if ($new_folder === false) {
                    throw new Exception(self::getStatus($this->zipArchive->status));
                }
            } else {
                $folder_target = null;
            }

            foreach (new \DirectoryIterator($real_file) as $path) {
                if ($path->isDot()) {
                    continue;
                }

                $file_real = $path->getPathname();

                $base = is_null($folder_target) ? null : ($folder_target . '/');

                try {
                    $this->addItem($file_real, false, $base);
                } catch (Exception $ze) {
                    throw $ze;
                }
            }
        } else if (is_file($real_file)) {
            $file_target = is_null($base) ? $real_name : $base . $real_name;

            $add_file = $this->zipArchive->addFile($real_file, $file_target);

            if ($add_file === false) {
                throw new Exception(self::getStatus($this->zipArchive->status));
            }
        } else {
            return;
        }
    }

    /**
     * Delete item from zip archive
     *
     * @param string $file File to delete (zippath)
     * @throws Exception
     *
     */
    private function deleteItem($file)
    {
        $deleted = $this->zipArchive->deleteName($file);

        if ($deleted === false) {
            throw new \Exception(self::getStatus($this->zipArchive->status));
        }
    }

    /**
     * Open a zip file
     *
     * @param string $zip_file ZIP status code
     * @param int $flags ZIP status code
     *
     * @return  \ZipArchive
     * @throws Exception
     */
    private static function openZipFile($zip_file, $flags = null)
    {
        $zip = new ZipArchive();

        if (is_null($flags)) {
            $open = $zip->open($zip_file);
        } else {
            $open = $zip->open($zip_file, $flags);
        }


        if ($open !== true) {
            throw new \Exception(self::getStatus($open));
        }

        return $zip;
    }

    /**
     * Get status from zip status code
     *
     * @param int $code ZIP status code
     *
     * @return  string
     */
    private static function getStatus(int $code): string
    {
        if (array_key_exists($code, self::$zipStatusCodes)) {
            return self::$zipStatusCodes[$code];
        }
        return sprintf('Unknown status %s', $code);
    }
}
