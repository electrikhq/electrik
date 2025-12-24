<?php

return [
    /*
    |--------------------------------------------------------------------------
    | View Name
    |--------------------------------------------------------------------------
    |
    | Choose a view to display when Breadcrumbs::render() is called.
    | Built in templates are:
    |
    | - 'tailwind' - Tailwind CSS styling
    | - 'bootstrap5' - Bootstrap 5 styling
    | - 'bootstrap4' - Bootstrap 4 styling
    |
    | Or a custom view: 'view.name'
    |
    */

    'view' => 'vendor.breadcrumbs.tailwind',

    /*
    |--------------------------------------------------------------------------
    | Breadcrumbs File(s)
    |--------------------------------------------------------------------------
    |
    | The file(s) where breadcrumbs are defined. e.g.:
    |
    | base_path('routes/breadcrumbs.php')
    |
    | or for multiple files:
    |
    | [
    |     base_path('routes/breadcrumbs.php'),
    |     base_path('routes/breadcrumbs/admin.php'),
    | ]
    |
    */

    'files' => base_path('routes/breadcrumbs.php'),

    /*
    |--------------------------------------------------------------------------
    | Exceptions
    |--------------------------------------------------------------------------
    |
    | Determine when to throw an exception.
    |
    */

    // When route-bound breadcrumbs are used but the current route doesn't have a name (UnnamedRouteException)
    'unnamed-route-exception' => true,

    // When route-bound breadcrumbs are used and the matching breadcrumb doesn't exist (InvalidBreadcrumbException)
    'missing-route-bound-breadcrumb-exception' => true,

    // When a named breadcrumb is used but doesn't exist (InvalidBreadcrumbException)
    'invalid-named-breadcrumb-exception' => true,

    /*
    |--------------------------------------------------------------------------
    | Classes
    |--------------------------------------------------------------------------
    |
    | Subclass the default classes for more advanced customisations.
    |
    */

    // Manager
    'manager-class' => Diglactic\Breadcrumbs\Manager::class,

    // Generator
    'generator-class' => Diglactic\Breadcrumbs\Generator::class,

];

