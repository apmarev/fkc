<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AccessController extends Controller {

    public function getAccessByID($elementID) {
        return Access::find($elementID);
    }

    public function getAccessByName($name) {
        return Access::where('name', $name)->first();
    }

    public function create($data): Access {
        $element = new Access();

        foreach($data as $key => $value) {
            $element->__set($key, $value);
        }

        $element->save();

        return $element;
    }

    public function updateField($name, $data) {
        $service = $this->getAccessByName($name);
        $element = Access::find($service['id']);

        foreach($data as $key => $value) {
            $element->__set($key, $value);
        }

        $element->save();

        return $element;
    }

    public function test() {
        return Storage::put('amo.txt', json_encode(
            [
                'name'          => 'amo',
                'description'   => 'amo',
                'secret'        => '5P5vxX1nHp8Aw7N9WxHVAleabUzb9IqDKR5uqshDfY1V2mCyJOA9TVYg79MeJQr2',
                'client'        => 'ecad2465-b6e3-4267-8371-f4e0b8be5de9',
                'access'        => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjIyNGI5MmQzN2VmODEyMTk2NGE0NjQwMzlhMDUzNDk3MjdkNTA3MWI2M2ZhNjJiODc1NDg0OGFiOTMyZTM2ZGEwMzZhZTk0NTQwZTE0ZDBlIn0.eyJhdWQiOiJlY2FkMjQ2NS1iNmUzLTQyNjctODM3MS1mNGUwYjhiZTVkZTkiLCJqdGkiOiIyMjRiOTJkMzdlZjgxMjE5NjRhNDY0MDM5YTA1MzQ5NzI3ZDUwNzFiNjNmYTYyYjg3NTQ4NDhhYjkzMmUzNmRhMDM2YWU5NDU0MGUxNGQwZSIsImlhdCI6MTY1MzE0MjU2NSwibmJmIjoxNjUzMTQyNTY1LCJleHAiOjE2NTMyMjg5NjQsInN1YiI6IjY3MzA4NjQiLCJhY2NvdW50X2lkIjoyOTI4NDA0NSwic2NvcGVzIjpbInB1c2hfbm90aWZpY2F0aW9ucyIsImNybSIsIm5vdGlmaWNhdGlvbnMiXX0.GAIM5RthP1zXRo58aICdwqHxJA5NJHHyUIy-aXknFuuJHFgAO5ZIqiWu4dlCayfcqkY8MMOYR570h3dghg5913cqbK7FRHB6uBBtaLsBoP3TBYZDTNAsuZ5VYy-o3vI-ozzoDmy7uGdEalR935I7D7uSXArUJQ95sFEb38w1CJTlcN0IxBaLD4VMdqpvqY2sSdwoeCdfJVb3vvV0sv696nvPvuRZIuxPLoA9BtwWFN6f6ArBPG8MbvBKUqB8AyKIARY7yusMG_J07zsNghlBMznu4xI3zWKjkfB4VFOnjXT5060eXpgmKct1uc2kaCYEZ_xqhCO3XBdRtuOtFYsW3g',
                'refresh'       => 'def502004a75d83e06fd2b559e78fa873368b090b86ca72be70733b915de501bc829a4ca803ead49e159f8dfb360623145343dd10e9de82adfb35ec1422b299020c362364407bf6d1b162d9838f5738db8419c9f037da173e6bf92bc020f76719df75101e90409bec6d9830e5c533c5e8788e9e528ab6b3f939d0f07206ab3f4717f3c880e7e3660c1aea2053a41f984297b6732ab2a90a4c58a4d3469acc8f2c3cd416db7bebdb3baeefe7135f16f512d751462497a52a9c7f477a31d8f38415295b98f25d27337380fd1ef661a6f95559a3d406737f27f80b991bce61db0ca1a2eef7b1c5a450a1f3ec3da6fb787c1558bef91ca4977ce30784d48881b3f804d4380f5d701a4384c55d67375a946b8885bca55957f2a8c16c999c4cb145898362680cdadde6ccaace4f6b1652c9649dbc711f3951ab1927af78bb952086e78227111925b6a04080f136eb41ff89447726e440dd5b96eef2ffdebd7a6155c8f8c7dd8027f30f0296364160498610fbb381e3fe5a20bc86585d1db21f9847f7ccfae7aa86ccafcc71e5b56384e17d1ff3f21f95bbb3f289f6c7e7d24219503bc46203dfd4df4903467baa6d63e86d994241d2c2d956ef0fc09cc8173c24aec3001cd787bcea6fabd7869',
                'expires'       => 1653228964,
            ]
        ));
    }

}
