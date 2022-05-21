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
        $custom = '';
        $period = '';
        if($request->has('period')) {
            $period = $request->input('period');
            if($period == 'custom') {
                Telegram::sendMessage([
                    'chat_id' => 228519769,
                    'text' => json_encode($request->all())
                ]);
            }

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
