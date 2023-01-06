<?php

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Routing\UrlGenerator;
use Collective\Html\HtmlFacade as Html;
use Core\Providers\Facades\Log\ChannelLog;
use Core\Helpers\Device;
use Core\Helpers\Url;
use Core\Providers\Facades\Storages\BaseStorage;

if (!function_exists('disableDebugBar')) {
    /**
     * disable debug bar
     */
    function disableDebugBar()
    {
        \Debugbar::disable();
    }
}

if (!function_exists('getConfig')) {
    /**
     * get config
     *
     * @param $key
     * @param null $default
     * @return Repository|Application|mixed
     */
    function getConfig($key, $default = null)
    {
        return config('config.' . $key, $default);
    }
}

if (!function_exists('getConstant')) {
    /**
     * get config constant
     *
     * @param $key
     * @param null $default
     * @return Repository|Application|mixed
     */
    function getConstant($key, $default = null)
    {
        return config('constant.' . $key, $default);
    }
}

if (!function_exists('getArea')) {
    /**
     * get current area
     * ex: batch, api, admin, web ...
     *
     * @return string|null
     */
    function getArea(): ?string
    {
        $area = 'web';

        if (App::runningInConsole()) {
            return 'batch';
        }

        $requestUri = request()->getRequestUri();
        $uri = explode('/', $requestUri);

        if (!array_key_exists(1, $uri)) {
            return $area;
        }

        $routePrefix = strtok($uri[1], '?');
        $config = getConfig('routes');

        foreach ($config as $key => $item) {
            if ($routePrefix == $item['prefix']) {
                $area = $key;
                break;
            }
        }

        return $area;
    }
}

if (!function_exists('getGuard')) {
    /**
     * @return Guard|StatefulGuard
     */
    function getGuard()
    {
        $area = getArea();
        $guards = config('auth.guards');
        $guards = !empty($guards) ? array_keys($guards) : [];

        if (!empty($guards) && in_array($area, $guards)) {
            return Auth::guard($area);
        }

        // return default if guard not setting or not found
        return Auth::guard();
    }
}

if (!function_exists('getRoute')) {
    /**
     * get route with area
     *
     * @param null $route
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    function getRoute($route = null, $parameters = [], $absolute = true)
    {
        $area = getArea();
        $as = 'web.';
        $config = getConfig('routes');
        foreach ($config as $key => $item) {
            if ($area == $key) {
                $as = $item['as'];
                break;
            }
        }

        if (empty($route)) {
            return route($as . 'home', $parameters, $absolute);
        }

        return route($as . $route, $parameters, $absolute);
    }
}

if (!function_exists('getControllerName')) {
    /**
     * get controller name
     *
     * @return string|null
     */
    function getControllerName(): ?string
    {
        if (empty(Route::getCurrentRoute())) {
            return '';
        }
        $name = Route::getCurrentRoute()->getActionName();
        $controller = explode('@', class_basename($name));
        $controller = reset($controller);
        if (empty($controller)) {
            return '';
        }
        $controller = str_replace(['controller', 'Controller'], '', $controller);
        return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $controller));
    }
}

if (!function_exists('getActionName')) {
    /**
     * get method in controller
     *
     * @return string|null
     */
    function getActionName(): ?string
    {
        if (empty(Route::getCurrentRoute())) {
            return '';
        }
        $method = Route::getCurrentRoute()->getActionMethod();
        return strtolower(preg_replace('/([^A-Z])(A-Z)/', "$1_$2", $method));
    }
}

if (!function_exists('isEnableChatWork')) {
    /**
     * enable | disable push message to chatwork
     * support for write log errors, critical ...
     *
     * @return Repository|Application|mixed
     */
    function isEnableChatWork()
    {
        return config('services.chat_work.is_enable', false);
    }
}


if (!function_exists('isEnableLogSql')) {
    /**
     * @return mixed
     */
    function isEnableLogSql()
    {
        return env('LOG_SQL', false);
    }
}

if (!function_exists('buildVersion')) {
    /**
     * @param $file
     * @return string
     */
    function buildVersion($file): string
    {
        return $file . '?v=' . getConfig('static_version', date('YmdHis'));
    }
}

if (!function_exists('toSql')) {
    /**
     * @param $query
     * @return string
     */
    function toSql($query): string
    {
        return sqlBinding($query->toSql(), $query->getBindings());
    }
}

