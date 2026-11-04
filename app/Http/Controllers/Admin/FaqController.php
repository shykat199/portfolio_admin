<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RefreshesFrontend;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    use RefreshesFrontend;

    /**
     * List every question in display order.
     */
    public function index(): View
    {
        return view('faqs.index', [
            'faqs' => Faq::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    /**
     * Show the form for a new question.
     */
    public function create(): View
    {
        return view('faqs.form', [
            'faq' => new Faq(['is_active' => true, 'sort_order' => (Faq::max('sort_order') ?? 0) + 1]),
        ]);
    }

    /**
     * Save a new question.
     */
    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::create($request->validated());

        $this->refreshFrontend('faqs');

        return redirect()->route('faqs.index')->with('toast', [
            'type' => 'success',
            'message' => __('Question added.'),
        ]);
    }

    /**
     * Show the form for editing a question.
     */
    public function edit(Faq $faq): View
    {
        return view('faqs.form', ['faq' => $faq]);
    }

    /**
     * Save changes to a question.
     */
    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        $this->refreshFrontend('faqs');

        return redirect()->route('faqs.index')->with('toast', [
            'type' => 'success',
            'message' => __('Changes saved.'),
        ]);
    }

    /**
     * Delete a question.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        $this->refreshFrontend('faqs');

        return redirect()->route('faqs.index')->with('toast', [
            'type' => 'success',
            'message' => __('Question deleted.'),
        ]);
    }
}
