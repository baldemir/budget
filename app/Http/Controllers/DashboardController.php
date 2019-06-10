<?php

namespace App\Http\Controllers;

use App\Repositories\TagRepository;
use Illuminate\Http\Request;

use App\Earning;
use App\Recurring;
use App\Spending;
use Auth;
use DB;

class DashboardController extends Controller {
    public function __invoke() {

        session(['dashYear' => 2019]);
        session(['dashMonth' => 5]);

        $space_id = session('space')->id;
        $currentYear = date('Y');
        $currentMonth = date('m');


        if(session('dashYear') != null){
            //$currentYear = session('dashYear');
        }

        if(session('dashMonth') != null){
            //$currentMonth = session('dashMonth');

        }


        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

        $balance = session('space')->monthlyBalance($currentYear, $currentMonth);
        $recurrings = session('space')->monthlyRecurrings($currentYear, $currentMonth);
        $leftToSpend = $balance - $recurrings;

        $totalSpent = session('space')->spendings()->whereRaw('YEAR(happened_on) = ? AND MONTH(happened_on) = ?', [$currentYear, $currentMonth])->sum('amount');
        $totalEarnt = session('space')->earnings()->whereRaw('YEAR(happened_on) = ? AND MONTH(happened_on) = ?', [$currentYear, $currentMonth])->sum('amount');

        $tagRepository = new TagRepository();
        $mostExpensiveTags = $tagRepository->getMostExpensiveTags($space_id, 3, $currentYear, $currentMonth);

        $balanceTick = 0;
        $dailyBalance = [];
        for ($i = 1; $i <= $daysInMonth; $i ++) {
            $balanceTick += session('space')
                ->spendings()
                ->where('happened_on', $currentYear . '-' . $currentMonth . '-' . $i)
                ->sum('amount');
/*
            $balanceTick += session('space')
                ->earnings()
                ->where('happened_on', $currentYear . '-' . $currentMonth . '-' . $i)
                ->sum('amount');
*/
            $dailyBalance[$i] = number_format($balanceTick / 100, 2, '.', '');
        }
        return view('dashboard', [
            'month' => date('n'),

            'balance' => $balance,
            'recurrings' => $recurrings,
            'leftToSpend' => $leftToSpend,

            'totalSpent' => $totalSpent,
            'totalEarnt' => $totalEarnt,
            'mostExpensiveTags' => $mostExpensiveTags,

            'daysInMonth' => $daysInMonth,
            'dailyBalance' => $dailyBalance
        ]);
    }
}
