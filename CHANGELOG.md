# Change Log

## [1.0.0] - 2024-01-15

Public release.
- Implemented user authorization, management, backend UI elements.

## [1.0.1] - 2024-01-16

- Updated the build process because the main project was unable to utilize Bootstrap variables within user-specific SCSS files.
- Discontinued Vite publishing in favor of a streamlined approach where Laravel's publish command now only handles asset copying. Users will need to manually include the package's SCSS and JS files.
- While this change introduces a slight inconvenience, it simplifies the integration of custom handlers, routes, etc., offering more customization options.

## [1.0.2] - 2024-01-16

- Further tweaks to make possible integration with the package.
- Added head.blade.php to allow injection code into <head> of the page event when view is rendered completely from inside the package.
- Removed alias from internal inclusion of Bootstrap, as this provides possibility to use shorter path on user side.

## [1.0.3] - 2024-01-16

- Added file uploader component.

## [1.0.4] - 2024-01-17

- Added modal dialog component.

## [1.0.5] - 2024-01-17

- Various package integration fixes.

## [1.0.6] - 2024-01-25

- Added Alert component
- Small bug fixes.

## [1.1.0] - 2024-03-08

- Rewrite how pages and permissions are registered in application. Got rid of config files, now configuration is done via extended service provider.
