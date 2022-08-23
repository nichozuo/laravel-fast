<?php


namespace Nichozuo\LaravelFast\Http\Controllers;


use Nichozuo\LaravelFast\Exceptions\Err;

trait ControllerTrait
{
    /**
     * @return int
     * @throws Err
     */
    protected function perPage(): int
    {
        $params = request()->only('perPage');
        if (!isset($params['perPage']) || !is_numeric($params['perPage']))
            return 20;

        $allow = config('common.perPageAllow', [10, 20, 50, 100]);
        if (!in_array($params['perPage'], $allow))
            Err::NewText('分页数据不在规定范围内');

        return (int)$params['perPage'];
    }

    /**
     * @return string
     */
    protected function getMines(): string
    {
        $mime_image = 'gif,jpeg,png,ico,svg';
        $mine_docs = 'xls,xlsx,doc,docx,ppt,pptx,pdf';
        $mine_zip = '7z,zip,rar';
        return $mime_image . ',' . $mine_docs . ',' . $mine_zip;
    }

    /**
     * @param array $params
     * @param string $key
     * @return void
     */
    protected function crypto(array &$params, string $key = 'password'): void
    {
        if (isset($params[$key]))
            $params[$key] = bcrypt($params[$key]);
    }
}