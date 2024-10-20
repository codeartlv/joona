Joona is a utility package for Laravel, designed to streamline backend development. It includes features such as user management, permission settings, and an activity log. On the frontend, it integrates Bootstrap 5 and additional UI components to enhance interface design. This package offers a quick starting point for projects that require an administration interface.

Unlike other admin panels (such as Filament), Joona promotes the creation of highly customized user interfaces by not restricting developers to predefined views for content output.

## Requirements

This package is tailored for seamless integration with a brand-new Laravel 11.x installation but can also be incorporated into existing projects. Included in the package are migrations essential for creating tables. However, it's important to note that if your current project already contains tables sharing the same names, potential conflicts will arise.

## Installation

1. Require the package via Composer: `composer require codeartlv/joona`
2. Publish assets: `php artisan joona:publish`. This exports UI assets into `public/vendor/joona` directory and registers service provider.
3. If using Laravel < 11, add `App\Providers\JoonaServiceProvider::class,` service provider in `config/app.php`.
4. Run migrations: `php artisan migrate`
5. Seed defaults: `php artisan joona:seed`
6. To support extending CSS/JS within your project, add dependency in your files:

Your SCSS file:

```scss
/* Import base config */
@import '@joona/scss/config.scss';

/**
* You can override theme settings here
*/

/* Import main stylesheet */
@import '@joona/scss/main.scss';

// At this point, include your custom SCSS files. All Bootstrap mixins and variables are available.
```

Your Javascript entry point:

```javascript
import Joona from '@joona/js/main.js';

// Add your JS code.

Joona.ready();
```

If your are using Vite, the basic setup could look like this:

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/js/app.js', 'resources/scss/main.scss'],
			refresh: true,
		}),
	],
	resolve: {
		alias: {
			'@joona': path.resolve(__dirname, 'vendor/codeartlv/joona/resources/assets'),
			'@joona-modules': path.resolve(__dirname, 'vendor/codeartlv/joona/node_modules'),
		},
	},
});
```

After defining CSS/JS files, build the project:

```
npx vite build
```

By now, the setup should be complete. Navigate to `/admin` and log in using the following credentials:

Email: **admin@localhost**
Password: **password**

It's important to note that the actual password policy is stricter. You are advised to change the default user password, as the required password complexity will be higher.

### Custom CSS/JS location

If you have customized location of (S)CSS/JS files, update it in `app/Providers/JoonaServiceProvider.php`:

```php

class JoonaServiceProvider extends JoonaPanelProvider
{

	protected function configure(Panel $panel): void
	{
		$panel
			->addViteResources([
				'resources/backend/scss/main.scss',
				'resources/backend/js/main.js'
			]
		);
	}

}
```

## Usage

The package introduces several middlewares and templates to integrate into backend interface. It uses custom `auth` guard and users are authenticated against database.

### Create routes inside backend

Within the backend interface, there are both authenticated and unauthenticated routes. Authenticated routes require user authentication, while unauthenticated routes don't, but they apply additional settings such as color theme, locale, etc. To incorporate unauthenticated routes, apply the `admin.web` middleware to your routes:

```php
Route::middleware(['admin.web'])->group(function(){
    Route::get('/set-region', [ExampleController::class, 'action']);
});
```

To add authenticated routes, use middleware `admin.auth`:

```php
Route::middleware(['admin.auth'])->group(function(){
    Route::get('/blog', [ExampleController::class, 'action']);
});
```

Note that this still requires `admin.web` middleware to provide basic settings. You can create a group for your backend routes:

```php
Route::middleware(['admin.web'])->group(function(){
    // Unauthenticated routes
    Route::middleware(['admin.auth'])->group(function () {
        // Authenticated routes
    });
});
```

Proceed to write controllers as you usually do.

### Views

The package provides two layouts - a simple layout and layout with sidebar. Sidebar layout is used to add various data filtering controls.

#### Simple view

To use simple view, you would write your template like this:

```
<x-content :title="Page title">
    <x-slot name="sidebar">
	    Sidebar controls (optional)
    </x-slot>

    The main content goes here

	<x-slot name="footer">
	    The footer of the page (optional). Add additional controls here
	    like paginator etc.
	</x-slot>

	<x-slot name="controls">
	    This block resides at the header of page (optional).
	    Add CTA button here.
	</x-slot>
</x-content>
```

#### Including metadata

You will likely need to inject additional data into `<head>` even when the view is completely rendered from inside the package.
Just create `resources/views/vendor/joona/head.blade.php` file and Laravel will include it inside the `<head>`.

### Routes

The package uses Ziggy to provide routes inside Javascript. Just call function `route` and use it the same way as in Laravel.

## Javascript UI components

The package provides several commonly used components that can be included in the views.

### Modal dialog

A modal dialog that is loaded by remote request.

```javascript
import Modal from '@joona/js/components/modal.js';
let modalDialog = new Modal();

modalDialog
	.open('/page', {
		animations: true,
	})
	.then(() => {
		// modal is opened
		// close by calling modalDialog.close();
	});
```

### Confirmation dialog

```javascript
import ConfirmDialog from '@joona/js/components/confirm-dialog.js';

const caption = 'Confirm';
const message = 'Are you sure you want to delete this record?';
const buttons = [
	{
		caption: 'Cancel',
		role: 'secondary',
		callback: () => {
			// Optional, execute on "Cancel" action
		},
	},
	{
		caption: 'Yes',
		role: 'primary',
		callback: () => {
			// Execute "Yes" action
		},
	},
];

