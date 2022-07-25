<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AmoCrmController;
use Illuminate\Http\Request;

class ReportController extends Controller {

    protected $amo;

    public function ttt() {

        $query = "/leads?page=1";
        $res = $this->amo->amoGet($query);
        return $this->amo->getIsSetList([2134], 'leads');
    }

    public function __construct(AmoCrmController $amo) {
        $this->amo = $amo;
    }

    protected function getTasksToReports($pipeline_id, $from, $to) {
        $filter = "&filter[is_completed]=1&filter[entity_type]=leads&filter[updated_at][from]={$from}&filter[updated_at][to]={$to}";
        $tasks = $this->amo->getAllListByFilter('tasks', $filter);

        $filter = "&filter[pipeline_id]={$pipeline_id}";
        $t = 0;
        foreach($tasks as $l) {
            $filter .= "&filter[id][{$t}]={$l['entity_id']}";
            $t++;
        }

        $leads = $this->amo->getAllListByFilter('leads', "{$filter}");

        return [
            'leads' => $leads,
            'tasks' => $tasks
        ];
    }

    public function getAllReports(Request $request) {

        $date = [];

        if($request->has('period')) {
            $period = $request->input('period');
            if($period == 'custom') {
                $date = [
                    'from' => strtotime($request->input('date_from')),
                    'to' => strtotime($request->input('date_to'))
                ];
            } else {
                if($period == 'week') {
                    $date = [
                        'from' => strtotime(date("d.m.Y", strtotime('monday this week')) . "00.00.01"),
                        'to' => time(),
                    ];
                } else if($period == 'day') {
                    $date = [
                        'from' => strtotime(date('d.m.Y') . "00.00.01"),
                        'to' => time()
                    ];
                } else if($period == 'yesterday') {
                    $date = [
                        'from' => strtotime(date('d.m.Y', strtotime('yesterday')) . "00.00.01"),
                        'to' => strtotime(date('d.m.Y', strtotime('yesterday')) . "23.59.59")
                    ];
                } else if($period == 'month') {
                    $date = [
                        'from' => strtotime(date("d.m.Y", strtotime('first day of this month')) . "00.00.01"),
                        'to' => time(),
                        'test' => date("d.m.Y", strtotime('first day of this month'))
                    ];
                }
            }
//
//            Telegram::sendMessage([
//                'chat_id' => 228519769,
//                'text' => json_encode($date)
//            ]);

        }

//        $this->completedTasks();
//        $this->createdTasks();
//        $this->closedTasksByManagers();
//        $this->createdNotesForManagers();
        $data = $this->getLeadsFromPipelineActiveClients();

        // return $this->dealsByManager($data);

        return view('reports.report', [
            'salesAnalysis' => $this->salesAnalysis($data),
            'transactionSources' => $this->transactionSources($date),
            'dealsByManager' => $this->dealsByManager($data),
            'salesByManager' => $this->salesByManager($date),
        ]);
    }

    public function getTwoReports(Request $request) {

        $date = [];

        if($request->has('period')) {
            $period = $request->input('period');
            if($period == 'custom') {
                $date = [
                    'from' => strtotime($request->input('date_from')),
                    'to' => strtotime($request->input('date_to'))
                ];
            } else {
                if($period == 'week') {
                    $date = [
                        'from' => strtotime(date("d.m.Y", strtotime('monday this week')) . "00.00.01"),
                        'to' => time(),
                    ];
                } else if($period == 'day') {
                    $date = [
                        'from' => strtotime(date('d.m.Y') . "00.00.01"),
                        'to' => time()
                    ];
                } else if($period == 'yesterday') {
                    $date = [
                        'from' => strtotime(date('d.m.Y', strtotime('yesterday')) . "00.00.01"),
                        'to' => strtotime(date('d.m.Y', strtotime('yesterday')) . "23.59.59")
                    ];
                } else if($period == 'month') {
                    $date = [
                        'from' => strtotime(date("d.m.Y", strtotime('first day of this month')) . "00.00.01"),
                        'to' => time(),
                        'test' => date("d.m.Y", strtotime('first day of this month'))
                    ];
                }
            }
//
//            Telegram::sendMessage([
//                'chat_id' => 228519769,
//                'text' => json_encode($date)
//            ]);

        }

        $tasks = $this->getTasksToReports(3965530, $date['from'], $date['to']);
//        $tasks2 = $this->getTasksToReportsTwo(3966382, $date['from'], $date['to']);

        return $tasks;

        return view('reports.reportTwo', [
            'completedTasks' => $this->completedTasks($date, $tasks),
            'createdTasks' => $this->createdTasks($date),
            'closedTasksByManagers' => $this->closedTasksByManagers($date, $tasks2),
            'createdNotesForManagers' => $this->createdNotesForManagers($date),
        ]);
    }

