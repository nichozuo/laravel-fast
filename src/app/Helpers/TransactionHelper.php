<?php


namespace Nichozuo\LaravelFast\Helpers;


use Closure;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionHelper
{
    /**
     * @param Closure $closure
     * @return void
     */
    public static function Trans(Closure $closure): void
    {
        try {
            DB::beginTransaction();
            $closure();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