if (!function_exists('sqlBinding')) {
    /**
     * @param $sql
     * @param $bindings
     * @return string
     */
    function sqlBinding($sql, $bindings): string
    {
        $boundSql = str_replace(['%', '?'], ['%%', '%s'], $sql);

        foreach ($bindings as &$binding) {
            if ($binding instanceof \DateTime) {
                $binding = $binding->format('\'Y-m-d H:i:s\'');
            } elseif (is_string($binding)) {
                $binding = "'$binding'";
            }
        }

        return vsprintf($boundSql, $bindings);
    }
}

if (!function_exists('logDebug')) {
    /**
     * @param $message
     * @param array $context
     * @param string $mode
     * @param string $path
     */
    function logDebug($message, array $context = [], string $mode = 'NASUCTRH', string $path = '')
    {
        $context = array_merge($context, ['mode' => $mode, 'path' => $path]);
        ChannelLog::debug('debug', $message, $context);
    }
}

if (!function_exists('logInfo')) {
    /**
     * @param $message
     * @param array $context
     * @param string $mode
     * @param string $path
     */
    function logInfo($message, array $context = [], string $mode = 'NASUCTRH', string $path = '')
    {
        $context = array_merge($context, ['mode' => $mode, 'path' => $path]);
        ChannelLog::info('info', $message, $context);
    }
}

if (!function_exists('logNotice')) {
    /**
     * @param $message
     * @param array $context
     * @param string $mode
     * @param string $path
     */
    function logNotice($message, array $context = [], string $mode = 'NASUCTRH', string $path = '')
    {
        $context = array_merge($context, ['mode' => $mode, 'path' => $path]);
        ChannelLog::notice('notice', $message, $context);
    }
}

if (!function_exists('logWarning')) {
    /**
     * @param $message
     * @param array $context
     * @param string $mode
     * @param string $path
     */
    function logWarning($message, array $context = [], string $mode = 'NASUCTRH', string $path = '')
    {
        $context = array_merge($context, ['mode' => $mode, 'path' => $path]);
        ChannelLog::warning('warning', $message, $context);
    }
}

if (!function_exists('logError')) {
    /**
     * @param $message
     * @param array $context
     * @param string $mode
     * @param string $path
     */
    function logError($message, array $context = [], string $mode = 'NASUCTRH', string $path = '')
    {
        $context = array_merge($context, ['mode' => $mode, 'path' => $path]);
        ChannelLog::error('error', $message, $context);
    }
}

if (!function_exists('logCritical')) {
    /**
     * @param $message
     * @param array $context
     * @param string $mode
     * @param string $path
     */
    function logCritical($message, array $context = [], string $mode = 'NASUCTRH', string $path = '')
    {
        $context = array_merge($context, ['mode' => $mode, 'path' => $path]);
        ChannelLog::critical('critical', $message, $context);
    }
}

if (!function_exists('logAlert')) {
    /**
     * @param $message
     * @param array $context
     * @param string $mode
     * @param string $path
     */
    function logAlert($message, array $context = [], string $mode = 'NASUCTRH', string $path = '')
    {
        $context = array_merge($context, ['mode' => $mode, 'path' => $path]);
        ChannelLog::alert('alert', $message, $context);
    }
}

if (!function_exists('logEmergency')) {
    /**
     * @param $message
     * @param array $context
     * @param string $mode
     * @param string $path
     */
    function logEmergency($message, array $context = [], string $mode = 'NASUCTRH', string $path = '')
    {
        $context = array_merge($context, ['mode' => $mode, 'path' => $path]);
        ChannelLog::emergency('emergency', $message, $context);
    }
}

if (!function_exists('getClientIp')) {
    /**
     * get Client IP Address
     *
     * @return mixed
     */
    function getClientIp(): mixed
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }
        return $ipaddress;
    }
}

if (!function_exists('getBodyClass')) {
    /**
     * body class
     *
     * @return string
     */
    function getBodyClass(): string
    {
        $area = getArea();
        $controllerName = getControllerName();
        $actionName = getActionName();
        $device = Device::getDevice();
        $os = Device::getOs();
        $browser = Device::getBrowser();

        return 'area-' . (empty($area) ? 'null' : $area)
            . ' c-' . (empty($controllerName) ? 'null' : $controllerName)
            . ' a-' . (empty($actionName) ? 'null' : $actionName)
            . ' device-' . (empty($device) ? 'unknown' : $device)
            . ' os-' . (empty($os) ? 'unknown' : $os)
            . ' browser-' . (empty($browser) ? 'unknown' : $browser);
    }
}