    protected function getLeadsFromPipelineActiveClients() {
        $pipeline = 3965530; // Клиенты в активной работе
        $statuses = $this->amo->getStatusesByPipeline($pipeline);
        $statuses = array_reverse($statuses);

        $filter = "&filter[pipeline_id]={$pipeline}";
        for($i=0;$i<sizeof($statuses);$i++) $filter .= "&filter[statuses][{$i}][pipeline_id]={$pipeline}&filter[statuses][{$i}][status_id]={$statuses[$i]['id']}";

        return [
            'statuses' => $statuses,
            'leads' => $this->amo->getAllListByFilter('leads', $filter)
        ];
    }

    /**
     * Виджет «Анализ продаж»
     */
    public function salesAnalysis($data) {

        $leads = $data['leads'];
        $statuses = $data['statuses'];

        $array = [];
        foreach($statuses as $status) {
            $array[$status['id']] = [
                'name' => mb_strtoupper($status['name']),
                'count' => 0,
                'price' => 0,
                'budget' => 0
            ];
        }

        foreach($leads as $lead) {
            $custom = $this->amo->getIsSetListCustomFields($lead);

            $count = 0;
            $price = 0;

            foreach($custom as $c)
                if($c['field_id'] == 915455) $price = $c['values'][0]['value'];

            if( isset($array[$lead['status_id']]) ) {
                $price = $price + $array[$lead['status_id']]['price'];
                $count = $array[$lead['status_id']]['count'] + 1;
            }

            $array[$lead['status_id']]['count'] = $count;
            $array[$lead['status_id']]['price'] = $price;
            $array[$lead['status_id']]['budget'] = $array[$lead['status_id']]['budget'] + $lead['price'];
        }

        $size = [
            'count' => 0,
            'price' => 0,
            'budget' => 0
        ];

        $items = [];
        foreach($array as $a) {
            $size = [
                'count' => $size['count'] + $a['count'],
                'price' => $size['price'] + $a['price'],
                'budget' => $size['budget'] + $a['budget']
            ];
            if($a['price'] > 0) $a['price'] = number_format($a['price'], 2, ',', ' ') . " ₽";
            if($a['budget'] > 0) $a['budget'] = number_format($a['budget'], 2, ',', ' ') . " ₽";
            $items[] = $a;
        }

        $size['price'] = number_format($size['price'], 2, ',', ' ') . " ₽";
        $size['budget'] = number_format($size['budget'], 2, ',', ' ') . " ₽";

        return [
            'size' => $size,
            'items' => $items
        ];
    }

