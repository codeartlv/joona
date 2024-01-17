
# Joona
Joona is a utility package for Laravel, designed to enhance backend development. It features user management, permission settings, and an activity log. The frontend integrates Bootstrap 5 and additional UI components for improved interface design. It provides a quick way to kickstart any project that requires administration interface.

## Requirements
This package is tailored for seamless integration with a brand-new Laravel 10.x installation but can also be incorporated into existing projects. Its setup is facilitated by automatic package discovery. Included in the package are migrations essential for creating tables. However, it's important to note that if your current project already contains tables sharing the same names, potential conflicts might arise.

## Installation

1. Require the package via Composer: `composer require codeartlv/joona`
2. Publish assets: `php artisan joona:publish`. Note that this exports UI assets into `public/vendor/joona` directory. If you build your assets in pipeline or production, you should gitignore this directory. Also this creates `config/joona.php` configuration file. Configuration options will be discussed later.
3. Run migrations: `php artisan migrate`
4. Seed defaults: `php artisan joona:seed`
5. Include CSS and JS into your project:

```scss
@import "@joona/scss/app";
```

```javascript
import { Joona } from '@joona/js/app.js';

// Add your custom handlers
Joona.addHandlers();

// Add your custom translations
Joona.addTranslations();

// Add your custom routes
Joona.addRoutes();

// Bootstrap app
Joona.ready();
```

By now, the setup should be complete. Navigate to `/admin` and log in using the following credentials:
Username: **admin**
Password: **password**

It's important to note that the actual password policy is stricter. You are advised to change the default user password, as the required password complexity will be higher.

## Usage
The package introduces several middlewares and templates to integrate into backend interface. It uses custom `auth` guard and users are authenticated against database.

### Configuration settings
Inside your published `config/joona.php` there are several settings which can be adjusted to match your needs:
`app_logo` - provide URL to logo image to match your branding.
`locales` - lists available locales for the backend. Remove the ones you don't need.
`admin_password_policy` - specify policy for passwords. Possible options are documented inside the config file.

Application title which is used inside backend is taken from `app.name` configuration setting.

### Create routes inside backend
Within the backend interface, there are both authenticated and unauthenticated routes. Authenticated routes require user authentication, while unauthenticated routes don't but they apply additional settings such as color theme, locale, etc. To incorporate unauthenticated routes, apply the `admin.web` middleware to your routes:

```php
Route::middleware(['admin.web'])->prefix('/admin')->group(function(){
    Route::get('/set-region', [ExampleController::class, 'action']);
});
```
To add authenticated routes, use middleware `admin.auth`:

```php
Route::middleware(['admin.auth'])->prefix('/admin')->group(function(){
    Route::get('/blog', [ExampleController::class, 'action']);
});
```
Note that this still requires `admin.web` middleware to provide basic settings. You can create a group for your backend routes:

```php
Route::middleware(['admin.web'])->prefix('/admin')->group(function(){
    // Unauthenticated routes
    Route::middleware(['admin.auth'])->group(function () {
        // Authenticated routes
    });
});
```
Proceed to write controllers as you usually do. The URL of the administration interface starts with `/admin`. Almost all package provided URLs reside within `/admin/panel`.

### Views
The package provides two layouts - a simple layout and layout with sidebar. Sidebar layout is used to add various data filtering controls. Sidebar layout is made responsive and can be used on mobile devices.

#### Simple view
To use simple view, you would write your template like this:
```
@extends('joona::simple')

@section('page_title', 'Page title')

@section('main')
     The main content goes here
@endsection

@section('foot')
     The footer of the page. Add additional controls here like paginator etc.
@endsection

@section('controls')
     This block resides at the header of page. Add CTA button here.
@endsection
```
Not all sections need to be filled, but the main content should be in section `main`.

#### Sidebar layout
The block layout is the same as in simple layout, but sidebar content has dedicated block:

```
@section('sidebar')
     Sidebar content goes here
@endsection
```

#### Including metadata
You will likely need to inject additional data into <head> even when the view is completely rendered from inside the package.
Just create `resources/views/vendor/joona/head.blade.php` file and Laravel will include it inside the <head>

### Working with UI components
Backend features a very simple JS framework to separate view from JS code. It utilizes `data` attributes on HTML elements to which every component is binded. You are not required to use this, but for simple interactions it can be faster than to deploy React/Vue etc. framework. It's up to you anyway.

