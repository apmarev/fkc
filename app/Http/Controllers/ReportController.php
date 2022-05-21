<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AmoCrmController;

class ReportController extends Controller {

    protected $amo;

    public function __construct(AmoCrmController $amo) {
        $this->amo = $amo;
    }

    /**
     * Виджет «Анализ продаж»
     */
    public function salesAnalysis() {
        $pipeline = 3965530; // Клиенты в активной работе
        $statuses = $this->amo->getStatusesByPipeline($pipeline);

        $array = [];
        foreach($statuses as $status) {
            $array[$status['id']] = [
                'name' => $status['name'],
                'count' => 0,
                'price' => 0
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
        }

        $items = [];
        foreach($array as $a) {
            if($a['price'] > 0) $a['price'] = number_format($a['price'], 2, ',', ' ') . " ₽";
            $items[] = $a;
        }

        return $items;
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
                'price' => 0
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
                }
            }

        }

        $items = [];
        foreach($array as $a) {
            if($a['price'] > 0) $a['price'] = number_format($a['price'], 2, ',', ' ') . " ₽";
            $items[] = $a;
        }

        return $items;
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
                'price' => 0
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
                }
            }
        }

        $items = [];
        foreach($array as $a) {
            if($a['price'] > 0) $a['price'] = number_format($a['price'], 2, ',', ' ') . " ₽";
            $items[] = $a;
        }

        return $items;
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
                    'count' => $array[$resource]['count'] + 1
                ];
            } else {
                $array[$resource] = [
                    'price' => $price,
                    'count' => 1
                ];
            }

        }

        return $array;
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

        return $array;
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

        return $array;
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

        return $array;
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

        return $array;
    }

    public function test() {
        return $this->amo->getUsersByGroup();
    }
}