if (!function_exists('loadFiles')) {
    /**
     * load file .css, .js ...
     *
     * @param $files
     * @param string $area
     * @param string $type
     * @return string
     */
    function loadFiles($files, string $area = '', string $type = 'css'): string
    {
        if (empty($files)) {
            return '';
        }

        $result = '';

        foreach ($files as $item) {
            $filePath = str('assets')->append('/' . $type . (!empty($area) ? '/' . $area : '') . '/' . $item . '.' . $type);

            /*
            if (!file_exists(public_path($filePath))) {
                continue;
            }
            */

            $result .= $type == 'css' ? Html::style(asset($filePath)) : Html::script(asset($filePath));
        }

        return $result;
    }
}

if (!function_exists('public_url')) {
    /**
     * get public url
     *
     * @param $url
     * @return mixed|string
     */
    function public_url($url)
    {
        if (str_contains($url, 'http')) {
            return $url;
        }

        $appURL = config('app.url');
        $str = substr($appURL, strlen($appURL) - 1, 1);

        if ($str != '/') {
            $appURL .= '/';
        }

        if (request()->isSecure()) {
            $appURL = str_replace('http://', 'https://', $appURL);
        }

        return $appURL . $url;
    }
}

if (!function_exists('getMediaDir')) {
    /**
     * get media dir
     *
     * @param $file
     * @return mixed
     */
    function getMediaDir($file = null)
    {
        return getConfig('media_dir', 'media') . '/' . $file;
    }
}

if (!function_exists('getTmpUploadDir')) {
    /**
     * get tmp upload dir
     *
     * @param $file
     * @return mixed
     */
    function getTmpUploadDir($file = null)
    {
        return getConfig('tmp_upload_dir', 'tmp_upload') . '/' . $file;
    }
}

if (!function_exists('baseStorageUrl')) {
    /**
     * @param $path
     * @return mixed
     */
    function baseStorageUrl($path)
    {
        return BaseStorage::url($path);
    }
}

if (!function_exists('backUrl')) {
    /**
     * build back url
     *
     * @param $url
     * @param string $default
     * @param array $paramsDefault
     * @param array $params
     * @return UrlGenerator|string
     */
    function backUrl($url, array $params = [], string $default = '', array $paramsDefault = [])
    {
        return Url::backUrl($url, $params, $default, $paramsDefault);
    }
}

if (!function_exists('keepBack')) {
    /**
     * @return string
     */
    function keepBack(): string
    {
        return Url::keepBackUrl();
    }
}

if (!function_exists('getBackUrl')) {
    /**
     * @param bool $fromConfirm
     * @param bool $fullUrl
     * @return mixed|string
     */
    function getBackUrl(bool $fromConfirm = false, bool $fullUrl = true)
    {
        return $fromConfirm ? Url::getOldUrl() : Url::getBackUrl($fullUrl);
    }
}

if (!function_exists('getBackParams')) {
    /**
     * @param false $fromSession
     * @return array|ArrayAccess|mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function getBackParams($fromSession = false)
    {
        $r = request()->get(Url::QUERY);

        if ($fromSession) {
            $urlKeys = session(Url::URl_KEY, []);
            $url = $urlKeys[$r] ?? '';
            $parts = parse_url($url, PHP_URL_QUERY);
            parse_str($parts, $params);

            return data_get($params, Url::QUERY);
        }

        return $r;
    }
}

if (!function_exists('escape_like')) {
    /**
     * escape like
     *
     * @param string $value
     * @param string $char
     * @return string
     */
    function escape_like(string $value, string $char = '\\'): string
    {
        return str_replace(
            [$char, '%', '_'],
            [$char . $char, $char . '%', $char . '_'],
            $value
        );
    }
}

if (!function_exists('is_json')) {
    /**
     * check is json string
     *
     * @param $string
     * @return bool
     */
    function is_json($string)
    {
        try {
            json_decode($string);
            return json_last_error() === JSON_ERROR_NONE;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
