<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AmoCrmController;

class ReportController extends Controller {

    protected $amo;

    public function __construct(AmoCrmController $amo) {
        $this->amo = $amo;
    }

    public function getAllReports() {

        $date = [
            'from' => strtotime(date('d.m.Y', strtotime('yesterday')) . "00.00.01"),
            'to' => strtotime(date('d.m.Y', strtotime('yesterday')) . "23.59.59")
        ];

//        $this->completedTasks();
//        $this->createdTasks();
//        $this->closedTasksByManagers();
//        $this->createdNotesForManagers();

        return view('reports.report', [
            'salesAnalysis' => $this->salesAnalysis(),
            'transactionSources' => $this->transactionSources($date),
            'dealsByManager' => $this->dealsByManager(),
            'salesByManager' => $this->salesByManager($date),
        ]);
    }

    public function getTwoReports() {

        $date = [
            'from' => strtotime(date('d.m.Y', strtotime('yesterday')) . "00.00.01"),
            'to' => strtotime(date('d.m.Y', strtotime('yesterday')) . "23.59.59")
        ];


        return view('reports.reportTwo', [
            'completedTasks' => $this->completedTasks($date),
            'createdTasks' => $this->createdTasks($date),
            'closedTasksByManagers' => $this->closedTasksByManagers($date),
            'createdNotesForManagers' => $this->createdNotesForManagers($date),
        ]);
    }

    /**
     * Виджет «Анализ продаж»
     */
    public function salesAnalysis() {
        $pipeline = 3965530; // Клиенты в активной работе
        $statuses = $this->amo->getStatusesByPipeline($pipeline);

        $statuses = array_reverse($statuses);

        $array = [];
        foreach($statuses as $status) {
            $array[$status['id']] = [
                'name' => mb_strtoupper($status['name']),
                'count' => 0,
                'price' => 0,
                'budget' => 0
            ];
        }

        $filter = "&filter[pipeline_id]={$pipeline}";
        for($i=0;$i<sizeof($statuses);$i++) $filter .= "&filter[statuses][{$i}][pipeline_id]={$pipeline}&filter[statuses][{$i}][status_id]={$statuses[$i]['id']}";
        $leads = $this->amo->getAllListByFilter('leads', $filter);

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
            $array[$lead['status_id']]['budget'] = $lead['price'];
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
    public function dealsByManager() {
        $pipeline = 3965530; // Клиенты в активной работе
        $statuses = $this->amo->getStatusesByPipeline($pipeline);

        $array = [];
        $managers = $this->amo->getUsersByGroup();
        foreach($managers as $manager) {
            $array[$manager['id']] = [
                'id' => $manager['id'],
                'name' => $manager['name'],
                'count' => 0,
                'price' => 0,
                'budget' => 0
            ];
        }

        $filter = "&filter[pipeline_id]={$pipeline}";
        for($i=0;$i<sizeof($statuses);$i++) $filter .= "&filter[statuses][{$i}][pipeline_id]={$pipeline}&filter[statuses][{$i}][status_id]={$statuses[$i]['id']}";
        $leads = $this->amo->getAllListByFilter('leads', $filter);

        foreach($array as $k => $v) {

            foreach($leads as $lead) {
                if($lead['responsible_user_id'] == $k) {
                    $custom = $this->amo->getIsSetListCustomFields($lead);

                    $count = 0;
                    $price = 0;

                    foreach($custom as $c)
                        if($c['field_id'] == 915455) $price = $c['values'][0]['value'];

                    if( isset($array[$k]) ) {
                        $price = $price + $array[$k]['price'];
                        $count = $array[$k]['count'] + 1;
                    }

                    $array[$k]['count'] = $count;
                    $array[$k]['price'] = $price;
                    $array[$k]['budget'] = $lead['price'];
                }
            }

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
     * Виджет «Продажи по менеджерам»
     */
    public function salesByManager($date) {
        $pipeline = 3965530; // Клиенты в активной работе

        $array = [];
        $managers = $this->amo->getUsersByGroup();
        foreach($managers as $manager) {
            $array[$manager['id']] = [
                'id' => $manager['id'],
                'name' => $manager['name'],
                'count' => 0,
                'price' => 0,
                'budget' => 0
            ];
        }

        $filter = "&filter[statuses][0][pipeline_id]={$pipeline}&filter[statuses][0][status_id]=142&filter[closed_at][from]={$date['from']}&filter[closed_at][to]={$date['to']}";
        $leads = $this->amo->getAllListByFilter('leads', $filter);

        foreach($array as $k => $v) {
            foreach($leads as $lead) {
                if($lead['responsible_user_id'] == $k) {
                    $custom = $this->amo->getIsSetListCustomFields($lead);

                    $count = 0;
                    $price = 0;

                    foreach($custom as $c)
                        if($c['field_id'] == 915455) $price = $c['values'][0]['value'];

                    if( isset($array[$k]) ) {
                        $price = $price + $array[$k]['price'];
                        $count = $array[$k]['count'] + 1;
                    }

                    $array[$k]['count'] = $count;
                    $array[$k]['price'] = $price;
                    $array[$k]['budget'] = $lead['price'];
                }
            }
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
    public function completedTasks($date) {
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

        $filter = "&filter[is_completed]=1&filter[entity_type]=leads&filter[updated_at][from]={$date['from']}&filter[updated_at][to]={$date['to']}";
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
    public function closedTasksByManagers($date) {
        $pipeline = 3966382; // Клиенты без активных сделок

        $array = [];
        $managers = $this->amo->getUsersByGroup();
        foreach($managers as $manager) {
            $array[$manager['id']] = [
                'id' => $manager['id'],
                'name' => $manager['name'],
                'count' => 0,
                'price' => 0
            ];
        }

        $leadsByPipeline = $this->amo->getAllListByFilter('leads', "&filter[pipeline_id]={$pipeline}");

        $filter = "&filter[is_completed]=1&filter[entity_type]=leads&filter[updated_at][from]={$date['from']}&filter[updated_at][to]={$date['to']}";
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
     * Виджет «Создано примечаний по менеджерам»
     */
    public function createdNotesForManagers($date) {
        $pipeline = 3965530; // Клиенты в активной работе
        $pipelineTwo = 3966385; // Клиенты на юридическое сопровождение

        $array = [];
        $managers = $this->amo->getUsersByGroup();
        foreach($managers as $manager) {
            $array[$manager['id']] = [
                'id' => $manager['id'],
                'name' => $manager['name'],
                'count' => 0,
                'price' => 0
            ];
        }

        $leadsByPipelineOne = $this->amo->getAllListByFilter('leads', "&filter[pipeline_id]={$pipeline}");
        $leadsByPipelineTwo = $this->amo->getAllListByFilter('leads', "&filter[pipeline_id]={$pipelineTwo}");

        $leadsByPipeline = array_merge($leadsByPipelineOne, $leadsByPipelineTwo);

        $filter = "&filter[entity_type]=leads&filter[note_type]=common&filter[updated_at][from]={$date['from']}&filter[updated_at][to]={$date['to']}";
        $leads = $this->amo->getNotesByFilter($filter);

        foreach($array as $k => $v) {
            foreach($leads as $lead) {
                $key = array_search($lead['entity_id'], array_column($leadsByPipeline, 'id'));

                if(!$key && $lead['responsible_user_id'] == $k) {
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

    public function test2() {
        return $_SESSION['access'] . ' ' . $_SESSION['expires'];
    }

    public function test() {
        $_SESSION['expires'] = 12332345;
        $_SESSION['access'] = '12342435363457456757';
        return $this->test2();
    }
}
