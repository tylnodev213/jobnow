<?php

namespace Core\Helpers;

use Illuminate\Support\Facades\URL as FacadesURL;

class Url
{
    protected static $currentControllerName = null;

    /**
     * @var Url|null
     */
    protected static $instance = null;

    /**
     * @var int
     */
    protected $old = 0;

    /**
     *
     */
    const URl_KEY = 'url_key';

    /**
     *
     */
    const QUERY = '_o';

    /**
     *
     */
    const OLD_QUERY = '_o_';

    const BACK_URL_LIMIT = 200;

    /**
     * @return Url|null
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return null
     */
    public static function getCurrentControllerName()
    {
        return self::$currentControllerName;
    }

    /**
     * @param null $currentControllerName
     */
    public static function setCurrentControllerName($currentControllerName)
    {
        self::$currentControllerName = $currentControllerName;
    }


    /**
     * @param Url|null $instance
     */
    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }

    /**
     * @param $default |null
     * @param $params |null
     * @return int
     */
    public static function genUrlKey($default = '', $params = [])
    {
        $url = static::getFullUrl($default, $params);
        $urlKeys = session(self::URl_KEY, []);
        global $urlIdx;
        $urlIdx++;
        $time = time() . $urlIdx;
        krsort($urlKeys, SORT_STRING);

        if (!empty($urlKeys)) {
            $limit = self::BACK_URL_LIMIT;
            $urlKeys = array_chunk($urlKeys, $limit - 1, true);
            $urlKeys = $urlKeys[0];
        }

        $urlKeys[$time] = $url;
        session([self::URl_KEY => $urlKeys]);

        return $time;
    }

    protected static function getFullUrl($default = '', $params = [])
    {
        if ($default) {
            $url = str_contains($default, '.') ? route($default, $params) : $default;
            $url = parse_url($url);
            $r = $url['path'] ?? '';
            return isset($url['query']) && $r ? $r . '?' . $url['query'] : $r;
        }

        $router = app('router');
        $request = request()->all();
        $inputs = static::buildParamString($request);
        $uri = $router->getCurrentRoute()->uri;

        foreach ($router->getCurrentRoute()->parameters as $parameter => $value) {
            $uri = str_replace('{' . $parameter . '}', $value, $uri);
        }

        return $uri . $inputs;
    }

    protected static function buildParamString($params, $params1 = [])
    {
        $params = array_merge($params1, $params);
        $params = http_build_query($params);
        return $params ? '?' . $params : '';
    }

    public static function getBackUrl($full = true, $defaultUrl = '')
    {
        $old = request()->get(self::QUERY, false);

        if (!$old) {
            return !empty($defaultUrl) ? $defaultUrl : url()->previous();
        }

        $urlKeys = session(self::URl_KEY, []);
        $url = $urlKeys[$old] ?? $defaultUrl;

        return $full ? url($url) : $url;
    }

    /**
     * @param $url
     * @param $default
     * @param array $params
     * @param array $paramsDefault
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public static function backUrl($url, $params = [], $default = '', $paramsDefault = [])
    {
        $old = self::genUrlKey($default, $paramsDefault);
        $params = array_merge((array)$params, [self::QUERY => $old]);

        if (str_contains($url, '/')) {
            return url($url, $params);
        }

        return route($url, $params);
    }

    protected static function getOldKey()
    {
        return static::getCurrentControllerName() . self::OLD_QUERY;
    }

    public static function getOldUrl()
    {
        return session(static::getOldKey(), '');
    }

    public static function collectOldUrl()
    {
        session([static::getOldKey() => FacadesURL::previous()]);
    }

    public static function keepBackUrl($value = null)
    {
        $value = $value ? $value : request()->get(self::QUERY, '');

        return '<input type="hidden" name="' . self::QUERY . '" value="' . $value . '">';
    }
}