    /**
     * Виджет «Сделки по менеджерам»
     */
    public function dealsByManager($data) {
        $leads = $data['leads'];
        $managers = $this->amo->getUsersByGroup();

        foreach($managers as $k => $v) {

            $i = 0;
            foreach($v['users'] as $user) {
                foreach($leads as $lead) {
                    if($lead['responsible_user_id'] == $user['id']) {
                        $custom = $this->amo->getIsSetListCustomFields($lead);

                        $count = 0;
                        $price = 0;

                        foreach($custom as $c)
                            if($c['field_id'] == 915455) $price = $c['values'][0]['value'];


                        $price = $price + $v['users'][$i]['price'];
                        $count = $v['users'][$i]['count'] + 1;

                        $v['users'][$i]['count'] = $count;
                        $v['users'][$i]['price'] = $price;
                        $v['users'][$i]['budget'] = $v['users'][$i]['budget'] + $lead['price'];
                    }
                }
                $i++;
            }

            $managers[$k] = $v;

        }

        $size = [
            'count' => 0,
            'price' => 0,
            'budget' => 0
        ];

        foreach($managers as $k => $v) {
            foreach($v['users'] as $user) {
                $size = [
                    'count' => $size['count'] + $user['count'],
                    'price' => $size['price'] + $user['price'],
                    'budget' => $size['budget'] + $user['budget']
                ];

                $v['count'] = $v['count'] + $user['count'];
                $v['price'] = $v['price'] + $user['price'];
                $v['budget'] = $v['budget'] + $user['budget'];

                if($user['price'] > 0) $user['price'] = number_format($user['price'], 2, ',', ' ') . " ₽";
                if($user['budget'] > 0) $user['budget'] = number_format($user['budget'], 2, ',', ' ') . " ₽";
            }

            $managers[$k] = $v;
        }

        $size['price'] = number_format($size['price'], 2, ',', ' ') . " ₽";
        $size['budget'] = number_format($size['budget'], 2, ',', ' ') . " ₽";

        return [
            'size' => $size,
            'items' => $managers
        ];
    }

    /**
     * Виджет «Продажи по менеджерам»
     */
    public function salesByManager($date) {
        $pipeline = 3965530; // Клиенты в активной работе

        $array = [];
        $managers = $this->amo->getUsersByGroup();

        $filter = "&filter[statuses][0][pipeline_id]={$pipeline}&filter[statuses][0][status_id]=142&filter[closed_at][from]={$date['from']}&filter[closed_at][to]={$date['to']}";
        $leads = $this->amo->getAllListByFilter('leads', $filter);

        foreach($managers as $k => $v) {
            $i = 0;
            foreach($v['users'] as $user) {
                foreach ($leads as $lead) {
                    if ($lead['responsible_user_id'] == $user['id']) {
                        $custom = $this->amo->getIsSetListCustomFields($lead);

                        $count = 0;
                        $price = 0;

                        foreach ($custom as $c)
                            if ($c['field_id'] == 915455) $price = $c['values'][0]['value'];


                        $price = $price + $v['users'][$i]['price'];
                        $count = $v['users'][$i]['count'] + 1;


                        $v['users'][$i]['count'] = $count;
                        $v['users'][$i]['price'] = $price;
                        $v['users'][$i]['budget'] = $v['users'][$i]['budget'] + $lead['price'];
                    }
                }
                $i++;
            }

            $managers[$k] = $v;
        }

        $size = [
            'count' => 0,
            'price' => 0,
            'budget' => 0
        ];

        foreach($managers as $k => $v) {
            foreach($v['users'] as $user) {
                $size = [
                    'count' => $size['count'] + $user['count'],
                    'price' => $size['price'] + $user['price'],
                    'budget' => $size['budget'] + $user['budget']
                ];

                $v['count'] = $v['count'] + $user['count'];
                $v['price'] = $v['price'] + $user['price'];
                $v['budget'] = $v['budget'] + $user['budget'];

                if($user['price'] > 0) $user['price'] = number_format($user['price'], 2, ',', ' ') . " ₽";
                if($user['budget'] > 0) $user['budget'] = number_format($user['budget'], 2, ',', ' ') . " ₽";
            }

            $managers[$k] = $v;
        }

        $size['price'] = number_format($size['price'], 2, ',', ' ') . " ₽";
        $size['budget'] = number_format($size['budget'], 2, ',', ' ') . " ₽";

        return [
            'size' => $size,
            'items' => $managers
        ];
    }

