<?php

namespace App\Common\Helpers;

use Carbon\Carbon;

class FilterHelper
{
    /**
     * @param $collection \Illuminate\Support\Collection
     * @param $field_name string
     * @param $i_from_date string
     * @param $i_to_date string
     * @return \Illuminate\Support\Collection
     */
    static public function filterByFromDateToDate($collection, $field_name, $i_from_date, $i_to_date)
    {
        if ($i_from_date && $i_to_date) {
            $from_dateTime = DateTimeHelper::addTimeForDate($i_from_date, 'min');
            $to_dateTime   = DateTimeHelper::addTimeForDate($i_to_date, 'max');

            $from_date = Carbon::createFromFormat('d/m/Y H:i:s', $from_dateTime);
            $to_date   = Carbon::createFromFormat('d/m/Y H:i:s', $to_dateTime);

            $filtered = $collection->filter(function ($item, $key) use ($field_name, $from_date, $to_date) {
                $current = Carbon::createFromFormat('Y-m-d H:i:s', $item[$field_name]);
                return $current->between($from_date, $to_date, true);
            });

            return $filtered;
        }
        return $collection;
    }

    /**
     * @param $collection \Illuminate\Support\Collection
     * @param $field_name string
     * @param $range string
     * @return \Illuminate\Support\Collection
     */
    static public function filterByRangeDate($collection, $field_name, $range)
    {
        if ($range && $range != 'none') {
            $filtered = $collection->filter(function ($item, $key) use ($range, $field_name) {
                $current         = Carbon::createFromFormat('Y-m-d H:i:s', $item[$field_name]);
                $current->hour   = 0;
                $current->minute = 0;
                $current->second = 0;

                $flag = false;

                switch ($range) {
                    case 'yesterday':
                        $yesterday = Carbon::yesterday();
                        $flag      = ($current->diffInDays($yesterday, false) == 0);
                        break;
                    case 'today':
                        $today = Carbon::today();
                        $flag  = ($current->diffInDays($today, false) == 0);
                        break;
                    case 'week':
                        $start_of_week = Carbon::now()->startOfWeek();
                        $end_of_week   = Carbon::now()->endOfWeek();
                        $flag          = $current->between($start_of_week, $end_of_week);
                        break;
                    case 'month':
                        $start_of_month = Carbon::now()->startOfMonth();
                        $end_of_month   = Carbon::now()->endOfMonth();
                        $flag           = $current->between($start_of_month, $end_of_month);
                        break;
                    case 'year':
                        $start_of_year = Carbon::now()->startOfYear();
                        $end_of_year   = Carbon::now()->endOfYear();
                        $flag          = $current->between($start_of_year, $end_of_year);
                        break;
                    default:
                        break;
                }
                return $flag;
            });
            return $filtered;
        }
        return $collection;
    }

//    static public function filterByValue($collection, $field_name, $value)
//    {
//        if ($value)
//            return $collection->where($field_name, $value);
//        return $collection;
//    }
}