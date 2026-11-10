<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TechItemRequest;
use App\Models\TechItem;
use App\Services\WebpImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TechItemController extends Controller
{
    use RefreshesFrontend;

    public function __construct(private WebpImageStore $images) {}

    /**
     * List the tech stack in display order.
     */
    public function index(): View
    {
        return view('tech.index', [
            'items' => TechItem::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    /**
     * Show the form for a new item.
     */
    public function create(): View
    {
        return view('tech.form', [
            'item' => new TechItem(['is_active' => true, 'sort_order' => (TechItem::max('sort_order') ?? 0) + 1]),
        ]);
    }

    /**
     * Save a new item.
     */
    public function store(TechItemRequest $request): RedirectResponse
    {
        $item = TechItem::create($this->attributes($request, new TechItem));

        $this->refreshFrontend('tech');

        return redirect()->route('tech.index')->with('toast', [
            'type' => 'success',
            'message' => __(':name added to your tech stack.', ['name' => $item->name]),
        ]);
    }

    /**
     * Show the form for editing an item.
     */
    public function edit(TechItem $tech): View
    {
        return view('tech.form', ['item' => $tech]);
    }

    /**
     * Save changes to an item.
     */
    public function update(TechItemRequest $request, TechItem $tech): RedirectResponse
    {
        $tech->update($this->attributes($request, $tech));

        $this->refreshFrontend('tech');

        return redirect()->route('tech.index')->with('toast', [
            'type' => 'success',
            'message' => __(':name updated.', ['name' => $tech->name]),
        ]);
    }

    /**
     * Delete an item and its logo.
     */
    public function destroy(TechItem $tech): RedirectResponse
    {
        if ($tech->logo_path) {
            Storage::disk('public')->delete($tech->logo_path);
        }

        $tech->delete();

        $this->refreshFrontend('tech');

        return redirect()->route('tech.index')->with('toast', [
            'type' => 'success',
            'message' => __(':name removed.', ['name' => $tech->name]),
        ]);
    }

    /**
     * The fields to save, with the logo stored, replaced or removed.
     *
     * @return array<string, mixed>
     */
    private function attributes(TechItemRequest $request, TechItem $item): array
    {
        $attributes = $request->safe()->only(['name', 'is_active', 'sort_order']);
        $replacing = $request->hasFile('logo') || $request->boolean('remove_logo');

        if ($replacing && $item->logo_path) {
            Storage::disk('public')->delete($item->logo_path);
            $attributes['logo_path'] = null;
        }

        if ($request->hasFile('logo')) {
            $attributes['logo_path'] = $this->images->store($request->file('logo'), 'tech');
        }

        return $attributes;
    }
}
