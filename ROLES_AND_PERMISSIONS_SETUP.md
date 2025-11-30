# Roles and Permissions Setup Guide

This document explains how to set up and use the Roles and Permissions system using Spatie Laravel Permission package.

## Installation Steps

1. **Run Migrations** (if not already run):
   ```bash
   php artisan migrate
   ```

2. **Seed Permissions and Roles**:
   ```bash
   php artisan db:seed --class=RolesAndPermissionsSeeder
   ```

   This will:
   - Create all permissions based on existing modules
   - Create a default "Super Admin" role with all permissions

## Features

### 1. Admin Module Integration
- Every admin **must** have at least one role
- Roles are assigned when creating/editing admins
- Admins can have multiple roles

### 2. Permission-Based Modules
Permissions are organized by modules:
- Dashboard
- Categories
- Invitations
- Invitation Requests
- Users
- Admins
- Packages
- Promo Codes
- Financial
- Notifications
- Contact Us
- App Settings
- Roles
- Permissions

### 3. Using Permissions in Controllers

#### Method 1: Using Middleware in Routes
```php
Route::middleware(['auth:admin', 'permission:view-dashboard'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

#### Method 2: Using in Controller Methods
```php
public function index()
{
    $this->authorize('view-users');
    
    // Your code here
}
```

#### Method 3: Using in Blade Templates
```blade
@can('view-users')
    <a href="{{route('users.index')}}">Users</a>
@endcan

@hasrole('Super Admin')
    <a href="{{route('admins.index')}}">Admins</a>
@endhasrole
```

### 4. Available Permission Checks

#### In Controllers:
```php
// Check if admin has permission
if (auth()->guard('admin')->user()->can('view-users')) {
    // Allow access
}

// Check if admin has role
if (auth()->guard('admin')->user()->hasRole('Super Admin')) {
    // Allow access
}

// Check if admin has any of the roles
if (auth()->guard('admin')->user()->hasAnyRole(['Super Admin', 'Manager'])) {
    // Allow access
}
```

#### In Blade Templates:
```blade
@can('view-users')
    <!-- Content for users with view-users permission -->
@endcan

@cannot('delete-users')
    <!-- Content for users without delete-users permission -->
@endcannot

@hasrole('Super Admin')
    <!-- Content for Super Admin role -->
@endhasrole

@hasanyrole('Super Admin|Manager')
    <!-- Content for users with any of these roles -->
@endhasanyrole
```

## Adding New Permissions

1. **Add to PermissionHelper** (`app/Helpers/PermissionHelper.php`):
   ```php
   'new-module' => [
       'view-new-module',
       'create-new-module',
       'edit-new-module',
       'delete-new-module',
   ],
   ```

2. **Run the seeder again**:
   ```bash
   php artisan db:seed --class=RolesAndPermissionsSeeder
   ```

3. **Add translations** in `lang/en/admin.php` and `lang/ar/admin.php`:
   ```php
   'permission-view-new-module' => 'View New Module',
   'module-new-module' => 'New Module',
   ```

## Protecting Routes

### Example: Protect all admin routes
```php
Route::group(['middleware' => ['auth:admin', 'permission:view-dashboard']], function () {
    // Your routes here
});
```

### Example: Protect specific routes
```php
Route::get('/users', [UsersController::class, 'index'])
    ->middleware('permission:view-users');

Route::post('/users', [UsersController::class, 'store'])
    ->middleware('permission:create-users');
```

## Menu Visibility Based on Permissions

Update `resources/views/layouts/includes/menu.blade.php`:

```blade
@can('view-users')
<li>
    <a href="{{route('users.index')}}" class="waves-effect">
        <i class="bx bx-user"></i>
        <span>{{__('admin.users')}}</span>
    </a>
</li>
@endcan
```

## Default Super Admin Role

After running the seeder, assign the "Super Admin" role to your first admin:

```php
$admin = Admin::first();
$admin->assignRole('Super Admin');
```

Or via tinker:
```bash
php artisan tinker
>>> $admin = App\Models\Admin::first();
>>> $admin->assignRole('Super Admin');
```

## Important Notes

1. **Guard Configuration**: The system uses the 'admin' guard. Make sure `config/permission.php` has `'default_guard_name' => 'admin'`.

2. **Admin Model**: The `Admin` model already uses the `HasRoles` trait from Spatie.

3. **Required Roles**: Admins must have at least one role. This is enforced in the `AdminRequest` validation.

4. **Permission Naming**: Follow the pattern `action-module` (e.g., `view-dashboard`, `create-users`).

## Troubleshooting

### Permissions not working?
1. Clear cache: `php artisan cache:clear`
2. Clear config: `php artisan config:clear`
3. Re-run seeder: `php artisan db:seed --class=RolesAndPermissionsSeeder`

### Roles not showing?
1. Make sure migrations are run: `php artisan migrate`
2. Check if roles exist in database: `php artisan tinker` then `Role::all()`

### Admin can't access?
1. Make sure admin has at least one role assigned
2. Check if the role has the required permissions
3. Verify the guard is set correctly in `config/permission.php`

