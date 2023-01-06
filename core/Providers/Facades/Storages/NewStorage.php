<?php

namespace Core\Providers\Facades\Storages;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class NewStorage
{
    /**
     * @param $name
     * @param $arguments
     * @return false|mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([Storage::class, $name], $arguments);
    }

    /**
     * @param $file
     * @return array|string|string[]
     */
    public function path($file)
    {
        return str_replace('/', '\\', storage_path($file));
    }

    /**
     * @param $fileName
     * @return string
     */
    public function url($fileName)
    {
        if (!$fileName) {
            return '';
        }

        if (str_contains($fileName, 'http')) {
            return $fileName;
        }

        $fileName = str_replace('\\', '/', $fileName);

        return urldecode(Storage::url($fileName));
    }

    /**
     * @param $filePath
     * @param string $newName
     * @return false|mixed|string
     */
    public function moveFromTmpToMedia($filePath, string $newName = '')
    {
        if (!Storage::exists($filePath)) {
            logError(__('messages.file_does_not_exist') . PHP_EOL . '(File path: ' . $filePath . ')');
            return false;
        }

        $newFilePath = getMediaDir(!empty($newName) ? $newName : $filePath);
        $nameBackup = $newFilePath . '_' . time();
        $logs = "(File path: " . $filePath . ", New path: " . $newFilePath . ")";

        try {
            if (Storage::exists($newFilePath)) {
                Storage::move($newFilePath, $nameBackup);
            }

            if (!Storage::move($filePath, $newFilePath)) {
                logError(__('messages.file_upload_failed') . PHP_EOL . $logs);
                return false;
            }

            if (Storage::exists($nameBackup)) {
                Storage::delete($nameBackup);
            }

            return $newFilePath;
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $logs . PHP_EOL . $exception->getTraceAsString());
            if (Storage::exists($nameBackup)) {
                Storage::move($nameBackup, $newFilePath);
            }
            return false;
        }
    }

    /**
     * @param $file
     * @param $content
     * @return bool
     */
    public function put($file, $content)
    {
        if (!$this->isUploadFile($content)) {
            $content = $this->base64ToFile($content);
        }

        return Storage::put($file, $content);
    }

    /**
     * @param $data
     * @return bool
     */
    public function isUploadFile($data)
    {
        return $data instanceof UploadedFile;
    }

    /**
     * @param $fileData
     * @return false|string
     */
    public function base64ToFile($fileData)
    {
        @list($type, $fileData) = explode(';', $fileData);
        @list(, $fileData) = explode(',', $fileData);

        return base64_decode($fileData);
    }

    /**
     * Custom from function uploadToTmp() and moveTmpToMedia()
     * @param $file
     * @param string $fileName
     * @param array $options
     * @return array
     */
    public function uploadFileToMedia($file, string $fileName = '', array $options = ['public-read'])
    {
        $result = ['status' => true, 'filename' => ''];

        if (empty($fileName)) {
            $fileName = $this->genFileName($file);
        }

        if (!$this->validationFile($fileName, $file)) {
            $result['status'] = false;
            return $result;
        }

        $newFileSavePath = getMediaDir($fileName);
        if ($this->isUploadFile($file)) {
            $r = Storage::putFileAs(getMediaDir(), $file, $fileName, $options);
        } else {
            $r = $this->put($newFileSavePath, $file);
        }

        return [
            'status' => (bool)$r,
            'filename' => $r ? $newFileSavePath : '',
        ];
    }

    /**
     * @param $fileName
     * @param $content
     * @return bool
     */
    protected function validationFile($fileName, $content)
    {
        if ($this->isUploadFile($content)) {
            $ext = $content->getClientOriginalExtension();
        } else {
            $ext = Arr::last(explode('.', $fileName));
        }

        $extBlacklist = (array)getConfig('ext_blacklist', ['php', 'phtml', 'html']);

        if (in_array($ext, $extBlacklist)) {
            logError(__('messages.file_upload_blacklist') . PHP_EOL . "(File: " . $fileName . ", Blacklist: " . json_encode($extBlacklist) . ")");
            return false;
        }

        return true;
    }

    /**
     * @param $file
     * @return string
     */
    public function genFileName($file)
    {
        $controller = getControllerName();
        $folder = !empty($controller) ? $controller . '/' : '';
        $pathInfo = $this->mbPathinfo($file->getClientOriginalName());
        $filename = data_get($pathInfo, 'filename') ?? '';
        $filename = str_replace(['!', '@', '#', '$', '%', '^', '&', '/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'], [], $filename);
        $random = time() . sprintf('%09d', rand(0, 999999999));
        return $folder . $filename . '_' . $random . '.' . data_get($pathInfo, 'extension');
    }

    /**
     * Get path info of file upload
     *
     * @param $filepath
     * @return string[]
     */
    public function mbPathInfo($filepath)
    {
        preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $filepath, $m);

        return [
            'dirname' => $m[1] ?? '',
            'basename' => $m[2] ?? '',
            'extension' => $m[5] ?? '',
            'filename' => $m[3] ?? '',
        ];
    }

    /**
     * Upload file to tmp
     *
     * @param $fileName
     * @param $content
     * @return string
     */
    public function uploadToTmp($content, $fileName = null)
    {
        if (empty($fileName)) {
            $fileName = $this->genFileName($content);
        }

        if (!$this->validationFile($fileName, $content)) {
            return false;
        }

        $newFilePath = getTmpUploadDir(date('Y-m-d')) . '/' . $fileName;
        $this->deleteTmpDaily();
        $logs = "(Filename: " . $fileName . ', New path: ' . $newFilePath . ")";

        if ($this->isUploadFile($content)) {
            if (!Storage::putFileAs(getTmpUploadDir(date('Y-m-d')), $content, $fileName, 'public')) {
                logError(__('messages.file_upload_failed') . PHP_EOL . $logs);
                return false;
            }

            return $newFilePath;
        }

        if (!$this->put($newFilePath, $content)) {
            logError(__('messages.file_upload_failed') . PHP_EOL . $logs);
            return false;
        }

        return $newFilePath;
    }

    /**
     * delete tmp file
     */
    public function deleteTmpDaily()
    {
        for ($i = 2; $i <= 30; $i++) { // from 2 days ago
            $directory = getTmpUploadDir(today()->subDays($i)->format('Y-m-d'));
            Storage::deleteDirectory($directory);
        }
    }
}
