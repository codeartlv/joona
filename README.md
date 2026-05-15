# Joona

Joona is a Laravel admin panel package with user management, roles and permissions, activity logging, and a Bootstrap 5–based UI layer. Unlike opinionated admin builders, it does not force predefined CRUD screens—you build your own Blade views and routes while reusing layouts, form helpers, and JavaScript components.

## Requirements

- PHP 8.2+
- Laravel 11.x (recommended for new projects; the package can be added to existing apps)
- Node.js and npm (for compiling your Vite assets)
- Composer

The package ships database migrations. If your application already has tables with the same names (`admin_users`, `roles`, etc.), resolve naming conflicts before migrating.

## Installation

### 1. Install the package

```bash
composer require codeartlv/joona
```

Laravel auto-discovers `Codeart\Joona\Providers\JoonaProvider`, which registers migrations and Artisan commands.

### 2. Publish assets and application provider

```bash
php artisan joona:publish
```

This command:

- Runs `npm install` inside the package asset directory (`vendor/codeartlv/joona/resources/assets`)
- Publishes flag images to `public/vendor/joona`
- Publishes `config/joona.php`
- Publishes `app/Providers/JoonaServiceProvider.php` (extends `JoonaPanelProvider`)
- Registers `JoonaServiceProvider` in `bootstrap/providers.php` (Laravel 11+)

On Laravel 10 and below, add `App\Providers\JoonaServiceProvider::class` to `config/app.php` manually if it was not added automatically.

### 3. Run migrations and seed

```bash
php artisan migrate
php artisan joona:seed
```

Default login (change immediately in production):

| Field    | Value              |
|----------|--------------------|
| URL      | `/admin`           |
| Email    | `admin@localhost`  |
| Password | `password`         |

The seeded password satisfies the default policy in `config/joona.php` (`min:8,max:20,mixed,number,special`). Stricter rules apply when users change passwords through the UI.

## Frontend setup (Vite)

Joona does not ship pre-built CSS/JS for your app. You compile your own entry files and point the panel at them via `addViteResources()`.

### SCSS entry

Create e.g. `resources/scss/admin.scss`:

```scss
/* Theme variables (Bootstrap overrides) */
@import '@joona/scss/config.scss';

/* Your overrides here */

/* Package styles (Bootstrap, components, vendors) */
@import '@joona/scss/main.scss';

/* Your custom styles */
```

### JavaScript entry

Create e.g. `resources/js/admin.js`:

```javascript
import Joona from '@joona/js/main.js';

// Optional: register custom handlers
// import Blog from './handlers/blog.js';
// Joona.addHandlers(Blog);

Joona.ready();
```

`main.js` exports the runtime singleton as `window.Joona`. Call `Joona.ready()` once to bind `data-bind` handlers, load translations, and initialize built-in components.

### Vite configuration

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/admin.js', 'resources/scss/admin.scss'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@joona': path.resolve(__dirname, 'vendor/codeartlv/joona/resources/assets'),
            '@joona-modules': path.resolve(__dirname, 'vendor/codeartlv/joona/resources/assets/node_modules'),
        },
    },
});
```

Run `npm install` in your Laravel project, then build:

```bash
npm run build
# or during development:
npm run dev
```

### Register Vite entries in the panel

Edit `app/Providers/JoonaServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Codeart\Joona\MetaData\Locale;
use Codeart\Joona\MetaData\Page;
use Codeart\Joona\Panel;
use Codeart\Joona\Providers\JoonaPanelProvider;

