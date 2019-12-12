<?php

namespace App\Http\Controllers;

use App\Earning;
use App\Repositories\TransactionRepository;
use App\Spending;
use Illuminate\Http\Request;

class TransactionController extends Controller {
    public function __construct(TransactionRepository $transactionRepository) {
        $this->repository = $transactionRepository;
    }

    public function index(Request $request) {
        $filterBy = null;

        if ($request->get('filterBy')) {
            $filterBy = explode('-', $request->get('filterBy'));
        }

        $tags = [];

        foreach (session('space')->tags as $tag) {
            $tags[] = [
                'key' => $tag->id,
                'type' => $tag->type,
                'label' => '<div class="row"><div class="row__column row__column--compact row__column--middle mr-1"><div style="width: 15px; height: 15px; border-radius: 2px; background: #' . $tag->color . ';"></div></div><div class="row__column row__column--middle">' . $tag->name . '</div></div>'];
        }

        return view('transactions.index', [
            'yearMonths' => $this->repository->getTransactionsByYearMonth($filterBy),
            'tags' => session('space')->tags,
            'tagList' => $tags
        ]);
    }

    public function create() {
        $tags = [];

        foreach (session('space')->tags as $tag) {
            $tags[] = [
                'key' => $tag->id,
                'type' => $tag->type,
                'label' => '<div class="row"><div class="row__column row__column--compact row__column--middle mr-1"><div style="width: 15px; height: 15px; border-radius: 2px; background: #' . $tag->color . ';"></div></div><div class="row__column row__column--middle">' . $tag->name . '</div></div>'];
        }

        return view('transactions.create', compact('tags'));
    }
    public function updateSpending(Request $request, Spending $spending) {
        $this->authorize('update', $spending);
        $tagId = $request->get('tag_id');
        $spending->tag_id = $tagId;
        $spending->save();
        return $spending;

    }
    public function updateEarning(Request $request, Earning $earning) {
        $this->authorize('update', $earning);
        $tagId = $request->get('tag_id');
        $earning->tag_id = $tagId;
        $earning->save();
        return $earning;
    }
}
