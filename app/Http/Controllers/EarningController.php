<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Earning;

use Auth;

class EarningController extends Controller {
    protected function validationRules() {
        return [
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'required|max:255',
            'additional_desc' => 'max:1000',
            'amount' => 'required|regex:/^(\d{0,3}(?:,\d{3}){0,4})*(\.\d{2})?$/'
        ];
    }

    public function index(Request $request) {
        $user = Auth::user();

        $earningsByMonth = [];

        for ($month = 12; $month >= 1; $month --) {
            $query = session('space')
                ->earnings()
                ->whereYear('happened_on', date('Y'))
                ->whereMonth('happened_on', $month);


            if ($import_id = $request->get('filter_by_import')) {
                $filter = 'import';

                $query->where('import_id', $import_id);
            }

            $earningsThisMonth = $query->orderBy('happened_on', 'DESC')
                ->get();

            if (count($earningsThisMonth)) {
                $earningsByMonth[$month] = $earningsThisMonth;
            }
        }


        $earnings = session('space')
            ->earnings()
            ->orderBy('happened_on', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('earnings.index', compact('earnings', 'earningsByMonth'));
    }

    public function create() {
        return view('earnings.create');
    }

    public function store(Request $request) {
        $request->validate($this->validationRules());

        $earning = new Earning;

        $earning->space_id = session('space')->id;
        $earning->happened_on = $request->input('date');
        $earning->description = $request->input('description');
        $earning->additional_desc = $request->input('additional_desc');
        $earning->amount = (int) ($request->input('amount') * 100);

        $earning->save();

        return redirect()->route('dashboard');
    }

    public function edit(Earning $earning) {
        $this->authorize('edit', $earning);

        return view('earnings.edit', compact('earning'));
    }

    public function update(Request $request, Earning $earning) {
        $this->authorize('update', $earning);

        $request->validate($this->validationRules());
        $amount = str_replace('.', '', str_replace(',', '', $request->input('amount')));

        $earning->fill([
            'happened_on' => $request->input('date'),
            'description' => $request->input('description'),
            'additional_desc' => $request->input('additional_desc'),
            'amount' => $amount
        ])->save();

        return redirect()->route('transactions.index');
    }

    public function destroy(Earning $earning) {
        $this->authorize('delete', $earning);

        $restorableEarning = $earning->id;

        $earning->delete();

        return redirect()
            ->back()
            ->with([
                'restorableEarning' => $restorableEarning
            ]);
    }

    public function restore($id) {
        $earning = Earning::withTrashed()->find($id);

        if (!$earning) {
            // 404
        }

        $this->authorize('restore', $earning);

        $earning->restore();

        return redirect()->route('earnings.index');
    }
}
