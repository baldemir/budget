<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Spending;
use App\Tag;

use Auth;

class SpendingController extends Controller {
    protected function validationRules() {
        return [
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'required|max:255',
            'amount' => 'required|regex:/^(\d{0,3}(?:,\d{3}){0,4})*(\.\d{2})?$/',
            'tag_id' => 'nullable|exists:tags,id'
        ];
    }
    public function index(Request $request) {
        $filter = false;

        $spendingsByMonth = [];

        for ($month = 12; $month >= 1; $month --) {
            $query = session('space')
                ->spendings()
                ->whereYear('happened_on', date('Y'))
                ->whereMonth('happened_on', $month);


            if ($import_id = $request->get('filter_by_import')) {
                $filter = 'import';

                $query->where('import_id', $import_id);
            }

            $spendingsThisMonth = $query->orderBy('happened_on', 'DESC')
                ->get();

            if (count($spendingsThisMonth)) {
                $spendingsByMonth[$month] = $spendingsThisMonth;
            }
        }

        return view('spendings.index', compact('filter', 'spendingsByMonth'));
    }

    public function create() {
        $tags = session('space')->tags()->orderBy('created_at', 'DESC')->get();

        return view('spendings.create', compact('tags'));
    }

    public function store(Request $request) {
        $request->validate([
            'tag_id' => 'nullable|exists:tags,id', // TODO CHECK IF TAG BELONGS TO USER
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'required|max:255',
            'additional_desc' => 'max:1000',
            'amount' => 'required|regex:/^(\d{0,3}(?:,\d{3}){0,4})*(\.\d{2})?$/'
        ]);

        $spending = new Spending;

        $spending->space_id = session('space')->id;
        $spending->tag_id = $request->input('tag_id');
        $spending->happened_on = $request->input('date');
        $spending->description = $request->input('description');
        $spending->additional_desc = $request->input('additional_desc');
        $spending->amount = (int) ($request->input('amount') * 100);

        $spending->save();

        return redirect()->route('dashboard');
    }

    public function destroy(Spending $spending) {
        $this->authorize('delete', $spending);

        $restorableSpending = $spending->id;

        $spending->delete();

        return redirect()
            ->back()
            ->with([
                'restorableSpending' => $restorableSpending
            ]);
    }
    public function update(Request $request, Spending $spending) {
        $this->authorize('update', $spending);

        $request->validate($this->validationRules());
        $amount = str_replace('.', '', str_replace(',', '', $request->input('amount')));
        $spending->fill([
            'tag_id' => $request->input('tag_id'),
            'happened_on' => $request->input('date'),
            'description' => $request->input('description'),
            'additional_desc' => $request->input('additional_desc'),
            'amount' => $amount
        ])->save();

        return redirect()->route('transactions.index');
    }

    public function edit(Spending $spending) {
        $this->authorize('edit', $spending);
        $tags = session('space')->tags()->orderBy('created_at', 'DESC')->get();
        return view('spendings.edit', compact('spending', 'tags'));
    }

    public function restore($id) {
        $spending = Spending::withTrashed()->find($id);

        if (!$spending) {
            // 404
        }

        $this->authorize('restore', $spending);

        $spending->restore();

        return redirect()->route('spendings.index');
    }
}
