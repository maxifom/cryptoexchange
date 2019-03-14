<?php

namespace App\Rules;

use Denpa\Bitcoin\Client;
use Denpa\Bitcoin\Exceptions\BitcoindException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Validation\Rule;
use App\Coin;

class ValidAddress implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $coin_name;

    public function __construct($coin_name)
    {
        $this->coin_name = $coin_name;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $coin = Cache::remember("Coin" . $this->coin_name, 60, function () {
                return Coin::where('name', $this->coin_name)->first();
            });
            $bitcoind = new Client('http://' . $coin->user . ':' . $coin->pass . '@localhost:' . $coin->port . '/');
            /*$rpcClient = new Client('http://localhost:' . $coin['port']);
            $rpcClient->getHttpClient()
                ->withUsername($coin['user'])
                ->withPassword($coin['pass']);
            */
            return $bitcoind->validateAddress($value)->get()['isvalid'];
        }
        catch (BitcoindException $e)
        {
            report($e);
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid address';
    }
}
