<?php

namespace App\Http\Controllers;

use App\Repositories\TagRepository;
use App\Utils;
use Illuminate\Http\Request;

use App\Earning;
use App\Recurring;
use App\Spending;
use Auth;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class DashboardController extends Controller {
    public function __invoke() {

        session(['dashYear' => 2019]);
        session(['dashMonth' => 5]);

        $space_id = session('space')->id;
        $currentYear = date('Y');
        $currentMonth = date('m');
        $monthYear = date('Y-m');


        if(Input::get('yil')){
            $currentYear = Input::get('yil');
        }
        if(Input::get('ay')){
            $currentMonth= Input::get('ay');
        }


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

        $totalEarnt2 = session('space')->earnings()->whereRaw('YEAR(happened_on) = ? AND MONTH(happened_on) = ?', [$currentYear, $currentMonth])->groupBy('description')->selectRaw('SUM(amount) total, description')->orderBy('total')->get();
        $totalSpent2 = session('space')->spendings()->whereRaw('YEAR(happened_on) = ? AND MONTH(happened_on) = ?', [$currentYear, $currentMonth])->groupBy('tag_id')
            ->join('tags', 'spendings.tag_id', '=', 'tags.id')->selectRaw('SUM(amount) total, tag_id, name')->orderBy('total')->get();

        $earningCats = [];
        $earningAmounts = [];
        $earningColors = [];

        $spendingCats = [];
        $spendingAmounts = [];
        $spendingColors = [];
        foreach ($totalEarnt2 as $earnt){
            $earningCats[] = $earnt["description"];
            $earningAmounts[] = Utils::formatAmount($earnt["total"]);
            $earningColors[] = "#" . Utils::rand_color();
        }

        foreach ($totalSpent2 as $spent){
            $spendingCats[] = $spent["name"];
            $spendingAmounts[] = Utils::formatAmount($spent["total"]);
            $spendingColors[] = "#" . Utils::rand_color();
        }




        $tagRepository = new TagRepository();
        $mostExpensiveTags = $tagRepository->getMostExpensiveTags($space_id, 5, $currentYear, $currentMonth);

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

        $months = Spending::select(DB::raw('count(id) as `data`'), DB::raw("DATE_FORMAT(happened_on, '%Y') new_year"), DB::raw("DATE_FORMAT(happened_on, '%m') new_month"),  DB::raw('YEAR(happened_on) year, MONTH(happened_on) month'))
            ->groupby('year','month')
            ->orderBy('happened_on', 'desc')
            ->limit(5)
            ->get();


        $tags=[];
        foreach ($months as $month) {
            $monthNumber = (int)$month->new_month;
            $href = "";
            if($monthNumber == $currentMonth && $month->new_year == $currentYear ){
                $href = "";
                $monthYear = $month->new_year . '-' . $month->new_month;
            }else{
                $href = "href='?ay=" . $monthNumber . '&yil=' . $month->new_year . "'";
            }
            $tags[] = [true,'key' => $month->new_year . '-' . $month->new_month, 'label' => '<div class="row"><div class="row__column row__column--compact row__column--middle mr-1"><a ' . $href .' ><div style="width: 15px; height: 15px; border-radius: 2px; background: #' . 'F00' . ';"></div></div><div class="row__column row__column--middle">' . Lang::get('calendar.months.' . $monthNumber) .' ' . $month->new_year . '</a></div></div>'];

        }
        return view('dashboard', [
            'monthYear' => $monthYear,

            'balance' => $balance,
            'recurrings' => $recurrings,
            'leftToSpend' => $leftToSpend,

            'totalSpent' => $totalSpent,
            'totalEarnt' => $totalEarnt,
            'mostExpensiveTags' => $mostExpensiveTags,

            'daysInMonth' => $daysInMonth,
            'dailyBalance' => $dailyBalance,
            'tags' => $tags,
            'earningCats' => $earningCats,
            'earningAmounts' => $earningAmounts,
            'earningColors' => $earningColors,
            'spendingCats' => $spendingCats,
            'spendingAmounts' => $spendingAmounts,
            'spendingColors' => $spendingColors,
        ]);
    }
}
