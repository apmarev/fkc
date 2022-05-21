<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebController extends Controller {

    protected ReportController $reports;

    public function __construct(ReportController $reports) {
        $this->reports = $reports;
    }

    public function getFunction(Request $request, $type) {
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
                        'test' => date("d.m.Y", strtotime('monday this week')),
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

                }
            }

            Telegram::sendMessage([
                'chat_id' => 228519769,
                'text' => json_encode($date)
            ]);

        }
        return $this->$type($period, $custom);
    }

    /**
     * Виджет «Анализ продаж»
     */
    public function salesAnalysis($period = '', $custom = '') {
        return view('reports.salesAnalysis', [ 'items' => $this->reports->salesAnalysis() ]);
    }

    /**
     * Виджет «Сделки по менеджерам»
     */
    public function dealsByManager($period = '', $custom = '') {
        return view('reports.dealsByManager', [ 'items' => $this->reports->dealsByManager() ]);
    }

    /**
     * Виджет «Продажи по менеджерам»
     */
    public function salesByManager($period, $custom = '') {

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
