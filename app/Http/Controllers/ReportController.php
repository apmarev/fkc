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
    public function salesByManager() {
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

        $filter = "&filter[statuses][0][pipeline_id]={$pipeline}&filter[statuses][0][status_id]=142";
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
    public function transactionSources() {

    }

    /**
     * Виджет «Выполненные задачи»
     */
    public function completedTasks() {

    }

    /**
     * Виджет «Созданные задачи»
     */
    public function createdTasks() {

    }

    /**
     * Виджет «Закрыто задач по менеджерам»
     */
    public function closedTasksByManagers() {

    }

    /**
     * Виджет «Создано примечаний по менеджерам»
     */
    public function createdNotesForManagers() {

    }

}