    /**
     * Виджет «Источники сделок»
     */
    public function transactionSources($date) {
        $pipeline = 3965530; // Клиенты в активной работе

        $filter = "&filter[pipeline_id]={$pipeline}&filter[created_at][from]={$date['from']}&filter[created_at][to]={$date['to']}";
        $leads = $this->amo->getAllListByFilter('leads', $filter);

        $array = [];

        foreach($leads as $lead) {
            $custom = $this->amo->getIsSetListCustomFields($lead);
            $resource = "Источник не указан";
            $price = 0;
            foreach($custom as $c) {
                if($c['field_id'] == 915455) $price = $c['values'][0]['value'];
                if($c['field_id'] == 221387) $resource = $c['values'][0]['value'];
            }

            if(isset($array[$resource])) {
                $array[$resource] = [
                    'price' => $array[$resource]['price'] + $price,
                    'count' => $array[$resource]['count'] + 1,
                    'budget' => $array[$resource]['budget'] + $lead['price'],
                ];
            } else {
                $array[$resource] = [
                    'price' => $price,
                    'count' => 1,
                    'budget' => $lead['price']
                ];
            }

        }

        $size = [
            'count' => 0,
            'price' => 0,
            'budget' => 0
        ];

        $items = [];
        foreach($array as $k => $a) {
            $size = [
                'count' => $size['count'] + $a['count'],
                'price' => $size['price'] + $a['price'],
                'budget' => $size['budget'] + $a['budget']
            ];
            if($a['price'] > 0) $a['price'] = number_format($a['price'], 2, ',', ' ') . " ₽";
            if($a['budget'] > 0) $a['budget'] = number_format($a['budget'], 2, ',', ' ') . " ₽";
            $items[$k] = $a;
        }

        $size['price'] = number_format($size['price'], 2, ',', ' ') . " ₽";
        $size['budget'] = number_format($size['budget'], 2, ',', ' ') . " ₽";

        return [
            'size' => $size,
            'items' => $items
        ];
    }

    /**
     * Виджет «Выполненные задачи»
     */
    public function completedTasks($date, $data) {
        // $pipeline = 3965530; // Клиенты в активной работе

        $array = [];
        $managers = $this->amo->getUsersByGroup(395710);
        foreach($managers as $manager) {
            $array[$manager['id']] = [
                'id' => $manager['id'],
                'name' => $manager['name'],
                'count' => 0,
                'price' => 0
            ];
        }

        // $leadsByPipeline = $this->amo->getAllListByFilter('leads', "&filter[pipeline_id]={$pipeline}");

//        $filter = "&filter[is_completed]=1&filter[entity_type]=leads&filter[updated_at][from]={$date['from']}&filter[updated_at][to]={$date['to']}";
//        $leads = $this->amo->getAllListByFilter('tasks', $filter);
//
//        $filter = "&filter[pipeline_id]={$pipeline}";
//        $t = 0;
//        foreach($leads as $l) {
//            $filter .= "&filter[id][{$t}]={$l['entity_id']}";
//            $t++;
//        }
//
//        $leadsByPipeline = $this->amo->getAllListByFilter('leads', "{$filter}");

        foreach($array as $k => $v) {
            foreach($data['tasks'] as $lead) {
                if($lead['responsible_user_id'] == $k && array_search($lead['entity_id'], array_column($data['leads'], 'id')) > -1) {

                    $count = 0;

                    if( isset($array[$k]) ) {
                        $count = $array[$k]['count'] + 1;
                    }

                    $array[$k]['count'] = $count;
                }
            }
        }

        $size = [
            'count' => 0
        ];

        foreach($array as $k => $v) {
            $size['count'] = $size['count'] + $v['count'];
        }

        return [
            'size' => $size,
            'items' => $array
        ];
    }

    /**
     * Виджет «Созданные задачи»
     */
    public function createdTasks($date) {
        $pipeline = 3965530; // Клиенты в активной работе

        $array = [];
        $managers = $this->amo->getUsersByGroup(395710);
        foreach($managers as $manager) {
            $array[$manager['id']] = [
                'id' => $manager['id'],
                'name' => $manager['name'],
                'count' => 0,
                'price' => 0
            ];
        }

        $leadsByPipeline = $this->amo->getAllListByFilter('leads', "&filter[pipeline_id]={$pipeline}");

        $filter = "&filter[entity_type]=leads&filter[created_at][from]={$date['from']}&filter[created_at][to]={$date['to']}";
        $leads = $this->amo->getAllListByFilter('tasks', $filter);


        foreach($array as $k => $v) {
            foreach($leads as $lead) {
                if($lead['responsible_user_id'] == $k && array_search($lead['entity_id'], array_column($leadsByPipeline, 'id')) > -1) {

                    $count = 0;

                    if( isset($array[$k]) ) {
                        $count = $array[$k]['count'] + 1;
                    }

                    $array[$k]['count'] = $count;
                }
            }
        }

        $size = [
            'count' => 0
        ];

        foreach($array as $k => $v) {
            $size['count'] = $size['count'] + $v['count'];
        }

        return [
            'size' => $size,
            'items' => $array
        ];
    }

