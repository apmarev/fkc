<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller {

    public function test1() {

        $controller = new AmoCrmController(new AccessController());
        $file = $this->parseCsv('sk');
        $ids = [];
        foreach($file as $id) {
            if(isset($id[0]))
                $ids[] = preg_replace("/[^,.0-9]/", '', $id[0]);
        }

        $leadsIds = array_chunk($ids, 50);

        unset($ids);

        foreach($leadsIds as $ids) {
            $leads = $controller->getLeadsByIds($ids);

            $edit = [];

            foreach($leads as $lead) {
                $old = -1;
                if(isset($lead['custom_fields_values']) && is_array($lead['custom_fields_values'])) {
                    foreach($lead['custom_fields_values'] as $custom) {
                        if($custom['field_id'] == 972341) {
                            $old = $custom['values'][0]['value'];
                            break;
                        }
                    }
                    if($old >= 0) {
                        $edit[] = [
                            'id' => $lead['id'],
                            'custom_fields_values' => [
                                [
                                    'field_id' => 974375,
                                    'values' => [ [ 'value' => $old ] ]
                                ]
                            ]
                        ];
                    }
                }
            }

            $controller->amoPut('/leads', $edit);
        }

    }

    public function test2() {

    }

    protected function parseCsv($filename) {
        $line_of_text = [];
        $file_handle = fopen(__DIR__ . "/{$filename}.csv", 'r');

        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, 0, ';');
        }
        fclose($file_handle);
        return $line_of_text;
    }

}