class JoonaServiceProvider extends JoonaPanelProvider
{
    protected function configure(Panel $panel): void
    {
        $panel
            ->setBasePath('/admin') // optional, default is /admin
            ->setLocales([
                new Locale('English', 'en', 'us'),
            ])
            ->addViteResources([
                'resources/scss/admin.scss',
                'resources/js/admin.js',
            ])
            ->addPages([
                Page::make('blog')
                    ->route('blog.index')
                    ->caption('Blog')
                    ->icon('article'),
            ]);
    }
}
```

Layouts load these files with `@vite($vite_resources)` in `resources/views/global.blade.php`.

### Custom `<head>` / body snippets

Publish or create:

- `resources/views/vendor/joona/head.blade.php`
- `resources/views/vendor/joona/body.blade.php`

They are included from the main layout automatically.

## Panel configuration

Configure the panel in `JoonaServiceProvider::configure()` using the `Panel` instance:

| Method | Description |
|--------|-------------|
| `setBasePath(string $path)` | URL prefix for the admin area (default `/admin`) |
| `setBaseDomain(string $domain)` | Optional dedicated domain |
| `setAppName(string $name)` | Title shown in the UI |
| `setLogo(string $light, ?string $dark, ?string $icon)` | Logo URLs |
| `setLocales(Locale[] $locales)` | Language switcher entries |
| `useRolesAndPermissions(bool $state)` | Enable/disable RBAC (default `true`) |
| `addViteResources(array $paths)` | Vite entry files |
| `addPages(Page[] $pages)` | Sidebar navigation |
| `addPermissions(array $groups)` | Custom permission groups |
| `setPermissionLoader(string $class)` | Custom permission loader class |
| `addUserClasses(array $levels)` | Extra user level enums/classes |
| `addNotifications(array $classes)` | Notification handler classes |
| `addRoutes('free' \| 'secure', callable\|string $routes)` | Register route files or closures |

### Navigation pages

```php
use Codeart\Joona\MetaData\Page;

Page::make('settings.blog')   // nested ID: settings → blog
    ->route('blog.index')
    ->caption('Blog posts')
    ->icon('article')
    ->badge(3)                // int or callable
    ->activeRoutes(['blog.edit', 'blog.create']);
```

Top-level IDs appear in the sidebar; dotted IDs nest under the first segment.

### Locales

```php
use Codeart\Joona\MetaData\Locale;

new Locale('Latviešu', 'lv', 'lv'); // caption, locale code, flag file key
```

Flag SVGs are served from `public/vendor/joona/images/flags/{map}.svg`.

## Authentication and guards

Joona merges an `admin` guard and `joona` user provider into `config/auth.php`:

- Guard: `admin` (session driver)
- Provider model: `Codeart\Joona\Models\User\AdminUser`
- Facade helper: `joona.auth` resolves `Auth::guard('admin')`

Use `Auth::guard('admin')` or the `joona.auth` binding in your code. Do not confuse this with Laravel’s default `web` guard.

## Routes and middleware

Package routes are registered under `Panel::getBasePath()` with the `web` and `admin.web` middleware groups. Authenticated package routes also use `admin.auth`.

| Middleware group | Purpose |
|------------------|---------|
| `admin.web` | Locale, theme, activity logging |
| `admin.auth` | Requires admin login; optional permission checks |
| `userclass` | Alias for `CheckUserClass` middleware |

### Adding your own admin routes

Register routes in `JoonaServiceProvider::configure()`:

```php
$panel->addRoutes('secure', function () {
    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
});

$panel->addRoutes('free', function () {
    Route::get('/public-report', [ReportController::class, 'show'])->name('report.public');
});
```

Or pass a route file path:

```php
$panel->addRoutes('secure', base_path('routes/admin.php'));
```

- **`secure`** — wrapped in `admin.auth` (must be logged in).
- **`free`** — only `web` + `admin.web` (e.g. login pages you add yourself).

Equivalent manual grouping:

```php
Route::middleware(['admin.web'])->group(function () {
    // Guest-accessible admin routes (theme, locale, etc.)

    Route::middleware(['admin.auth'])->group(function () {
        // Authenticated admin routes
    });
});
```

Named routes are available in JavaScript via [Ziggy](https://github.com/tighten/ziggy) (`@routes` in the layout). Use the global `route()` helper the same way as in PHP.

## Views and layouts

### Page layout (`<x-content>`)

The `content` component renders a page title, optional sidebar, header controls, main area, and footer:

```blade
<x-content title="Page title">
    <x-slot name="sidebar">
        {{-- Filters, secondary nav --}}
    </x-slot>

    <x-slot name="controls">
        {{-- Buttons in the page header --}}
        <x-button caption="Create" role="primary" icon="add" />
    </x-slot>

    Main page content.

    <x-slot name="footer">
        {{-- e.g. paginator --}}
        <x-paginator :total="$total" :size="25" />
    </x-slot>
