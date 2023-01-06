<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\CustomRepository;
use App\Services\CustomService;
use App\Validators\CustomValidator;
use Core\Http\Controllers\BaseController;
use Core\Providers\Facades\Storages\BaseStorage;
use Illuminate\Http\UploadedFile;

class AdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /** @var CustomRepository $repository */
    protected $repository;

    /** @var CustomValidator $validator */
    protected $validator;

    /** @var CustomService $service */
    protected $service;

    protected $formDataKeySuffix;

    protected $csvFilename = '';

    protected $csvHeaders = [];

    /**
     * @return string
     */
    protected function setFormDataKeySuffix($formDataKeySuffix = null)
    {
        $this->formDataKeySuffix = $formDataKeySuffix;
    }

    /**
     * @return string
     */
    protected function getFormDataKey()
    {
        return getArea() . '_' . getControllerName() . (!empty($this->formDataKeySuffix) ? '_' . $this->formDataKeySuffix : '');
    }

    /**
     * @param $data
     * @return $this
     */
    protected function setFormData($data)
    {
        $primaryKey = $this->repository->getKeyName();
        $this->setFormDataKeySuffix(data_get($data, $primaryKey));
        session()->put([$this->getFormDataKey() => $data]);

        return $this;
    }

    /**
     * @param null $suffix
     * @param bool $clean
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function getFormData($suffix = null, bool $clean = false)
    {
        $this->setFormDataKeySuffix($suffix);
        $data = session()->get($this->getFormDataKey(), []);

        if ($clean) {
            $this->cleanFormData();
        }

        return $data;
    }

    protected function cleanFormData()
    {
        session()->forget($this->getFormDataKey());
    }

    public function index()
    {
        $data = request()->all();
        $record = $this->service->index($data);
        $this->setViewData(['records' => $record,]);

        return $this->render();
    }

    public function create()
    {
        $formData = $this->getFormData('', true);
        $this->setViewData(['record' => $formData]);

        return $this->render();
    }

    public function edit($id)
    {
        $validate = $this->validator->validateShow($id);

        if (!$validate) {
            session()->flash('action_failed', __('messages.no_result_found'));
            return redirect(getBackUrl());
        }

        $formData = $this->getFormData($id, true);
        $record = !empty($formData) ? $formData : $this->repository->find($id)->toArray();
        $this->setViewData(['record' => $record]);

        return $this->render();
    }

    public function show($id)
    {
        $validate = $this->validator->validateShow($id);

        if (!$validate) {
            session()->flash('action_failed', __('messages.no_result_found'));

            return redirect(getBackUrl());
        }

        $record = $this->repository->find($id)->toArray();
        $this->setViewData(['record' => $record]);

        return $this->render();
    }

    public function valid()
    {
        $response = ['status' => true, 'messages' => '', 'data' => []];

        if (!request()->ajax()) {
            return $this->renderJson($response);
        }

        try {
            $params = request()->all();
            $this->_prepareValid($params);
            $this->_hasUploadFile($params);
            $this->setFormData($params);
            $pk = $this->repository->getKeyName();
            $pkValue = !is_null($pk) ? data_get($params, $pk) : '';
            $validate = empty($pkValue) ? $this->validator->validateCreate($params) : $this->validator->validateUpdate($params);
            if (!$validate) {
                $msg = $this->validator->customErrorsBag();
                $response['status'] = false;
                $response['data'] = $msg;
                return $this->renderJson($response);
            }
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            $response['status'] = false;
            $response['messages'] = __('messages.system_error');
            return $this->renderJson($response);
        }

        return $this->renderJson($response);
    }

    public function store()
    {
        $routeIndex = str_replace('.store', '', request()->route()->getName()) . '.index';
        $response = ['status' => true, 'messages' => '', 'data' => [], 'redirect_url' => route($routeIndex)];

        if (!request()->ajax()) {
            return $this->renderJson($response);
        }

        $data = $this->getFormData('', true);

        try {
            $validate = $this->validator->validateCreate($data);
            if (!$validate) {
                logError(__('messages.create_failed') . "(" . json_encode($this->validator->customErrorsBag(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ")");
                $response['status'] = false;
                $response['messages'] = __('messages.create_failed');
                return $this->renderJson($response);
            }
            $this->_prepareBeforeStore($data);
            if (!$this->service->store($data)) {
                $response['status'] = false;
                $response['messages'] = __('messages.create_failed');
                return $this->renderJson();
            }
        } catch (\Exception $exception) {
            logError($exception->getMessage()
                . PHP_EOL .
                json_encode(', Save data (' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ')')
                . PHP_EOL .
                $exception->getTraceAsString()
            );
            $response['status'] = false;
            $response['messages'] = __('messages.system_error');
            return $this->renderJson($response);
        }

        $response['messages'] = __('messages.create_success');
        return $this->renderJson($response);
    }

    public function update($id)
    {
        $routeIndex = str_replace('.update', '', request()->route()->getName()) . '.index';
        $response = ['status' => true, 'messages' => '', 'data' => [], 'redirect_url' => route($routeIndex)];

        if (!request()->ajax()) {
            return $this->renderJson($response);
        }

        $validateShow = $this->validator->validateShow($id);
        if (!$validateShow) {
            logError(__('messages.update_failed') . "(" . json_encode($this->validator->customErrorsBag(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ")");
            $response['status'] = false;
            $response['messages'] = __('messages.update_failed');
            return $this->renderJson($response);
        }

        $data = $this->getFormData($id, true);

        try {
            $validate = $this->validator->validateUpdate($data);
            if (!$validate) {
                logError(__('messages.update_failed') . "(" . json_encode($this->validator->customErrorsBag(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ")");
                $response['status'] = false;
                $response['messages'] = __('messages.update_failed');
                return $this->renderJson($response);
            }
            $this->_prepareBeforeUpdate($data);
            if (!$this->service->update($id, $data)) {
                $response['status'] = false;
                $response['messages'] = __('messages.update_failed');
                return $this->renderJson($response);
            }
        } catch (\Exception $exception) {
            logError($exception->getMessage()
                . PHP_EOL .
                json_encode(', Save data (' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ')')
                . PHP_EOL .
                $exception->getTraceAsString()
            );
            $response['status'] = false;
            $response['messages'] = __('messages.system_error');
            return $this->renderJson($response);
        }

        $response['messages'] = __('messages.update_success');
        return $this->renderJson($response);
    }

    public function destroy($id)
    {
        $routeIndex = str_replace('.destroy', '', request()->route()->getName()) . '.index';
        $response = ['status' => true, 'messages' => '', 'data' => [], 'redirect_url' => route($routeIndex)];

        try {
            $validate = $this->validator->validateShow($id);
            if (!$validate) {
                $response['status'] = false;
                $response['messages'] = __('messages.no_result_found');
                return $this->renderJson($response);
            }

            if (!$this->service->destroy($id)) {
                $response['status'] = false;
                $response['messages'] = __('messages.delete_failed');
                return $this->renderJson($response);
            }
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            $response['status'] = false;
            $response['messages'] = __('messages.system_error');
            return $this->renderJson($response);
        }

        $response['messages'] = __('messages.delete_success');
        return $this->renderJson($response);
    }

    public function downloadCsv()
    {
        $this->service->downloadCsv(request()->all(), $this->csvFilename, $this->csvHeaders);
    }

    /**
     * @param $params
     */
    protected function _hasUploadFile(&$params)
    {
        foreach ($params as $key => $item) {
            if ($item instanceof UploadedFile) {
                $file = BaseStorage::uploadToTmp($item);
                if (!empty($file)) {
                    $params[$key] = $file;
                    $params['is_upload'][] = $key;
                }
            }
        }
    }

    protected function _prepareValid(&$params)
    {
        //
    }

    protected function _prepareBeforeStore(&$data)
    {
        //
    }

    protected function _prepareBeforeUpdate(&$data)
    {
        //
    }
}
