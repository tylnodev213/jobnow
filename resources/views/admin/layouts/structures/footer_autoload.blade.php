@php
    $area = getArea();
    $controllerName = getControllerName();
@endphp

@if (isset($controllerName) && !empty($controllerName) && file_exists(public_path('assets/js/' . $area . '/autoload/' . $controllerName . '.js')))
    {{  Html::script(buildVersion(asset('assets/js/' . $area . '/autoload/' . $controllerName . '.js'))) }}
@endif

@if (isset($controllerName) && !empty($controllerName) && file_exists(public_path('assets/js/' . $area . '/webpack/' . $controllerName . '.js')))
    {{ Html::script(buildVersion(asset('assets/js/' . $area . '/webpack/' . $controllerName . '.js'))) }}
@endif
