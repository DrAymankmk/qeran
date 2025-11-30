<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Mpdf\Mpdf;

class AdminsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = Admin::with('roles')->orderBy('created_at', 'desc')->paginate(15);
        return view('pages.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        return view('pages.admins.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\AdminRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminRequest $request)
    {
        $data = $request->validated();

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $admin = Admin::create($data);

        // Assign roles
        if ($request->has('roles')) {
            $admin->syncRoles($request->roles);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            storeImage([
                'value' => $request->file('image'),
                'folderName' => Constant::ADMIN_IMAGE_FOLDER_NAME,
                'file_key' => Constant::FILE_KEY['Main'],
                'file_type' => Constant::FILE_TYPE['Image'],
                'model' => $admin,
                'saveInDatabase' => true
            ]);
        }

        return redirect()->route('admins.index')->with('success', __('admin.created-successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $admin = Admin::with('roles', 'permissions')->findOrFail($id);
        return view('pages.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = Admin::with('roles')->findOrFail($id);
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        $adminRoles = $admin->roles->pluck('id')->toArray();
        return view('pages.admins.edit', compact('admin', 'roles', 'adminRoles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\AdminRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdminRequest $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $data = $request->validated();

        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);

        // Sync roles
        if ($request->has('roles')) {
            $admin->syncRoles($request->roles);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($admin->hubFiles) {
                deleteImage($admin->hubFiles->get_folder_file(), $admin->hubFiles);
            }

            storeImage([
                'value' => $request->file('image'),
                'folderName' => Constant::ADMIN_IMAGE_FOLDER_NAME,
                'file_key' => Constant::FILE_KEY['Main'],
                'file_type' => Constant::FILE_TYPE['Image'],
                'model' => $admin,
                'saveInDatabase' => true
            ]);
        }

        return redirect()->route('admins.index')->with('success', __('admin.updated-successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        // Prevent deleting yourself
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->route('admins.index')->with('error', __('admin.cannot-delete-yourself'));
        }

        // Delete image if exists
        if ($admin->hubFiles) {
            deleteImage($admin->hubFiles->get_folder_file(), $admin->hubFiles);
        }

        $admin->delete();

        return redirect()->route('admins.index')->with('success', __('admin.deleted-successfully'));
    }

    /**
     * Export admins to PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function adminsExportPdf()
    {
        $admins = Admin::orderBy('created_at', 'desc')->get();

        // Configure mPDF with Arabic font support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape
            'orientation' => 'L',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'autoVietnamese' => true,
            'autoArabic' => true,
            'direction' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
        ]);

        // Build HTML content
        $html = view('pages.admins.pdf-export', compact('admins'))->render();

        $mpdf->WriteHTML($html);

        $filename = 'admins_' . date('Y-m-d_His') . '.pdf';

        return $mpdf->Output($filename, 'D'); // D = Download
    }
}

