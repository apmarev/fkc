<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;

class WebController extends Controller {

    protected ReportController $reports;

    public function __construct(ReportController $reports) {
        $this->reports = $reports;
    }

    public function getFunction($type) {
        return $this->$type();
    }

    /**
     * Виджет «Анализ продаж»
     */
    public function salesAnalysis() {
        return view('reports.salesAnalysis', [ 'items' => $this->reports->salesAnalysis() ]);
    }

    /**
     * Виджет «Сделки по менеджерам»
     */
    public function dealsByManager() {
        return view('reports.dealsByManager', [ 'items' => $this->reports->dealsByManager() ]);
    }

    /**
     * Виджет «Продажи по менеджерам»
     */
    public function salesByManager() {

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