    /**
     * Виджет «Закрыто задач по менеджерам»
     */
    public function closedTasksByManagers($date, $data) {
        $pipeline = 3966382; // Клиенты без активных сделок

        $array = [];
        $managers = $this->amo->getUsersByGroup();

        // $leadsByPipeline = $this->amo->getAllListByFilter('leads', "&filter[pipeline_id]={$pipeline}");

//        $filter = "&filter[is_completed]=1&filter[entity_type]=leads&filter[updated_at][from]={$date['from']}&filter[updated_at][to]={$date['to']}";
//        $leads = $this->amo->getAllListByFilter('tasks', $filter);
//
//        $filter = "&filter[pipeline_id]={$pipeline}";
//        $t = 0;
//        foreach($leads as $l) {
//            $filter .= "&filter[id][{$t}]={$l['entity_id']}";
//            $t++;
//        }
//
//        $leadsByPipeline = $this->amo->getAllListByFilter('leads', "{$filter}");

        foreach($managers as $k => $v) {
            $i = 0;
            foreach($v['users'] as $user) {
                foreach ($data['tasks'] as $lead) {
                    if ($lead['responsible_user_id'] == $user['id'] && array_search($lead['entity_id'], array_column($data['leads'], 'id')) > -1) {

                        $count = 0;

                        $count = $v['users'][$i]['count'] + 1;

                        $v['users'][$i]['count'] = $count;
                    }
                }
                $i++;
            }

            $managers[$k] = $v;
        }

        $size = [
            'count' => 0
        ];

        foreach($managers as $k => $v) {
            foreach($v['users'] as $user) {
                $size['count'] = $size['count'] + $user['count'];

                $v['count'] = $v['count'] + $user['count'];
                $v['price'] = $v['price'] + $user['price'];
            }

            $managers[$k] = $v;
        }

        return [
            'size' => $size,
            'items' => $managers
        ];
    }

    /**
     * Виджет «Создано примечаний по менеджерам»
     */
    public function createdNotesForManagers($date) {
        $pipeline = 3966382; // Клиенты без активных сделок

        $managers = $this->amo->getUsersByGroup();

        $filter = "&filter[entity_type]=leads&filter[note_type]=common&filter[updated_at][from]={$date['from']}&filter[updated_at][to]={$date['to']}";
        $leads = $this->amo->getNotesByFilter($filter);

        $filter = "&filter[pipeline_id]={$pipeline}";
        $t = 0;
        foreach($leads as $l) {
            $filter .= "&filter[id][{$t}]={$l['entity_id']}";
            $t++;
        }

        $leadsByPipeline = $this->amo->getAllListByFilter('leads', "{$filter}");

        foreach($managers as $k => $v) {
            $i = 0;
            foreach($v['users'] as $user) {
                foreach ($leads as $lead) {
                    $key = array_search($lead['entity_id'], array_column($leadsByPipeline, 'id'));

                    if ($key && $key >= 0 && $lead['responsible_user_id'] == $user['id']) {
                        $count = 0;
                        $count = $v['users'][$i]['count'] + 1;
                        $v['users'][$i]['count'] = $count;
                    }
                }
                $i++;
            }

            $managers[$k] = $v;
        }

        $size = [
            'count' => 0
        ];

        foreach($managers as $k => $v) {
            foreach($v['users'] as $user) {
                $size['count'] = $size['count'] + $user['count'];

                $v['count'] = $v['count'] + $user['count'];
                $v['price'] = $v['price'] + $user['price'];
            }

            $managers[$k] = $v;
        }

        return [
            'size' => $size,
            'items' => $managers
        ];
    }

    public function test2() {
        return $_SESSION['access'] . ' ' . $_SESSION['expires'];
    }

    public function test() {
        $_SESSION['expires'] = 12332345;
        $_SESSION['access'] = '12342435363457456757';
        return $this->test2();
    }
}