let confirmDialog = new ConfirmDialog(caption, message, buttons);
```

Confirmation dialog closes automatically once any of the buttons is pressed.

### Paginator

Outputs a paginator component. Pass `total` (total number of rows) and `size` (number of items on page).

```html
<x-paginator :total="$total" :size="$size" />
```

### Form

Creates a dynamic form. Form is submitted via Ajax.

```html
<x-joona-form method="post" action="" class="">
	<!-- Form elements -->
	<x-joona-form></x-joona-form
></x-joona-form>
```

When providing response to the form submission, use `FormResponse` class:

```php
use Codeart\Joona\View\Components\Form\FormResponse;

// Inside your controller
$form = new FormResponse();

// Form submitted successfully
$form->setSuccess('Data saved!');

// Set error on field
$form->setError('Value required.', 'name');

// Add action on a form. The action gets executed only if there are no errors.
// Multiple actions can be added.

$form->setAction('reload', true); // Reload page
$form->setAction('redurect', '/home'); // Redirect user to the URL
$form->setAction('close_popup', true); // Closes opened modal dialog
$form->setAction('reset', true); // Resets form to default state.

// Attaching additional data
$form->addData(['id'  =>  1]);

// Render form
return response()->json($form);
```

When setting error on the form field, any input with provided name gets searched. If empty field name is provided, the message is rendered into dedicated alert component. If form element can't have a specific name, you can defined where the form error gets rendered by adding:

```html
<div data-field="name"></div>
```

### Button

Displays a button.

```html
<x-button
	caption="Submit"
	type="submit"
	role="primary"
	icon="check"
	:attr="['custom-attribute'  =>  'yes']" />
```

### Alert

Displays an alert message.

```html
<x-alert role="info" message="Hello World!" />
```

### Dialog

Should be included when outputting a dialog content.

```html
<x-dialog :caption="Caption">
	<p>Dialog content</p>
	<x-slot name="footer"> Optional footer </x-slot>
</x-dialog>
```

Dialog example with form and save button:

```html
<x-form :action="route('blog.save')">
	<x-dialog :caption="Edit post">
		<div>
			<div data-role="form.response"></div>
			<!-- Form fields -->
		</div>

		<x-slot name="footer">
			<x-button :caption="Save" icon="check" />
		</x-slot>
	</x-dialog>
</x-form>
```

### Uploader

Creates a file uploader. When setting uploaded files, use instance of `Codeart\Joona\View\Components\Form\UploadedFile`.
When uploading file, return response of the same class.

```html
<x-uploader
	uploadroute="files.upload"
	deleteroute="files.delete"
	limit="5"
	submitbtn="#test-button"
	:files="$files"></x-uploader>
```

## Writing custom components

Backend features a very simple JS framework to separate view from JS code. It utilizes `data` attributes on HTML elements to which every component is binded. You are not required to use this, but for simple interactions it can be faster than to deploy React/Vue etc. framework. It's up to you anyway.

#### Create new component handler

First of all, if you need to add interaction to element, you create a Javascript class that collects a group functions for a problem domain. Let's say you have a blog and need to add component in the backend. Start by creating blog component handler:

```javascript
// resources/js/blog.js

import Handler from '@joona/js/handler';

export default class Blog extends Handler {
	static get pluginName() {
		return 'blog';
	}
}
```

Register handler at the application:

```javascript
// resource/js/app.js

import Blog from 'blog';

// Add your custom handlers
Joona.addHandlers(Blog);
```

The `pluginName` is an only required function for a handler. Now, let's say you need to create functionality around some HTML code. You start by referencing the handler through `data` attributes:

```html
<div data-bind="blog.edit-form" data-id="1"></div>
```

Here, `data-bind` consists of two parts - first is the name of handler (the value that `pluginName` returns) and second is the name of the handler function. Based on this example, we can write handler like this:

```javascript
// resources/js/blog.js
export default class Blog extends Handler {
	static get pluginName() {
		return 'blog';
	}

	editForm(element, parameters, runtime) {
		// add your functionality
		// element is HTML Node
		// parameters contains {"id":1}
	}
}
```

The handler name gets converted to camel case. `edit-form` becomes `editForm`.
Each handler function receives 3 arguments:
**_element_** - reference to DOM node where `data-bind` is assigned on;
**_parameters_** - any additional `data` arguments on the node;
**_runtime_** - instance of Runtime class.

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
	static get pluginName() {
		return 'blog';
	}

	userComponent(element, parameters, runtime) {
		return new (function () {
			this.hello = (name) => {
				alert(name);
			};
		})();
	}

	editForm(element, parameters, runtime) {
		runtime.getInstance(element, 'blog.user-component').then((userComponent) => {
			userComponent.hello('Bob');
		});
	}
}
```

`window.Runtime.getInstance` searches for binded component within provided scope (`element`), the second argument is full name of the handler. Note that component must return a function or object to be resolved.

`window.Runtime.getInstance` returns the first found component. If you presume that there can be many instances, use `runtime.getInstances`.

If you want to get instance by element's id, then use `runtime.getInstanceById(id)`.

#### Dynamically binding handler

If you load your content dynamically, after inserting new nodes into the DOM, call `window.Runtime.init(context)` on the new HTML nodes. `context` is the most highest DOM node you can reference that contains new HTML.

## Commands

Package adds additional console commands. Please see description of each of them:

`joona:seed` - creates default credentials. After running this seeds, you can authorize in backend panel with email `admin@localhost` and password `password`.
`joona:publish` - publishes backend template assets and related packages assets.

## Notes

This package adds `admin` guard to `auth.php` and `joona` guard to configuration. Please consider this naming when adding additional guards and/or providers.
