<?php


namespace App\Traits;


trait ColumnUpdate
{
    protected static function bootColumnUpdate()
    {
        self::columnUpdateHandle();
    }

    private static function columnUpdateHandle()
    {
        if(count(self::columnUpdateGetColumn()) > 0){

            $dataCount = self::columnUpdateQuery()->count();
            $perPage = 1000;
            $pageCount = ceil($dataCount / $perPage);

            for ($page = 0; $page < $pageCount; $page++) {
                $records = self::columnUpdateGetData($perPage);

                foreach ($records as $record) {
                    $record->{key(self::columnUpdateGetColumn())} = array_values(self::columnUpdateGetColumn())[0];
                    $record->save();
                }
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function columnUpdateQuery()
    {
        return parent::query()
            ->whereNull(key(self::columnUpdateGetColumn()));
    }

    /**
     * @param $perPage
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public static function columnUpdateGetData($perPage)
    {
        return self::columnUpdateQuery()
            ->take($perPage)
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @return string
     */
    private static function columnUpdateGetRandomDate()
    {
        $year = rand(\Carbon\Carbon::now()->subYear(24)->format("Y"), \Carbon\Carbon::now()->subYear(18)->format("Y"));
        $month = rand(1,12);
        $day = rand(1,28);

        return \Carbon\Carbon::parse($year."-".$month."-".$day)->format("Y-m-d");
    }

    /**
     * @return array
     */
    private static function columnUpdateGetColumn()
    {
        return self::columnUpdateSetColumn();
    }

    /**
     * @return array
     */
    public static function columnUpdateSetColumn()
    {
        return [];
    }
}
