<?php


namespace Nichozuo\LaravelFast\Tests;


use Illuminate\Support\Str;

trait TestCaseTrait
{
    protected string $token = '';
    protected string $url = '';
    protected int $id;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param string $method
     * @param array $params
     * @param array $headers
     * @return void
     */
    protected function go(string $method, array $params = [], array $headers = []): void
    {
        $url = $this->getUrl($method);
        $headers['Authorization'] = 'Bearer ' . $this->token;
        $url = $this->url ? $this->url . $url : $url;
        $response = $this->post($url, $params, $headers);
        $json = $response->json();
        $this->assertTrue($response->getStatusCode() == 200);
//        $response->assertStatus($response->getStatusCode());
        dump(json_encode($json));
        dump($json);
    }

    /**
     * @param string $method
     * @return string
     */
    private function getUrl(string $method): string
    {
        $t1 = explode('\\', $method);
        $urls[] = 'api';
        foreach ($t1 as $key => $value) {
            if ($key > 1 && $key < count($t1) - 1) {
                $urls[] = Str::snake($value);
            }
        }
        $urls[] = str_replace(
            '::test_',
            '',
            Str::snake(
                str_replace(
                    'ControllerTest',
                    '/',
                    end($t1)
                )
            )
        );
        return '/' . implode('/', $urls);
    }
}