</x-content>
```

- With a `sidebar` slot, the sidebar layout is used.
- Without it, the simple full-width layout is used.

### Extending package layouts directly

```blade
@extends('joona::default')

@section('content_main')
    ...
@endsection
```

Other layouts: `joona::global`, `joona::simple`, `joona::sidebar`.

### Blade directives

| Directive | Output |
|-----------|--------|
| `@icon('name')` | Material Symbols icon `<i>` |
| `@attributes([...])` | Renders HTML attributes via `HtmlHelper::attributes()` |

## Blade components

Components are registered without a namespace prefix—use `<x-form>`, `<x-button>`, etc.

### Forms

```blade
<x-form method="post" action="{{ route('blog.save') }}" class="my-form">
    <div data-role="form.response"></div>

    <x-input name="title" label="Title" value="{{ $post->title }}" required />

    <x-button caption="Save" role="primary" icon="check" />
</x-form>
```

The form renders `data-bind="components.form"` and submits via AJAX. Show feedback in an element with `data-role="form.response"` (created automatically if missing).

Field-level errors: use `data-field="field_name"` on a container, or rely on `name` matching inputs. Use `*` as the field key for global errors.

#### `FormResponse` (controller)

```php
use Codeart\Joona\View\Components\Form\FormResponse;

$form = new FormResponse();

$form->setSuccess('Data saved!');
$form->setError('Value required.', 'title'); // omit field or use '*' for global

$form->setAction('reload', true);
$form->setAction('redirect', '/admin/blog');
$form->setAction('close_popup', true);
$form->setAction('reset', true);
$form->addData('id', 1);

return response()->json($form);
```

JSON shape: `status` (`success`|`error`), `fields`, `message`, `actions`, `data`.

Supported actions: `redirect`, `reload`, `reset`, `close_popup`, `close_popup_reload`.

### Buttons

```blade
<x-button
    caption="Submit"
    role="primary"
    icon="check"
    type="submit"
/>
```

`role` maps to Bootstrap `btn-{role}`. Additional attributes are merged onto the `<button>` (default `type="submit"`).

### Alerts

```blade
<x-alert role="info">
    Hello world
</x-alert>
```

Roles follow Bootstrap alert variants (`info`, `success`, `danger`, etc.).

### Dialog (modal content)

Use inside a modal loaded by the JS `Modal` class or as inline modal markup:

```blade
<x-form method="post" action="{{ route('blog.save') }}">
    <x-dialog caption="Edit post">
        <x-input name="title" label="Title" />

        <x-slot name="footer">
            <x-button caption="Save" icon="check" />
        </x-slot>
    </x-dialog>
</x-form>
```

### Paginator

```blade
<x-paginator :total="$total" :size="25" param="page" :range="3" />
```

| Prop | Default | Description |
|------|---------|-------------|
| `total` | `0` | Total row count |
| `size` | `25` | Rows per page |
| `param` | `page` | Query string parameter |
| `range` | `3` | Page numbers shown around current |
| `links` | `[]` | Custom link attributes; `href` can use `sprintf` with page number |

Hidden when only one page exists.

### Input, textarea, select, checkbox

```blade
<x-input
    name="email"
    label="Email"
    value="{{ $user->email }}"
    size="md"
    icon-prepend="mail"
    required
/>

<x-textarea name="body" label="Body" />

<x-select
    name="status"
    label="Status"
    :options="$options"
    blank
/>

<x-checkbox name="active" label="Active" :checked="true" />
```

`<x-select>` options are `Codeart\Joona\View\Components\Select\Option` instances or `Group` objects for optgroups.

### Datepicker, color picker, range

```blade
<x-datepicker name="published_at" label="Published" :value="$date" />

<x-colorpicker name="color" label="Color" />

<x-range name="priority" label="Priority" min="0" max="100" />
```

Pass extra `data-*` attributes for JS options (see component templates).

### Password validator

```blade
<x-password-validator
    name="password"
    label="Password"
    :policy="config('joona.admin_password_policy')"
/>
```

### Autocomplete

```blade
<x-autocomplete
    name="user_id"
    label="User"
    :value="$id"
    data-route="{{ route('users.search') }}"
