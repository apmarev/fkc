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
        return $this->$type($date);
    }

    /**
     * Виджет «Анализ продаж»
     */
    public function salesAnalysis($date) {
        $result = $this->reports->salesAnalysis();
        return view('reports.salesAnalysis', [
            'items' => $result['items'],
            'size' => $result['size']
        ]);
    }

    /**
     * Виджет «Сделки по менеджерам»
     */
    public function dealsByManager($date) {
        $result = $this->reports->dealsByManager();
        return view('reports.dealsByManager', [
            'items' => $result['items'],
            'size' => $result['size']
        ]);
    }

    /**
     * Виджет «Продажи по менеджерам»
     */
    public function salesByManager($date) {
        return view('reports.salesByManager', [ 'items' => $this->reports->salesByManager($date) ]);
    }

    /**
     * Виджет «Источники сделок»
     */
    public function transactionSources($date) {
        return view('reports.transactionSources', [ 'items' => $this->reports->transactionSources($date) ]);
    }

    /**
     * Виджет «Выполненные задачи»
     */
    public function completedTasks($date) {
        return view('reports.completedTasks', [ 'items' => $this->reports->completedTasks($date) ]);
    }

    /**
     * Виджет «Созданные задачи»
     */
    public function createdTasks($date) {
        return view('reports.createdTasks', [ 'items' => $this->reports->createdTasks($date) ]);
    }

    /**
     * Виджет «Закрыто задач по менеджерам»
     */
    public function closedTasksByManagers($date) {
        return view('reports.closedTasksByManagers', [ 'items' => $this->reports->closedTasksByManagers($date) ]);
    }

    /**
     * Виджет «Создано примечаний по менеджерам»
     */
    public function createdNotesForManagers($date) {
        return view('reports.createdNotesForManagers', [ 'items' => $this->reports->createdNotesForManagers($date) ]);
    }
}
