<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AccessController extends Controller {

    public function getAccessByID($elementID) {
        return Access::findOrFail($elementID);
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

}