/>
```

`data-route` is required (converted to `route` in JS). Optional: `data-proxy`, `data-input`.

### Multiselect

```blade
<x-multiselect
    name="tags[]"
    label="Tags"
    type="checkbox"
    :options="$options"
/>
```

### Tags (Tagify)

```blade
<x-tags
    label="Keywords"
    name="keywords"
    :value="$tags"
    data-search-url="{{ route('tags.search') }}"
/>
```

### Uploader

```blade
<x-uploader
    name="files"
    class="default"
    :files="$existingFiles"
    data-uploadroute="files.upload"
    data-deleteroute="files.delete"
    data-limit="5"
    data-submitbtn="#save-button"
/>
```

Preload files using `Codeart\Joona\View\Components\Uploader\File\Image` or `Document` (extend `UploadedFile`). Return `UploadResponse` from upload/delete endpoints.

Optional: `data-croproute`, `data-sortable`, `data-captions`, crop presets via `[data-role="crop-presets"]` JSON.

### Gallery

```blade
<x-gallery
    name="images"
    :items="$items"
    :sortable="true"
/>
```

Items should expose `id`, `url`, `thumbnail` for the template.

### Editor (Editor.js)

```blade
<x-editor
    name="content"
    label="Content"
    :content="$blocks"
/>
```

### Text editor (Pell)

```blade
<x-text-editor name="summary" label="Summary" :value="$html" />
```

### Data table

```blade
<x-table sortable="handle">
    <thead>...</thead>
    <tbody>...</tbody>
</x-table>
```

Enables SortableJS when `sortable` is set (e.g. `handle` for drag handle selector).

### Tree editor

```blade
<x-tree-editor
    :rows="$nodes"
    edit-route="categories.edit"
    sort-route="categories.sort"
    delete-route="categories.delete"
    :depth="3"
/>
```

Rows implement `TreeNode` (id, parentId, title, etc.).

### Chart

```blade
<x-chart :data="$chartConfig" />
```

`$chartConfig` is passed to Chart.js as JSON in the template.

### Map picker

```blade
<x-map-picker name="location" :value="$coordinates" class="map-picker-lg" />
```

### Other components

| Component | Usage |
|-----------|--------|
| `<x-accordion>` | Collapsible sections |
| `<x-offcanvas>` | Offcanvas panel (header/body/footer slots) |
| `<x-navbar>` | Tab-like nav |
| `<x-container>` | Width-constrained wrapper |
| `<x-copy>` | Copy-to-clipboard control |
| `<x-dropdown-radio>` | Radio options in a dropdown |
| `<x-table-bulk-options>` | Bulk actions for tables |
| `<x-page-footer-bar>` | Sticky footer actions |
| `<x-form-section-heading>` | Section title in long forms |
| `<x-checkbox-group>` | Grouped checkboxes |
| `<x-toast>` | Toast markup (usually driven by JS) |

## JavaScript API

### Runtime (`Joona`)

```javascript
import Joona from '@joona/js/main.js';

Joona.ready(); // returns Promise

Joona.addHandlers(MyHandler);
Joona.init(contextElement); // bind new [data-bind] nodes (also runs after HTMX swaps if HTMX is present)

const { instance } = await Joona.getInstance(element, 'components.uploader');
const { instance } = await Joona.getInstanceById('my-id');
const instances = await Joona.getInstances(element, 'blog.edit-form');
```

Translations: `trans('joona::common.ok')` and `choice()` after `ready()` (backed by `lang.js`).

### Modal

```javascript
import Modal from '@joona/js/components/modal.js';

const modal = new Modal();
await modal.open('/admin/users/edit/1', { animations: true });
await modal.close();
```

While open, `window.JoonaModalInstance` references the active modal.

### Confirm dialog

```javascript
import ConfirmDialog from '@joona/js/components/confirm-dialog.js';

new ConfirmDialog('Confirm', 'Delete this record?', [
    { caption: 'Cancel', role: 'secondary', callback: () => {} },
    { caption: 'Delete', role: 'primary', callback: () => { /* ... */ } },
]).open();
```

Closes automatically when a button is pressed.

## Custom JS handlers

Handlers group DOM behavior behind `data-bind="plugin.action"` attributes.

### 1. Create a handler class

```javascript
// resources/js/handlers/blog.js
import Handler from '@joona/js/handler.js';

