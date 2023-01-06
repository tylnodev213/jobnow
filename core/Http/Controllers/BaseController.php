<?php

namespace Core\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected array $viewData = [];

    protected string $title = '';

    public function __construct()
    {
        //
    }

    /**
     * @param $data
     */
    public function setViewData($data)
    {
        $this->viewData = array_merge($this->viewData, (array)$data);
    }

    /**
     * @param null $item
     * @return array|mixed
     */
    public function getViewData($item = null)
    {
        if (!is_null($item)) return data_get($this->viewData, $item);

        return $this->viewData;
    }

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null $view
     * @param array $data
     * @param array $mergeData
     * @return Application|Factory|View
     */
    public function render($view = null, array $data = [], array $mergeData = [])
    {
        $area = getArea();
        $tmp = !empty($area) ? $area . '.' : '';
        $view = str($tmp)->append(!empty($view) ? $view : getControllerName() . '.' . getActionName());
        $data = array_merge($data, $this->getViewData(), [
            'title' => $this->getTitle(),
        ]);

        return view($view, $data, $mergeData);
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return JsonResponse
     */
    public function renderJson(array $data = [], int $status = 200, array $headers = [], int $options = 0)
    {
        return response()->json($data, $status, $headers, $options);
    }
}
