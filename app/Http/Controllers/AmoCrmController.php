<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class AmoCrmController extends Controller {

    protected $__access;

    public function __construct(AccessController $__access) {
        $this->__access = $__access;
    }

    public static function getUsersGroups(): array {
        return [
            406465 => [ 'name' => 'Отдел Боровковой Тани', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
            406468 => [ 'name' => 'Отдел Кашкаровой Наташи', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
            406471 => [ 'name' => 'Отдел Губина Михаила', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
            406474 => [ 'name' => 'Отдел Есина Паши', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
            406477 => [ 'name' => 'Отдел Долговой Алины', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
            406480 => [ 'name' => 'Отдел Савиной Крис', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
            406483 => [ 'name' => 'Отдел Шпортало Анастасии', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
            406486 => [ 'name' => 'Отдел Тришина Михаила', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
            406489 => [ 'name' => 'Отдел Лосевой Юлии', 'count' => 0, 'price' => 0, 'budget' => 0, 'users' => [] ],
        ];
    }

    public function createLead($title, $pipelineID, $statusID, $custom) {
        return $this->amoPost('/leads', [
            [
                'name' => $title,
                'pipeline_id' => $pipelineID,
                'status_id' => $statusID,
                'custom_fields_values' => $custom,
            ]
        ]);
    }

    public static function getIsSetList($data, string $type) {
        return $data['_embedded'][$type] ?? [];
    }

    public static function getIsSetListCustomFields($data): array {
        if(isset($data['custom_fields_values']) && is_array($data['custom_fields_values']) && sizeof($data['custom_fields_values']) > 0) {
            return $data['custom_fields_values'];
        }
        return [];
    }

    public function getUsersByGroup($groupId = 0) {
        $allUsers = $this->amoGet('/users');
        $list = $this->getIsSetList($allUsers, 'users');

        if($groupId == 0) {
            $groups = self::getUsersGroups();

            foreach($list as $user) {
                if(isset($groups[$user['rights']['group_id']])) {
                    $groups[$user['rights']['group_id']]['users'][] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'group' => $user['rights']['group_id'],
                        'count' => 0,
                        'price' => 0,
                        'budget' => 0
                    ];
                }
            }
        } else {
            $groups = [];

            foreach($list as $user) {
                if($user['rights']['group_id'] == $groupId)
                    $groups[] = $user;
            }
        }

        return $groups;
    }

    public function getStatusesByPipeline($pipelineId) {
        $return = [];
        $list = $this->amoGet("/leads/pipelines/{$pipelineId}/statuses");
        $result = $this->getIsSetList($list, 'statuses');
        foreach($result as $res)
            if($res['is_editable']) $return[] = $res;

        return $return;
    }

    public function getAllListByFilter(string $type, string $filter) {
        $result = [];

        for($i=1;;$i++) {
            $query = "/{$type}?page={$i}&limit=250{$filter}";
            $res = $this->amoGet($query);
            $list = self::getIsSetList($res, $type);

            if(sizeof($list) > 0) {
                $result = array_merge($result, $list);
                unset($list);
            } else
                break;
        }

        return $result;
    }

    public function getNotesByFilter(string $filter) {
        $result = [];

        for($i=1;;$i++) {
            $query = "/leads/notes?page={$i}&limit=250{$filter}";
            $res = $this->amoGet($query);
            $list = self::getIsSetList($res, 'notes');
            if(sizeof($list) > 0) {
                $result = array_merge($result, $list);
                unset($list);
            } else
                break;
        }

        return $result;
    }

    public function getLeadByID($leadID) {
        $path = "/leads/{$leadID}?with=contacts,companies";
        return $this->amoGet($path);
    }

    public function getCompanyByID($companyID) {
        $path = "/companies/{$companyID}";
        return $this->amoGet($path);
    }

    /**
     * Добавление текстового примечания к сделке
     *
     * @param $leadID
     * @param $description
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response|\Illuminate\Http\JsonResponse
     */
    public function setDescriptionToLead($leadID, $description) {
        $path = "/leads/{$leadID}/notes";
        return $this->amoPost($path, [[
            'note_type' => 'common',
            'params' => [ 'text' => $description ]
        ]]);
    }

    /**
     * Добавление текстового примечания к компании
     *
     * @param $leadID
     * @param $description
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response|\Illuminate\Http\JsonResponse
     */
    public function setDescriptionToCompany($companyID, $description) {
        $path = "/companies/{$companyID}/notes";
        return $this->amoPost($path, [[
            'note_type' => 'common',
            'params' => [ 'text' => $description ]
        ]]);
    }

    /**
     * Связь контакта с лидом
     *
     * @param $contactID
     * @param $leadID
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response|\Illuminate\Http\JsonResponse
     */
    public function associateLeadWithContact($contactID, $leadID) {
        return $this->amoPost("/leads/{$leadID}/link", [[
            'to_entity_id' => $contactID,
            'to_entity_type' => 'contacts'
        ]]);
    }

    /**
     * Связь компании с лидом
     *
     * @param $companyID
     * @param $leadID
     * @return \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response|\Illuminate\Http\JsonResponse
     */
    public function associateLeadWithCompany($companyID, $leadID) {
        return $this->amoPost("/leads/{$leadID}/link", [[
            'to_entity_id' => $companyID,
            'to_entity_type' => 'companies'
        ]]);
    }

    public function amoGet($path) {
        try {
            $amo = $this->amoGetStatusAccess();
            $response = Http::withHeaders([
                "Authorization" => "Bearer {$amo->access}",
                "Content-Type" => "application/json",
            ])
                ->get("https://" . config('app.services.amo.subdomain') . ".amocrm.ru/api/v4{$path}")['_embedded'];

            return $response;
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoPost($path, $data) {
        try {
            $amo = $this->amoGetStatusAccess();
            return Http::withHeaders([
                "Authorization" => "Bearer {$amo['access']}",
                "Content-Type" => "application/json",
            ])->post("https://" . config('app.services.amo.subdomain') . ".amocrm.ru/api/v4{$path}", $data);
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoPut($path, $data) {
        try {
            $amo = $this->amoGetStatusAccess();
            return Http::withHeaders([
                "Authorization" => "Bearer {$amo['access']}",
                "Content-Type" => "application/json",
            ])->patch("https://" . config('app.services.amo.subdomain') . ".amocrm.ru/api/v4{$path}", $data);
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public static function setSession($name, $value) {
        $_SESSION[$name] = $value;
    }

    public static function getSession($name) {
        return $_SESSION[$name] ?? null;
    }

    public function amoNewAccess(Request $request) {
        if(
            !$request->input('key') ||
            !$request->input('description') ||
            !$request->input('secret') ||
            !$request->input('client')
        )
            return CustomApiException::error(400);

        try {
            $response = $this->amoPostNewAccessAndRefresh($request->input('key'), $request->input('client'), $request->input('secret'));
            self::setSession('expires', time() + $response['expires_in']);
            self::setSession('access', $response['access_token']);

//            return Storage::put('amo.txt', json_encode(
//                [
//                    'name'          => 'amo',
//                    'description'   => $request->input('description'),
//                    'secret'        => $request->input('secret'),
//                    'client'        => $request->input('client'),
//                    'access'        => $response['access_token'],
//                    'refresh'       => $response['refresh_token'],
//                    'expires'       => time() + $response['expires_in'],
//                ]
//            ));
            return $this->__access->create([
                'name'          => 'amo',
                'description'   => $request->input('description'),
                'secret'        => $request->input('secret'),
                'client'        => $request->input('client'),
                'access'        => $response['access_token'],
                'refresh'       => $response['refresh_token'],
                'expires'       => time() + $response['expires_in'],
            ]);
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoPostNewAccessAndRefresh($code, $client, $secret) {
        $link = 'https://' . config('app.services.amo.subdomain') . '.amocrm.ru/oauth2/access_token';

        try {
            return Http::post($link, [
                'client_id' => $client,
                'client_secret' => $secret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('app.services.amo.domain'),
            ]);
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function amoGetStatusAccess() {
        if($access = Access::find(1)) {
            if(time() >= $access['expires'])
                return $this->newAccessTokenByRefreshToken($access);
            else
                return $access;
        } else {
            return CustomApiException::error(404);
        }
//        try {
//            $access = $this->__access->getAccessByID(1);
//            if(time() >= $access->expires) {
//                return $this->newAccessTokenByRefreshToken($access);
//            } else {
//                return $access;
//            }
//        } catch (\Exception $e) {
//            return CustomApiException::error(404);
//        }
    }

    public function newAccessTokenByRefreshToken($service) {
        try {
            $result = Http::asForm()->post("https://" . config('app.services.amo.subdomain') . ".amocrm.ru/oauth2/access_token", [
                'client_id' => $service->client,
                'client_secret' => $service->secret,
                'grant_type' => 'refresh_token',
                'refresh_token' => $service->refresh,
                'redirect_uri' => config('app.services.amo.domain'),
            ]);
            if(isset($result['access_token'])) {
                try {
                    $access = $this->__access->getAccessByID($service->id);
                    $access->__set('access', $result['access_token']);
                    $access->__set('refresh', $result['refresh_token']);
                    $access->__set('expires', time() + $result['expires_in']);
                    $access->save();

//                    $access = json_decode(Storage::get('amo.txt'), true);
//
//                    $access['access'] = $result['access_token'];
//                    $access['refresh'] = $result['refresh_token'];
//                    $access['expires'] = $result['expires_in'];

//                    self::setSession('expires', $result['expires_in']);
//                    self::setSession('access', $result['access_token']);

                    // Storage::put('amo.txt', json_encode($access));

                    return $access;
                } catch (\Exception $e) {
                    return CustomApiException::error(500, $e->getMessage());
                }
            } else {
                return CustomApiException::error(500);
            }
        } catch (\Exception $e) {
            return CustomApiException::error(500, $e->getMessage());
        }
    }

    public function getOrCreateContact($name, $phone) {
        $search = $this->searchContactByPhone($phone);
        if($search > 0) return $search;
        else {
            $path = "/contacts";
            $result = $this->amoPost($path, [
                [
                    'name' => $name,
                    'custom_fields_values' => [
                        [
                            'field_id' => 741295,
                            'values' => [
                                [ 'value' => $phone ]
                            ],
                        ]
                    ]
                ]
            ]);
            $list = $this->getIsSetList($result, 'contacts');
            if($list[0]['id'] > 0) {
                return $list[0]['id'];
            } else {
                return 0;
            }
        }
    }

    public function searchContactByPhone($phone) {
        $path = "/contacts?query={$phone}";
        $result = $this->amoGet($path);
        $list = $this->getIsSetList($result, 'contacts');

        foreach($list as $el) {
            $custom = $this->getIsSetListCustomFields($el);
            foreach($custom as $c) {
                if($c['field_id'] == 741295) {
                    foreach($c['values'] as $value) {
                        if(strripos($value['value'], strval($phone))) {
                            return $el['id'];
                        }
                    }
                }
            }
        }

        return 0;
    }

    public function test() {
        return $this->amoGetStatusAccess();
    }
}
