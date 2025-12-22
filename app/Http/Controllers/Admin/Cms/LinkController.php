<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsLink;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class LinkController extends Controller
{
    /**
     * Display links for a specific resource (Page, Section, or Item)
     */
    public function index(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        $links = $model->allLinks()->orderBy('order')->get();
        
        return view('admin.cms.links.index', compact('model', 'type', 'id', 'links'));
    }

    /**
     * Show form to create a new link
     */
    public function create(Request $request, $type, $id)
    {
        $model = $this->getModel($type, $id);
        $routes = $this->getAvailableRoutes();
        return view('admin.cms.links.create', compact('model', 'type', 'id', 'routes'));
    }

    /**
     * Store a new link
     */
    public function store(Request $request, $type, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'link_type' => 'required|in:route,custom',
            'route_name' => 'required_if:link_type,route|nullable|string|max:255',
            'link' => 'required_if:link_type,custom|nullable|url|max:500',
            'icon' => 'nullable|string|max:255',
            'target' => 'nullable|string|in:_self,_blank',
            'type' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $model = $this->getModel($type, $id);
        
        $link = $model->allLinks()->create([
            'name' => $validated['name'],
            'link' => $validated['link_type'] === 'custom' ? $validated['link'] : null,
            'route_name' => $validated['link_type'] === 'route' ? $validated['route_name'] : null,
            'icon' => $validated['icon'] ?? null,
            'target' => $validated['target'] ?? '_self',
            'type' => $validated['type'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('cms.links.index', [$type, $id])
            ->with('success', 'Link created successfully');
    }

    /**
     * Show form to edit a link
     */
    public function edit($type, $id, CmsLink $link)
    {
        $model = $this->getModel($type, $id);
        
        // Verify link belongs to this model
        if ($link->linkable_type !== get_class($model) || $link->linkable_id != $model->id) {
            abort(404);
        }
        
        $routes = $this->getAvailableRoutes();
        return view('admin.cms.links.edit', compact('model', 'type', 'id', 'link', 'routes'));
    }

    /**
     * Update a link
     */
    public function update(Request $request, $type, $id, CmsLink $link)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'link_type' => 'required|in:route,custom',
            'route_name' => 'required_if:link_type,route|nullable|string|max:255',
            'link' => 'required_if:link_type,custom|nullable|url|max:500',
            'icon' => 'nullable|string|max:255',
            'target' => 'nullable|string|in:_self,_blank',
            'type' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $model = $this->getModel($type, $id);
        
        // Verify link belongs to this model
        if ($link->linkable_type !== get_class($model) || $link->linkable_id != $model->id) {
            abort(404);
        }

        $updateData = [
            'name' => $validated['name'],
            'link' => $validated['link_type'] === 'custom' ? $validated['link'] : null,
            'route_name' => $validated['link_type'] === 'route' ? $validated['route_name'] : null,
            'icon' => $validated['icon'] ?? null,
            'target' => $validated['target'] ?? '_self',
            'type' => $validated['type'] ?? null,
            'order' => $validated['order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ];

        $link->update($updateData);

        return redirect()->route('cms.links.index', [$type, $id])
            ->with('success', 'Link updated successfully');
    }

    /**
     * Delete a link
     */
    public function destroy($type, $id, CmsLink $link)
    {
        $model = $this->getModel($type, $id);
        
        // Verify link belongs to this model
        if ($link->linkable_type !== get_class($model) || $link->linkable_id != $model->id) {
            abort(404);
        }

        $link->delete();

        return redirect()->route('cms.links.index', [$type, $id])
            ->with('success', 'Link deleted successfully');
    }

    /**
     * Reorder links
     */
    public function reorder(Request $request, $type, $id)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:cms_links,id',
        ]);

        foreach ($request->order as $index => $linkId) {
            CmsLink::where('id', $linkId)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get the model instance based on type and id
     */
    private function getModel($type, $id)
    {
        return match($type) {
            'page' => CmsPage::findOrFail($id),
            'section' => CmsSection::findOrFail($id),
            'item' => CmsItem::findOrFail($id),
            default => abort(404, 'Invalid link type'),
        };
    }

    /**
     * Get all available frontend named routes only
     */
    private function getAvailableRoutes()
    {
        $routes = [];
        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            
            // Skip if no name or starts with excluded prefixes
            if (!$name || 
                str_starts_with($name, 'admin.') || 
                str_starts_with($name, 'ignition.') || 
                str_starts_with($name, '_')) {
                continue;
            }
            
            // Check if route action is in Frontend or Website namespace
            $action = $route->getAction();
            $isFrontendRoute = false;
            
            if (isset($action['controller'])) {
                $controller = $action['controller'];
                // Check if controller is in Frontend or Website namespace
                if (str_contains($controller, '\\Frontend\\') || 
                    str_contains($controller, '\\Website\\')) {
                    $isFrontendRoute = true;
                }
            } else {
                // For routes without controller (closures), check URI prefix
                // Frontend routes typically don't start with /admin
                $uri = $route->uri();
                if (!str_starts_with($uri, 'admin/')) {
                    $isFrontendRoute = true;
                }
            }
            
            if ($isFrontendRoute) {
                $routes[$name] = $name;
            }
        }
        ksort($routes);
        return $routes;
    }
}