export default class Blog extends Handler {
    static get pluginName() {
        return 'blog';
    }

    editForm(element, parameters, runtime) {
        // element: DOM node
        // parameters: other data-* attributes (data-id → id)
        // runtime: Joona instance
    }
}
```

`edit-form` in HTML maps to the `editForm` method.

### 2. Register and bind

```javascript
// resources/js/admin.js
import Joona from '@joona/js/main.js';
import Blog from './handlers/blog.js';

Joona.addHandlers(Blog);
Joona.ready();
```

```html
<div data-bind="blog.edit-form" data-id="1"></div>
```

### 3. Return instances for cross-component access

```javascript
userComponent(element, parameters, runtime) {
    return {
        hello(name) {
            alert(name);
        },
    };
}

editForm(element, parameters, runtime) {
    runtime.getInstance(element, 'blog.user-component').then(({ instance }) => {
        instance.hello('Bob');
    });
}
```

Return a plain object or a Promise from handler methods to store retrievable instances.

### Built-in `components` handler

These `data-bind` values are handled automatically:

| Bind | Component |
|------|-----------|
| `components.form` | AJAX form |
| `components.uploader` | File uploader |
| `components.autocomplete` | Autocomplete |
| `components.datepicker` | Date picker |
| `components.multi-select` | Multiselect dropdown |
| `components.passwordValidator` | Password strength |
| `components.map-picker` | Leaflet map |
| `components.gallery` | Image gallery |
| `components.chart` | Chart.js |
| `components.tree-editor` | Nested sortable tree |
| `components.editor` | Editor.js |
| `components.text-editor` | Pell editor |
| `components.table` | Sortable table |
| `components.tags` | Tagify |
| `components.copy-text` | Clipboard copy |
| `components.table-bulk-options` | Bulk table actions |

The `admin` handler covers panel UI (theme switch, sidebar, notifications, profile modal, etc.).

## Permissions

When `useRolesAndPermissions(true)`:

- Routes can be tied to permissions via `RoutePermission` inside `PermissionGroup` objects.
- Menu items hide automatically when `Gate::denies()` the page route.
- Middleware `CheckPermissions` runs on `admin.auth` routes.

Register custom permissions in `configure()`:

```php
use Codeart\Joona\Auth\Permissions\PermissionGroup;
use Codeart\Joona\Auth\Permissions\RoutePermission;

$panel->addPermissions([
    PermissionGroup::make('Blog', [
        new RoutePermission(
            id: 'blog_edit',
            routes: ['blog.edit', 'blog.save'],
            label: 'Edit blog posts',
        ),
    ]),
]);
```

## Configuration (`config/joona.php`)

| Key | Description |
|-----|-------------|
| `admin_password_policy` | Comma-separated rules: `min`, `max`, `uppercase`, `lowercase`, `mixed`, `number`, `special` |
| `js_translations` | Extra translation keys exposed to JavaScript |
| `auto_block_user` | Failed login attempts before lockout (`0` = disabled) |
| `class_role_mode` | `interchangeable` — user class vs role behavior |

## Artisan commands

| Command | Description |
|---------|-------------|
| `joona:publish` | Install npm deps in package assets, publish config/images/provider |
| `joona:seed` | Seed default admin user (`AdminUserSeeder`) |
| `joona:update-session` | Updates admin session records (scheduled every 10 minutes) |

## Package structure (reference)

```
config/              joona.php, auth guard merge
database/migrations/ Admin users, roles, permissions, logs, notifications
export/              Published JoonaServiceProvider stub
resources/
  assets/            SCSS, JS, npm package (Bootstrap, Chart.js, Editor.js, …)
  views/             Layouts and Blade components
routes/              web.php (guest), secure.php (authenticated)
src/                 PHP: Panel, HTTP, Auth, View components, Commands
```

## Notes

- The package adds an **`admin`** guard and **`joona`** provider—plan naming if you add more guards.
- Static images (logos, flags) live under `public/vendor/joona` after publish.
- For HTMX projects, the runtime re-initializes bindings on `htmx:afterSwap` / `htmx:oobAfterSwap` when HTMX is loaded in your app (not bundled with Joona).
- Override package views by placing files in `resources/views/vendor/joona/`.
