# Change Log

## [1.0.0] - 2024-01-15

Public release. Implemented user authorization, management, backend UI elements.

## [1.0.1] - 2024-01-16

Updated the build process because the main project was unable to utilize Bootstrap variables within user-specific SCSS files.
Discontinued Vite publishing in favor of a streamlined approach where Laravel's publish command now only handles asset copying. Users will need to manually include the package's SCSS and JS files.
While this change introduces a slight inconvenience, it simplifies the integration of custom handlers, routes, etc., offering more customization options.