#### Create new component handler
First of all, if you need to add interaction you create a JS class that collects a group functions for a problem domain. Let's say you have a blog and need to add component in the backend. Start by creating blog component handler:
```javascript
// resources/js/blog.js

import Handler from  '@joona/js/handler';

export default class Blog extends Handler {
    static  get  pluginName() {
        return  'blog';
    }
}
```
*Note: If you use Vite, jo can alias `@joona` like this:*
```javascript
export default defineConfig({
    resolve: {
        alias: {
            '@joona': path.resolve(__dirname, 'vendor/codeartlv/joona/resources/assets'),
        },
    },
});
```
The `pluginName` is an only required function for a handler.  Now, let's say you need to create functionality around some HTML code. You start by referencing the handler through `data` attributes:
```html
<div data-bind="blog.edit-form" data-id="1">

</div>
```
Here, `data-bind` consists of two parts - first is the name of handler (the value that `pluginName` returns) and second is the name of the handler function. Based on this example, we can write handler like this:
```javascript
// resources/js/blog.js
export default class Blog extends Handler {
    static  get  pluginName() {
        return  'blog';
    }

    editForm(element, parameters, runtime) {
        // add your functionality
        // element is HTML Node
        // parameters contains {"id":1}
    }
}
```
Each handler function receives 3 arguments:
***element*** - reference to DOM node where `data-bind` is assigned on;
***parameters*** - any additional `data` arguments on the node;
***runtime*** - instance of Runtime class.

#### Getting instances of other components
If handler function returns and object, it is stored for later access. This way you can get other binded components from anywhere on the page or specific scope. Consider that you have two handler functions:
```html
<div data-bind="blog.edit-form">
    <div data-bind="blog.user-component"></div>
</div>
```
```javascript
// resources/js/blog.js
export default class Blog extends Handler {
    static  get  pluginName() {
        return  'blog';
    }
    
    userComponent(element, parameters, runtime) {
        return new function(){
            this.hello = (name) => {
                alert(name);
            };
        }();
    }
    
    editForm(element, parameters, runtime) {
        runtime.getInstance(element, 'blog.user-component').then((userComponent) => {
            userComponent.hello('Bob');
        });
    }
}
```
`runtime.getInstance` searches for binded component within provided scope (`element`), the second argument is full name of the handler. Note that component must return a function or object to be resolved.
`runtime.getInstance` returns the first found component. If you presume that there can be many instances, use `runtime.getInstances`.

#### Dynamically binding handler
If you load your content dynamically, after inserting new nodes into the DOM, call `Runtime.init(context)` on the new HTML nodes. `context` is the most highest DOM node you can reference that contains new HTML.

## Javascript UI components
The package provides several commonly used components that can be included in the views.

### Modal dialog
```javascript
import Modal from  '@joona/js/components/modal.js';

let modalDialog = new Modal();
modalDialog.open('/page', {
    animations : true,
}).then(() => {
    // modal is opened
    // close by calling modalDialog.close();
});
```
### Confirmation dialog
```javascript
import ConfirmDialog from  '@joona/js/components/confirm-dialog.js';

const caption = 'Confirm';
const message = 'Are you sure you want to delete this record?';
const buttons = [
    {caption:'Cancel', role:'secondary', callback:()=>{}},
    {caption:'Yes', role:'primary', callback:()=>{}},
];

let confirmDialog = new ConfirmDialog(caption, message, buttons);
```
Confirmation dialog closes automatically once any of the buttons is pressed.

## HTML components

### Paginator
Outputs a paginator component. Pass `total` (total number of rows) and `size` (number of items on page).
```html
<x-joona-paginator :total="$total" :size="$size" />
```
### Form
Creates a dynamic form. Form is submitted via Ajax.
```html
<x-joona-form  method="post" action="" class="">
    <!-- Form elements -->
<x-joona-form>
```
### Button
```html
<x-joona-button caption="Submit" type="submit" role="primary" icon="check" :attr="['custom-attribute'  =>  'yes']" />
```

### Dialoag
```html
<x-joona-dialog :caption="Caption">
	<p>Dialog content</p>

	<x-slot name="footer">
		Optional footer
	</x-slot>
</x-joona-dialog>
```

Dialog example with form and save button:
```html
<x-joona-form :action="route('blog.save')">
	<x-joona-dialog :caption="Edit post">

		<div data-bind="blog.post-edit-form">
			<div data-role="form.response"></div>

			<!-- Form fields -->
		</div>

		<x-slot name="footer">
			<x-joona-button :caption="Save" icon="check" />
		</x-slot>
	</x-joona-dialog>
</x-joona-form>

```
### Uploader

Creates a file uploader. When setting uploaded files, use instance of `Codeart\Joona\View\Components\Form\UploadedFile`.
When uploading file, return response of the same class.

```html
<x-joona-uploader uploadroute="files.upload" deleteroute="files.delete" limit="5" submitbtn="#test-button" :files="$files"></x-joona-uploader>
```

## Commands

Package adds additional console commands. Please see description of each of them:

`joona:seed` - creates default credentials. After running this seeds, you can authorize in backend panel with username `admin` and password `password`.
`joona:publish` - publishes backend template assets and related packages assets.

Please add `$schedule->command('joona:update-session')->everyTenMinutes();` in your scheduler. This will make updating admin user session data more precise.

## Notes

This package adds `admin` guard to `auth.php` and `joona` guard to configuration. Please consider this naming when adding additional guards and/or providers.
