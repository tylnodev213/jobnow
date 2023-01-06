<?php

namespace Core\Services;

use Core\Helpers\ExportCsv;
use Core\Providers\Facades\Storages\BaseStorage;
use Core\Repositories\BaseRepository;

class BaseService
{
    /**
     * @var BaseRepository $repository
     */
    protected $repository;

    public function __construct()
    {
        //
    }

    /**
     * @param $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return BaseRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function index($params)
    {
        return $this->getRepository()->getListForIndex($params);
    }

    /**
     * Store data
     *
     * @param $params
     * @return bool
     */
    public function store($params)
    {
        try {
            $this->prepareBeforeStore($params);
            $this->uploadToMedia($params);
            $this->getRepository()->create($params);
            return true;
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }

        return false;
    }

    /**
     * Update data
     *
     * @param $id
     * @param $params
     * @return bool
     */
    public function update($id, $params)
    {
        try {
            $this->prepareBeforeUpdate($params);
            $this->uploadToMedia($params);
            $this->getRepository()->update($id, $params);

            return true;
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }

        return false;
    }

    /**
     * Delete item
     *
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        try {
            $this->getRepository()->delete($id);
            return true;
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }

        return false;
    }

    /**
     * @param $params
     * @param $filename
     * @param $headers
     */
    public function downloadCsv($params, $filename, $headers)
    {
        $data = $this->getRepository()->getListForExport($params);
        $export = new ExportCsv($filename);
        $export->export($headers, $data);
    }

    /**
     * * Upload file
     *
     * @param $params
     * @param string $subFolder
     * @param bool $uploadTmp
     */
    protected function uploadToMedia(&$params, $subFolder = '')
    {
        $isUpload = $params['is_upload'] ?? [];

        foreach ($isUpload as $field) {
            $file = $params[$field] ?? '';

            if (empty($file)) {
                continue;
            }

            $filename = explode('/', $file);
            $filename = end($filename);
            $controller = getControllerName();
            $folder = !empty($subFolder) ? $subFolder . '/' : (!empty($controller) ? $controller . '/' : '');
            $newPath = $folder . '' . $filename;

            $uploaded = BaseStorage::moveFromTmpToMedia($file, $newPath);
            if ($uploaded) {
                $params[$field] = $uploaded;
            }
        }
    }

    protected function prepareBeforeStore(&$params)
    {
        //
    }

    protected function prepareBeforeUpdate(&$params)
    {
        //
    }
}
